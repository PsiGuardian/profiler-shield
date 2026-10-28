<?php
namespace OmegaGuardian\ProfilerShield\Storage;

use OmegaGuardian\ProfilerShield\Util\Env;

/**
 * Coffre local autonome, sans Doctrine. Format chiffré: AES-256-GCM, AAD=nom de
 * collection, clé par collection dérivée de GUARDIAN_ENCRYPTION_KEY.
 *
 * INVARIANTS:
 * - aucune donnée sensible en clair sur disque (hors sauvegardes externes);
 * - jamais de valeur par défaut si une collection présente est corrompue;
 * - écriture atomique sous verrou exclusif, fichiers 0600 et répertoire 0700;
 * - aucune initialisation sur les requêtes métier ordinaires;
 *
 * ATTENTION: le chiffrement au repos protège les copies accidentelles mais
 * pas un serveur compromis ayant également accès à la clé d'environnement.
 * Stockage mono-noeud: NFS et stockage multi-noeuds non pris en charge.
 */
final class FileStore
{
    private $root;
    private $keyOverride;

    public function __construct($root)
    {
        $custom = Env::get('GUARDIAN_STORAGE_DIR');
        $this->root = rtrim($custom !== '' ? $custom : $root, '/');
        $this->keyOverride = null;
    }

    public function root() { return $this->root; }
    public function ready() { return is_dir($this->root) && is_writable($this->root); }

    /** Le setup a créé .env.local mais Symfony ne l'a pas encore rechargé. */
    public function configureKey($hex)
    {
        if (!is_string($hex) || !preg_match('/^[0-9a-f]{64}$/Di', $hex)) {
            throw new \InvalidArgumentException('Clé Guardian invalide.');
        }
        $this->keyOverride = $hex;
    }

    private function key($name)
    {
        $hex = $this->keyOverride !== null ? $this->keyOverride : Env::get('GUARDIAN_ENCRYPTION_KEY');
        if (!preg_match('/^[0-9a-f]{64}$/Di', $hex)) {
            throw new \RuntimeException('Clé de stockage Guardian absente ou invalide : accès refusé.');
        }
        return hash_hmac('sha256', 'guardian-v2-storage:'.$name, hex2bin($hex), true);
    }

    public function initialize()
    {
        if (is_link($this->root)) { throw new \RuntimeException('Répertoire de stockage symbolique interdit.'); }
        if (!is_dir($this->root) && !@mkdir($this->root, 0700, true) && !is_dir($this->root)) {
            throw new \RuntimeException('Impossible de créer le stockage Guardian.');
        }
        @chmod($this->root, 0700);
        if (!is_writable($this->root)) { throw new \RuntimeException('Stockage Guardian non accessible en écriture.'); }
        // Défense additionnelle Apache si le document root est mal configuré.
        // Ne dispense PAS de DocumentRoot=public ni d'un contrôle Nginx.
        $htaccess=$this->root.'/.htaccess';
        if (!is_file($htaccess) && !is_link($htaccess)) {
            @file_put_contents($htaccess,"# Guardian encrypted store - never expose by HTTP\nRequire all denied\n",LOCK_EX);
            @chmod($htaccess,0600);
        }
    }

    private function path($name)
    {
        if (!is_string($name) || !preg_match('/^[a-z][a-z0-9_]{0,48}$/D', $name)) {
            throw new \InvalidArgumentException('Nom de collection invalide.');
        }
        return $this->root.'/'.$name.'.json';
    }

    /** Uniquement des collections chiffrées authentifiées en lecture HTTP. */
    public function read($name, $default = array())
    {
        $path = $this->path($name);
        if (!is_file($path)) { return $default; }
        if (is_link($path)) { throw new \RuntimeException('Lien symbolique dans le stockage refusé.'); }
        $json = @file_get_contents($path);
        if ($json === false) { throw new \RuntimeException('Lecture Guardian impossible.'); }
        $envelope = json_decode($json, true);
        if (!is_array($envelope) || !isset($envelope['schema']) || $envelope['schema'] !== 2) {
            throw new \RuntimeException('Format de stockage obsolète ou altéré : exécuter guardian:setup.');
        }
        foreach (array('nonce','tag','ciphertext') as $field) {
            if (!isset($envelope[$field]) || !is_string($envelope[$field])) {
                throw new \RuntimeException('Enveloppe Guardian invalide.');
            }
        }
        $nonce = base64_decode($envelope['nonce'], true);
        $tag = base64_decode($envelope['tag'], true);
        $ciphertext = base64_decode($envelope['ciphertext'], true);
        if ($nonce === false || strlen($nonce) !== 12 || $tag === false || strlen($tag) !== 16 || $ciphertext === false) {
            throw new \RuntimeException('Enveloppe Guardian corrompue.');
        }
        $plain = openssl_decrypt($ciphertext, 'aes-256-gcm', $this->key($name), OPENSSL_RAW_DATA, $nonce, $tag, 'guardian:'.$name);
        if ($plain === false) { throw new \RuntimeException('Déchiffrement ou authentification Guardian impossible.'); }
        $decoded = json_decode($plain, true);
        if (!is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) { throw new \RuntimeException('Collection Guardian invalide.'); }
        return $decoded;
    }

    /** Transaction lecture-modification-écriture protégée par flock. */
    public function change($name, callable $callback)
    {
        $this->initialize();
        $path = $this->path($name);
        $lock = @fopen($path.'.lock', 'c');
        if ($lock === false || !flock($lock, LOCK_EX)) { throw new \RuntimeException('Verrouillage Guardian indisponible.'); }
        @chmod($path.'.lock', 0600);
        try {
            $next = $callback($this->read($name));
            if (!is_array($next)) { throw new \LogicException('La transaction doit retourner un tableau.'); }
            $plain = json_encode($next, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            if ($plain === false) { throw new \RuntimeException('Sérialisation Guardian impossible.'); }
            $nonce = random_bytes(12);
            $tag = '';
            $cipher = openssl_encrypt($plain, 'aes-256-gcm', $this->key($name), OPENSSL_RAW_DATA, $nonce, $tag, 'guardian:'.$name);
            if ($cipher === false) { throw new \RuntimeException('Chiffrement Guardian impossible.'); }
            $payload = json_encode(array('schema'=>2,'nonce'=>base64_encode($nonce),'tag'=>base64_encode($tag),'ciphertext'=>base64_encode($cipher)));
            $tmp = tempnam($this->root, '.guardian-');
            if ($tmp === false) { throw new \RuntimeException('Fichier temporaire impossible.'); }
            @chmod($tmp, 0600);
            try {
                if (file_put_contents($tmp, $payload, LOCK_EX) === false || !@rename($tmp, $path)) {
                    throw new \RuntimeException('Écriture atomique impossible.');
                }
                @chmod($path, 0600);
            } finally { if (is_file($tmp)) { @unlink($tmp); } }
            return $next;
        } finally { flock($lock, LOCK_UN); fclose($lock); }
    }

}
