<?php
namespace OmegaGuardian\ProfilerShield\Installation;

/** Register only the standard dev/test entry, without executing host PHP. */
final class BundleRegistration
{
    public function register(string $root): void
    {
        $path = $root.'/config/bundles.php';
        if (is_link($root.'/config') || is_link($path) || !is_file($path)) {
            throw new \RuntimeException('config/bundles.php absent ou symbolique ; activation manuelle requise.');
        }
        $content = file_get_contents($path);
        if (!is_string($content)) { throw new \RuntimeException('config/bundles.php illisible.'); }
        token_get_all($content, TOKEN_PARSE);
        $significant = array_values(array_filter(token_get_all($content), static fn($token) =>
            !is_array($token) || !in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)));
        foreach ($significant as $index => $token) {
            if (is_array($token) && in_array($token[0], [T_NAME_FULLY_QUALIFIED, T_NAME_QUALIFIED], true)
                && ltrim($token[1], '\\') === 'OmegaGuardian\\ProfilerShield\\OmegaGuardianProfilerShieldBundle') {
                if (($significant[$index + 1][0] ?? null) === T_DOUBLE_COLON
                    && ($significant[$index + 2][0] ?? null) === T_CLASS
                    && ($significant[$index + 3][0] ?? null) === T_DOUBLE_ARROW) {
                    return; // Never replace an existing application's policy.
                }
                throw new \RuntimeException('Référence ou alias Guardian personnalisé ; activation manuelle requise.');
            }
        }
        if (!preg_match('/\breturn\s*\[/i', $content) || !preg_match('/\]\s*;\s*$/D', $content, $closing, PREG_OFFSET_CAPTURE)) {
            throw new \RuntimeException('config/bundles.php personnalisé ; activation manuelle requise.');
        }
        $last = null;
        foreach (token_get_all(substr($content, 0, $closing[0][1])) as $token) {
            if (is_array($token) && in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) { continue; }
            $last = is_array($token) ? $token[1] : $token;
        }
        $separator = in_array($last, ['[', ','], true) ? '' : ',';
        $replacement = $separator."\n".Uninstaller::BUNDLE_LINE."\n];";
        $new = preg_replace('/\]\s*;\s*$/D', $replacement, $content, 1);
        token_get_all($new, TOKEN_PARSE);
        $temporary = tempnam(dirname($path), '.guardian-bundles-');
        if ($temporary === false) { throw new \RuntimeException('Répertoire config non modifiable.'); }
        try {
            if (file_put_contents($temporary, $new, LOCK_EX) !== strlen($new)) { throw new \RuntimeException('Écriture incomplète de bundles.php.'); }
            chmod($temporary, fileperms($path) & 0777);
            if (is_link($path) || file_get_contents($path) !== $content) { throw new \RuntimeException('bundles.php a changé ; aucune modification appliquée.'); }
            if (!rename($temporary, $path)) { throw new \RuntimeException('Remplacement de bundles.php impossible.'); }
        } finally { if (is_file($temporary)) { unlink($temporary); } }
    }
}
