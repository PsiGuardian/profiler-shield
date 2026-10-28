<?php
namespace OmegaGuardian\ProfilerShield\Security;

use Symfony\Component\HttpFoundation\Response;

/**
 * Affichage d'erreur sans marqueur Guardian, sans diagnostic et sans lien externe.
 *
 * Ne jamais substituer un faux status HTTP : les caches, API, sondes et crawlers
 * lisent le statut réel même quand le HTML ne contient aucun chiffre.
 * Le modèle personnalisé du site est la priorité sur les 404 levées comme
 * exceptions. Les 404 déjà renvoyées par le site ne sont jamais modifiées
 * en mode "native".
 */
final class PublicErrorRenderer
{
    private $settings;
    private $projectDir;
    private $twig;

    public function __construct(Settings $settings, $projectDir, $twig = null)
    {
        $this->settings = $settings;
        $this->projectDir = (string) $projectDir;
        $this->twig = $twig;
    }

    public function config()
    {
        try { return $this->settings->all(); }
        catch (\Throwable $ignored) { return Settings::defaults(); }
    }

    /** HTML uniquement. Les autres formats métier restent inchangés. */
    public static function acceptsHtml($request)
    {
        // Aucun chemin métier non erroné n'est intercepté par cette classe.
        if (method_exists($request, 'getRequestFormat') && $request->getRequestFormat() !== 'html') {
            return false;
        }
        if (isset($request->headers) && method_exists($request->headers, 'get')) {
            $accept = strtolower((string)$request->headers->get('Accept',''));
            if ($accept !== '' && strpos($accept,'application/json') !== false
                && strpos($accept,'text/html') === false) { return false; }
        }
        return true;
    }

    public function exceptionPage($status, array $config)
    {
        // Les pages 404 de l'hôte ne sont jamais rendues en mode obfuscation.
        if (($config['error_mode'] ?? 'native') === 'native' && $status === 404) {
            $custom = $this->site404();
            if ($custom !== null) { return $custom; }
        }
        return $this->neutral($status, $config);
    }

    public function json($status)
    {
        return new Response('{"message":"Request failed"}', (int)$status, array(
            'Content-Type'=>'application/json; charset=UTF-8',
            'Cache-Control'=>'no-store, private',
            'X-Content-Type-Options'=>'nosniff',
        ));
    }

    /** Pas de rendu du navigateur d'exception Symfony, même en APP_DEBUG=1. */
    private function site404()
    {
        if ($this->twig === null || !method_exists($this->twig, 'render')) { return null; }
        // Reconnaît uniquement les chemins officiels d'override TwigBundle.
        // Pas de recherche récursive ou de traitement de chemins utilisateur.
        foreach (array('error404.html.twig', 'error.html.twig') as $name) {
            $path = $this->projectDir.'/templates/bundles/TwigBundle/Exception/'.$name;
            if (!is_file($path)) { continue; }
            try {
                $body = $this->twig->render('@Twig/Exception/'.$name, array(
                    'status_code' => 404, 'status_text' => 'Not Found',
                ));
                if (!is_string($body) || $body === '') { continue; }
                return $this->response($body, 404);
            } catch (\Throwable $ignored) {
                // On ne renvoie jamais une exception Twig en HTTP public.
            }
        }
        return null;
    }

    public function neutral($status, array $config)
    {
        $mode = $config['error_mode'] ?? 'native';
        $message = $mode === 'custom' ? (string)($config['error_custom_text'] ?? '') : 'The requested resource is unavailable.';
        if (trim($message) === '') { $message = 'The requested resource is unavailable.'; }
        $reference = '';
        if (($config['error_reference'] ?? 'none') === 'random') {
            try { $reference = strtoupper(bin2hex(random_bytes(6))); }
            catch (\Throwable $ignored) { $reference = ''; }
        } elseif (($config['error_reference'] ?? 'none') === 'custom') {
            $reference = (string)($config['error_reference_text'] ?? '');
        }
        $body = '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
            .'<meta name="robots" content="noindex,nofollow"><title>Unavailable</title>'
            .'<style>html{background:#f8fafc;color:#18212b;font:16px system-ui,sans-serif}body{min-height:80vh;display:grid;place-items:center;margin:0;padding:1rem}main{max-width:36rem}h1{font-size:1.5rem;font-weight:600}p{line-height:1.6}small{color:#64748b}</style></head><body>'
            .'<main><h1>Unavailable</h1><p>'.htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</p>'
            .($reference !== '' ? '<small>'.htmlspecialchars($reference, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</small>':'')
            .'</main></body></html>';
        return $this->response($body, (int)$status);
    }

    private function response($body, $status)
    {
        return new Response($body, $status, array(
            'Content-Type' => 'text/html; charset=UTF-8',
            'Cache-Control' => 'no-store, private',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'no-referrer',
            'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline'; base-uri 'none'; frame-ancestors 'none'",
        ));
    }
}
