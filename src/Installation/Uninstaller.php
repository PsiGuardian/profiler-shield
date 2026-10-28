<?php
namespace OmegaGuardian\ProfilerShield\Installation;

/**
 * Réversion prudente des modifications apportées par OmegaGuardian au projet hôte.
 *
 * - Ne retire jamais un fichier modifié ou créé par une autre application.
 * - Ne touche jamais aux fichiers métiers, au vendor hors Composer, ni au .env entier.
 * - Le coffre (comptes/TOTP/audit) est supprimé uniquement sur consentement explicite.
 * - Toutes les opérations peuvent être répétées après interruption.
 */
final class Uninstaller
{
    const RECEIPT = '.guardian-shield/.installation.json';
    const BUNDLE_LINE = "    \\OmegaGuardian\\ProfilerShield\\OmegaGuardianProfilerShieldBundle::class => ['dev' => true, 'test' => true],";

    /** @return array<string,mixed> Snapshot sans aucune valeur de secret */
    public static function snapshot($root)
    {
        $root = rtrim((string)$root, '/');
        return array(
            'schema' => 1,
            'env_existed' => is_file($root.'/.env.local'),
            'env_keys' => self::envKeys(self::read($root.'/.env.local')),
            'ignore_existed' => is_file($root.'/.gitignore'),
            'ignore_lines' => self::ignoreLines(self::read($root.'/.gitignore')),
            'route_existed' => file_exists($root.'/config/routes/guardian.php'),
            'route_import_existed' => file_exists($root.'/config/routes/guardian.yaml'),
            'profiler_existed' => file_exists($root.'/config/packages/dev/zz_guardian_profiler.yaml'),
            'bundle_line_existed' => strpos(self::read($root.'/config/bundles.php'), self::BUNDLE_LINE)!==false,
        );
    }

