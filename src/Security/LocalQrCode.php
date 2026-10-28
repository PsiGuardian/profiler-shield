<?php
namespace OmegaGuardian\ProfilerShield\Security;

/**
 * Matrice QR TOTP créée localement avec l'encodeur BaconQrCode 2.x.
 *
 * Le SVG est constitué uniquement de coordonnées de modules noirs.
 * Aucune URL, aucune clé et aucun libellé contrôlé par l'utilisateur
 * n'apparaissent en clair dans ses attributs XML. La bibliothèque génère
 * les données QR (masquage, Reed-Solomon), et ce code ne fait que tracer
 * leur matrice sous forme de segments horizontaux SVG.
 *
 * Avantage : aucun service externe, GD ou XMLWriter nécessaire. La matrice
 * et les secrets ne sont pas écrits sur le disque ni dans les journaux.
 *
 * ATTENTION : le QR révèle volontairement la clé au navigateur authentifié
 * pour l'enrôlement. La page a des en-têtes no-store et no-referrer.
 */
final class LocalQrCode
{
    public static function renderSvg($uri)
    {
        if (!is_string($uri) || strpos($uri, 'otpauth://totp/') !== 0 || strlen($uri) > 2048) {
            throw new \InvalidArgumentException('URI de configuration TOTP invalide.');
        }
        if (!class_exists('BaconQrCode\\Encoder\\Encoder')) {
            throw new \RuntimeException('Encodeur QR local non installé.');
        }
        $qr = \BaconQrCode\Encoder\Encoder::encode(
            $uri,
            \BaconQrCode\Common\ErrorCorrectionLevel::M(),
            'UTF-8'
        );
        $matrix = $qr->getMatrix();
        $width = $matrix->getWidth();
        $height = $matrix->getHeight();
        if ($width < 21 || $width > 177 || $height !== $width) {
            throw new \RuntimeException('Matrice QR de taille inattendue.');
        }
        $margin = 4;
        $size = $width + 2*$margin;
        $path = '';
        // Compaction par plages contiguës sur chaque ligne : diminue le
        // poids SVG sans aucune dépendance sur un moteur de rendu serveur.
        for ($y=0;$y<$height;$y++) {
            $x=0;
            while ($x<$width) {
                if ($matrix->get($x,$y) !== 1) { $x++; continue; }
                $begin=$x;
                while ($x<$width && $matrix->get($x,$y) === 1) { $x++; }
                $path.='M'.($begin+$margin).' '.($y+$margin).'h'.($x-$begin).'v1h-'.($x-$begin).'z';
            }
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" width="248" height="248" viewBox="0 0 '.$size.' '.$size.'" shape-rendering="crispEdges" role="img">'
            .'<rect width="'.$size.'" height="'.$size.'" fill="#ffffff"/>'
            .'<path fill="#10201c" d="'.$path.'"/></svg>';
    }
}
