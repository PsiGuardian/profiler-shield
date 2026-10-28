<?php
namespace OmegaGuardian\ProfilerShield\Security;

/** TOTP RFC 6238, HMAC-SHA1 / 30s, fenêtre ±1 tranche de temps. */
final class Totp
{
    const BASE32 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public function generateSecret()
    {
        $bytes = random_bytes(20);
        $bits = '';
        for ($i=0;$i<strlen($bytes);$i++) { $bits .= str_pad(decbin(ord($bytes[$i])),8,'0',STR_PAD_LEFT); }
        $result = '';
        foreach (str_split($bits,5) as $part) { $result .= self::BASE32[bindec($part)]; }
        return $result;
    }

    private function decode($secret)
    {
        $bits = '';
        $secret = strtoupper($secret);
        for ($i=0;$i<strlen($secret);$i++) {
            $position = strpos(self::BASE32, $secret[$i]);
            if ($position === false) { throw new \InvalidArgumentException('Secret TOTP invalide'); }
            $bits .= str_pad(decbin($position),5,'0',STR_PAD_LEFT);
        }
        $raw = '';
        foreach (str_split($bits,8) as $part) { if (strlen($part) === 8) { $raw .= chr(bindec($part)); } }
        return $raw;
    }

    public function verify($secret, $code, $now = null)
    {
        return $this->verifyWindow($secret, $code, $now) !== null;
    }

    /** Retourne le compteur TOTP accepté pour empêcher sa réutilisation. */
    public function verifyWindow($secret, $code, $now = null)
    {
        if (!is_string($code) || !preg_match('/^\d{6}$/D', $code)) { return null; }
        $now = $now === null ? time() : (int) $now;
        try { $key = $this->decode($secret); }
        catch (\InvalidArgumentException $e) { return null; }
        foreach (array(-1,0,1) as $offset) {
            $counter = (int) floor($now / 30) + $offset;
            $binary = pack('N2', 0, $counter);
            $mac = hash_hmac('sha1', $binary, $key, true);
            $index = ord(substr($mac,-1)) & 0x0f;
            $value = unpack('N', substr($mac,$index,4))[1] & 0x7fffffff;
            if (hash_equals(str_pad((string) ($value % 1000000),6,'0',STR_PAD_LEFT), $code)) { return $counter; }
        }
        return null;
    }

    /** Secret chiffré via AES-256-GCM ; clé obligatoire hors dépôt. */
    public function encrypt($secret)
    {
        $key = \OmegaGuardian\ProfilerShield\Util\Env::get('GUARDIAN_ENCRYPTION_KEY');
        if (!$key || !preg_match('/^[a-f0-9]{64}$/Di', $key)) {
            throw new \RuntimeException('GUARDIAN_ENCRYPTION_KEY doit contenir 32 octets hexadécimaux.');
        }
        $iv = random_bytes(12);
        $tag = '';
        $cipher = openssl_encrypt($secret, 'aes-256-gcm', hex2bin($key), OPENSSL_RAW_DATA, $iv, $tag);
        if ($cipher === false) { throw new \RuntimeException('Chiffrement TOTP impossible.'); }
        return base64_encode($iv.$tag.$cipher);
    }

    public function decrypt($encrypted)
    {
        $key = \OmegaGuardian\ProfilerShield\Util\Env::get('GUARDIAN_ENCRYPTION_KEY');
        if (!$key || !preg_match('/^[a-f0-9]{64}$/Di', $key)) { throw new \RuntimeException('Clé TOTP indisponible.'); }
        $raw = base64_decode($encrypted, true);
        if ($raw === false || strlen($raw) < 29) { throw new \RuntimeException('TOTP corrompu.'); }
        $plain = openssl_decrypt(substr($raw,28),'aes-256-gcm',hex2bin($key), OPENSSL_RAW_DATA, substr($raw,0,12),substr($raw,12,16));
        if ($plain === false) { throw new \RuntimeException('Déchiffrement TOTP impossible.'); }
        return $plain;
    }
}