    public static function writeReceipt($root, array $before)
    {
        $root = rtrim((string)$root, '/');
        // Si le coffre a été conservé lors d'une désinstallation antérieure,
        // maintenir les droits de propriété sur les variables et exclusions
        // historiques afin qu'une réinstallation reste complètement réversible.
        $previous = is_file($root.'/'.self::RECEIPT)
            ? json_decode(self::read($root.'/'.self::RECEIPT), true) : null;
        if (!is_array($previous) || !isset($previous['schema']) || $previous['schema'] !== 1) { $previous = array(); }
        $receipt = array(
            'schema' => 1,
            'managed_env_keys' => array_values(array_diff(
                self::envKeys(self::read($root.'/.env.local')),
                isset($before['env_keys']) ? $before['env_keys'] : array()
            )),
            'env_file_created' => empty($before['env_existed']),
            'managed_ignore_lines' => array_values(array_diff(
                self::ignoreLines(self::read($root.'/.gitignore')),
                isset($before['ignore_lines']) ? $before['ignore_lines'] : array()
            )),
            'ignore_file_created' => empty($before['ignore_existed']),
            'bundle_line_added' => empty($before['bundle_line_existed']),
            'route_hash' => empty($before['route_existed']) ? self::hashFile($root.'/config/routes/guardian.php') : null,
            'route_import_hash' => empty($before['route_import_existed']) ? self::hashFile($root.'/config/routes/guardian.yaml') : null,
            'profiler_hash' => empty($before['profiler_existed']) ? self::hashFile($root.'/config/packages/dev/zz_guardian_profiler.yaml') : null,
        );
        foreach (array('managed_env_keys','managed_ignore_lines') as $field) {
            if (isset($previous[$field]) && is_array($previous[$field])) {
                $receipt[$field] = array_values(array_unique(array_merge($previous[$field], $receipt[$field])));
            }
        }
        foreach (array('env_file_created','ignore_file_created') as $field) {
            $receipt[$field] = !empty($previous[$field]) || !empty($receipt[$field]);
        }
        // A repeated setup sees already-existing files. Keep the original
        // fingerprints rather than losing their proven ownership. Removal
        // still compares the current bytes, preserving customized files.
        foreach (array('route_hash','route_import_hash','profiler_hash') as $field) {
            if ($receipt[$field] === null && isset($previous[$field])
                && is_string($previous[$field]) && preg_match('/^[a-f0-9]{64}$/D',$previous[$field])) {
                $receipt[$field] = $previous[$field];
            }
        }
        $path = $root.'/'.self::RECEIPT;
        if (!file_exists(dirname($path)) && !is_link(dirname($path))) {
            if (!@mkdir(dirname($path),0700,true)) {throw new \RuntimeException('Création du dossier de reçu Guardian impossible.');}
        }
        if (!is_dir(dirname($path)) || is_link(dirname($path)) || is_link($path)) {
            throw new \RuntimeException('Impossible de sécuriser le manifeste de désinstallation Guardian.');
        }
        $encoded = json_encode($receipt, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if (!is_string($encoded)) { throw new \RuntimeException('Manifeste Guardian non sérialisable.'); }
        $tmp = tempnam(dirname($path), '.guardian-receipt-');
        if ($tmp === false) { throw new \RuntimeException('Écriture du manifeste Guardian impossible.'); }
        try {
            @chmod($tmp, 0600);
            if (file_put_contents($tmp, $encoded."\n", LOCK_EX) === false || !@rename($tmp, $path)) {
                throw new \RuntimeException('Sauvegarde du manifeste Guardian impossible.');
            }
            @chmod($path, 0600);
        } finally { if (is_file($tmp)) { @unlink($tmp); } }
    }

    /** @param callable $log callable(string):void */
    public function execute($root, callable $log, $purgeData = false)
    {
        $root = rtrim((string)$root, '/');
        $receiptPath = $root.'/'.self::RECEIPT;
        $receipt = null;
        if (is_link($receiptPath)) { throw new \RuntimeException('Manifeste de désinstallation symbolique : opération refusée.'); }
        if (is_file($receiptPath)) {
            $receipt = json_decode(self::read($receiptPath), true);
            if (!is_array($receipt) || !isset($receipt['schema']) || $receipt['schema'] !== 1) {
                throw new \RuntimeException('Manifeste de désinstallation invalide : retrait automatique refusé.');
            }
        }
        // Une purge partielle peut détruire la clé tout en laissant un coffre
        // externe ou des fichiers inconnus chiffrés. Refuser la purge AVANT
        // toute modification de l'environnement si l'effacement n'est pas sûr.
        $envForCheck = self::read($root.'/.env.local');
        $external = getenv('GUARDIAN_STORAGE_DIR');
        if (!is_string($external)) { $external = ''; }
        if (preg_match('/^\h*GUARDIAN_STORAGE_DIR\h*=\h*([^\s#]+)/m', $envForCheck, $found)) {
            $external = trim($found[1], "\"'");
        }
        if ($purgeData && $external !== '') {
            $log('ΩGuardian : coffre externe GUARDIAN_STORAGE_DIR détecté : suppression automatique du coffre et de la clé REFUSÉE.');
            $purgeData = false;
        }
        if ($purgeData && !$this->canPurgeVault($root.'/.guardian-shield')) {
            $log('ΩGuardian : fichiers inconnus, symlinks ou permissions insuffisantes dans le coffre : purge complète REFUSÉE, clé conservée.');
            $purgeData = false;
        }

        $route = $root.'/config/routes/guardian.php';
        $profiler = $root.'/config/packages/dev/zz_guardian_profiler.yaml';
        foreach (array(
            array($route, 'route_hash'),
            array($root.'/config/routes/guardian.yaml', 'route_import_hash'),
            array($profiler, 'profiler_hash'),
        ) as $item) {
            list($path, $field) = $item;
            if (!file_exists($path) && !is_link($path)) { continue; }
            if (is_link($path) || !is_file($path)) { $log('ΩGuardian : fichier inattendu conservé : '.$path); continue; }
            $expected = $receipt !== null && isset($receipt[$field]) && is_string($receipt[$field]) ? $receipt[$field] : null;
            $safe = $expected !== null && hash_equals($expected, (string)self::hashFile($path));
            if ($safe) {
                if (!@unlink($path)) { throw new \RuntimeException('Impossible de retirer '.$path); }
                $log('ΩGuardian : fichier généré retiré : '.substr($path, strlen($root)+1));
            } else {
                $log('ΩGuardian : fichier conservé car personnalisé ou non attribuable : '.substr($path,strlen($root)+1));
            }
        }

        $bundles = $root.'/config/bundles.php';
        if (is_file($bundles) && !is_link($bundles)) {
            $content = self::read($bundles);
            // Correspond à l'entrée standard même si bundles.php tient sur une
            // seule ligne ("<?php return [];") avant l'installation.
            $pattern = <<<'REGEX'
~[ \t]*\\?OmegaGuardian\\ProfilerShield\\OmegaGuardianProfilerShieldBundle::class[ \t]*=>[ \t]*\[[ \t]*['"]dev['"][ \t]*=>[ \t]*true[ \t]*,[ \t]*['"]test['"][ \t]*=>[ \t]*true[ \t]*\][ \t]*,?[ \t]*(?:\r?\n)?~
REGEX;
            $new = preg_replace($pattern, '', $content, -1, $count);
            if (is_string($new) && $count > 0) {
                $this->rewrite($bundles, $new);
                $log('ΩGuardian : entrée dev/test retirée de config/bundles.php.');
            } elseif (strpos($content,'OmegaGuardianProfilerShieldBundle::class')!==false) {
                $log('ΩGuardian : bundles.php personnalisé : entrée conservée pour revue manuelle.');
            }
        }

        $env = $root.'/.env.local';
        if (is_file($env) && !is_link($env)) {
            $keys = $receipt !== null ? (isset($receipt['managed_env_keys']) && is_array($receipt['managed_env_keys']) ? $receipt['managed_env_keys'] : array()) : array();
            $keys = array_values(array_filter($keys, function ($name) { return is_string($name) && preg_match('/^GUARDIAN_[A-Z0-9_]+$/D', $name); }));
            if (!$purgeData) {
                // Sans clé, un coffre conservé deviendrait définitivement
                // illisible. Préserver les secrets de déchiffrement et leur
                // exclusion Git jusqu'à la demande explicite d'effacement.
                $keys = array_values(array_diff($keys, array('GUARDIAN_ENCRYPTION_KEY', 'GUARDIAN_STORAGE_DIR')));
            }
            $old = self::read($env);
            $new = $old;
            foreach ($keys as $key) { $new = preg_replace('/^\h*'.preg_quote($key,'/').'\h*=.*(?:\R|$)/m', '', $new); }
            if ($old !== $new) {
                if ($new === '' && $receipt !== null && !empty($receipt['env_file_created'])) { @unlink($env); }
                else { $this->rewrite($env, $new); }
                $log('ΩGuardian : variables créées par Guardian retirées de .env.local.');
            }
        }

        $ignore = $root.'/.gitignore';
        if (is_file($ignore) && !is_link($ignore)) {
            $lines = $receipt !== null ? (isset($receipt['managed_ignore_lines']) && is_array($receipt['managed_ignore_lines']) ? $receipt['managed_ignore_lines'] : array()) : array();
            $lines = array_values(array_intersect($lines,array('/.guardian-shield/','/.env.local')));
            if (!$purgeData) { $lines = array(); } // Coffre et clé toujours exclus de Git.
            $old = self::read($ignore); $new=$old;
            foreach ($lines as $line) {
                $new = preg_replace('/^'.preg_quote($line,'/').'\h*(?:\R|$)/m', '', $new, 1);
                if ($line === '/.guardian-shield/') { $new = preg_replace('/^# ΩGuardian : coffre persistant chiffré \(ne jamais versionner\)\h*(?:\R|$)/mu', '', $new, 1); }
                if ($line === '/.env.local') { $new = preg_replace('/^# ΩGuardian : chemins et clés privées\h*(?:\R|$)/mu', '', $new, 1); }
            }
            if ($new !== $old) {
                if ($new === '' && $receipt !== null && !empty($receipt['ignore_file_created'])) { @unlink($ignore); }
                else { $this->rewrite($ignore,$new); }
                $log('ΩGuardian : règles .gitignore ajoutées par Guardian retirées.');
            }
        }
        // Nettoyage du reçu seulement lorsque les mutations de configuration
        // ont été réconciliées. En cas de données conservées, le coffre persiste.
        if ($purgeData && is_file($receiptPath)) { @unlink($receiptPath); }
        if ($purgeData) {
            $this->purgeDefaultVault($root.'/.guardian-shield', $log);
        } else {
            if (is_dir($root.'/.guardian-shield')) {
                $log('ΩGuardian : coffre et clé de déchiffrement CONSERVÉS avec exclusions Git. Pour préparer un effacement complet : php bin/console guardian:uninstall --purge, avant composer remove --dev omegaguardian/profiler-shield.');
            }
        }
        $log('ΩGuardian : restauration de la configuration du projet terminée.');
    }

    private static function knownVaultFiles()
    {
        $allowed = array('.htaccess','initial-admin-credentials.txt', '.installation.json');
        foreach (array('accounts','alerts','attempts','audit','control','devices','emergency_codes','sessions','settings') as $name) {
            $allowed[]=$name.'.json'; $allowed[]=$name.'.json.lock';
        }
        return $allowed;
    }

    private function canPurgeVault($vault)
    {
        if (!file_exists($vault) && !is_link($vault)) { return true; }
        if (is_link($vault) || !is_dir($vault) || !is_writable($vault)) { return false; }
        $files=@scandir($vault);
        if (!is_array($files)) { return false; }
        $allowed=self::knownVaultFiles();
        foreach ($files as $file) {
            if ($file==='.' || $file==='..') { continue; }
            if (!in_array($file,$allowed,true) || is_link($vault.'/'.$file) || !is_file($vault.'/'.$file)) { return false; }
        }
        return true;
    }

    private function purgeDefaultVault($vault, callable $log)
    {
        if (is_link($vault) || !is_dir($vault)) { return; }
        $allowed=self::knownVaultFiles();
        $entries=@scandir($vault);
        if (!is_array($entries)) { throw new \RuntimeException('Impossible de lire le coffre avant suppression.'); }
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') { continue; }
            $file=$vault.'/'.$entry;
            if (!in_array($entry,$allowed,true) || is_link($file) || !is_file($file)) {
                $log('ΩGuardian : élément inconnu conservé dans le coffre : '.$entry);
                continue;
            }
            if (!@unlink($file)) { throw new \RuntimeException('Impossible de retirer le fichier du coffre : '.$entry); }
        }
        if (@rmdir($vault)) { $log('ΩGuardian : coffre Guardian supprimé définitivement.'); }
        else { $log('ΩGuardian : répertoire du coffre conservé (fichiers étrangers ou permissions).'); }
    }

    private static function envKeys($data)
    {
        preg_match_all('/^\h*(GUARDIAN_[A-Z0-9_]+)\h*=/m', (string)$data, $matches);
        return array_values(array_unique($matches[1]));
    }

    private static function ignoreLines($data)
    {
        $lines=preg_split('/\R/',(string)$data);
        return array_values(array_intersect($lines,array('/.guardian-shield/','/.env.local')));
    }

    private static function read($file)
    {
        if (!is_file($file) || is_link($file)) { return ''; }
        $data=@file_get_contents($file);
        if (!is_string($data)) { throw new \RuntimeException('Lecture impossible : '.$file); }
        return $data;
    }

    private static function hashFile($file)
    {
        return is_file($file) && !is_link($file) ? hash_file('sha256',$file) : null;
    }

    private function rewrite($path,$content)
    {
        $tmp=@tempnam(dirname($path),'.guardian-uninstall-');
        if ($tmp===false) { throw new \RuntimeException('Écriture temporaire impossible : '.$path); }
        try {
            $mode=@fileperms($path);
            if ($mode!==false) { @chmod($tmp,$mode & 0777); }
            if (file_put_contents($tmp,$content, LOCK_EX)===false || !@rename($tmp,$path)) {
                throw new \RuntimeException('Écriture impossible : '.$path);
            }
        } finally { if (is_file($tmp)) { @unlink($tmp); } }
    }
}
