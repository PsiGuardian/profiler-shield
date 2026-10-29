<?php
namespace OmegaGuardian\ProfilerShield\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Safe Native HTTP representations for exceptions not eligible for a trusted
 * Symfony diagnostic page. Never serializes Throwable or its message.
 *
 * HTML remains owned by PublicErrorRenderer and the existing error_mode.
 */
final class SafeNativeRenderer
{
    public function format(Request $request): string
    {
        // Symfony's _format is an explicit route contract. Its implicit
        // 'html' default is not, so do not let it override Accept.
        $explicit = isset($request->attributes) && method_exists($request->attributes, 'get')
            ? $request->attributes->get('_format') : null;
        if (is_string($explicit) && $explicit !== '') {
            $routeFormat = match (strtolower($explicit)) {
                'json' => 'json', 'xml' => 'xml',
                'problem+json' => 'problem-json',
                'problem+xml' => 'problem-xml',
                default => null,
            };
            if ($routeFormat !== null) {
                return $routeFormat;
            }
        }
        $accept = isset($request->headers) && method_exists($request->headers, 'get')
            ? (string) $request->headers->get('Accept', '') : '';
        $fallback = str_starts_with($request->getPathInfo(), '/api/') ? 'json' : 'html';
        if ($accept === '') {
            return $fallback;
        }
        $choices = [];
        foreach (array_slice(explode(',', $accept), 0, 48) as $position => $entry) {
            $parts = explode(';', trim($entry));
            $mime = strtolower(trim(array_shift($parts)));
            $quality = 1.0;
            foreach ($parts as $part) {
                if (preg_match('/^\s*q\s*=\s*(0(?:\.\d+)?|1(?:\.0+)?)\s*$/iD', $part, $match)) {
                    $quality = (float) $match[1];
                }
            }
            if ($quality <= 0) {
                continue;
            }
            $format = match ($mime) {
                'application/json', 'application/*+json' => 'json',
                'application/xml', 'text/xml', 'application/*+xml' => 'xml',
                'application/problem+json' => 'problem-json',
                'application/problem+xml' => 'problem-xml',
                'text/html', 'application/xhtml+xml' => 'html',
                'text/plain' => 'text',
                '*/*' => $fallback,
                default => null,
            };
            if ($format === null) {
                continue;
            }
            $specificity = $mime === '*/*' ? 0 : (str_contains($mime, '*') ? 1 : 2);
            $choices[] = [$quality, $specificity, $position, $format];
        }
        usort($choices, static fn (array $a, array $b): int =>
            ($b[0] <=> $a[0]) ?: ($b[1] <=> $a[1]) ?: ($a[2] <=> $b[2])
        );
        return $choices[0][3] ?? $fallback;
    }

    public function render(int $status, string $format, ?PublicErrorDescriptor $approved = null): Response
    {
        if ($status < 400 || $status > 599) {
            $status = 500;
        }
        if ($approved !== null && $approved->status !== $status) {
            $approved = null;
        }
        $code = $approved?->code ?? 'REQUEST_FAILED';
        $title = $this->title($status);
        $headers = [
            'Cache-Control' => 'no-store, private',
            'Referrer-Policy' => 'no-referrer',
            'X-Content-Type-Options' => 'nosniff',
            'Vary' => 'Accept',
        ];
        if ($format === 'problem-json' || $format === 'problem-xml') {
            $problem = ['type' => 'about:blank', 'title' => $title, 'status' => $status];
            if ($approved !== null) {
                $problem['detail'] = $approved->publicMessage;
                $problem['code'] = $approved->code;
            }
            if ($format === 'problem-json') {
                $response = new JsonResponse($problem, $status, $headers);
                $response->headers->set('Content-Type', 'application/problem+json');
            } else {
                $xml = '<?xml version="1.0" encoding="UTF-8"?>'
                    .'<problem xmlns="urn:ietf:rfc:7807"><type>about:blank</type>'
                    .'<title>'.$this->xml($title).'</title><status>'.$status.'</status>';
                if ($approved !== null) {
                    $xml .= '<detail>'.$this->xml($approved->publicMessage).'</detail>'
                        .'<code>'.$this->xml($approved->code).'</code>';
                }
                $response = new Response($xml.'</problem>', $status, $headers);
                $response->headers->set('Content-Type', 'application/problem+xml');
            }
        } elseif ($format === 'xml') {
            $xml = '<?xml version="1.0" encoding="UTF-8"?>'
                .'<error><code>'.$this->xml($code).'</code><status>'.$status.'</status>';
            if ($approved !== null) {
                $xml .= '<safe_message>'.$this->xml($approved->publicMessage).'</safe_message>';
                foreach ($approved->fields as $name => $value) {
                    $xml .= '<field name="'.$this->xml($name).'">'.$this->xml($value).'</field>';
                }
            }
            $response = new Response($xml.'</error>', $status, $headers);
            $response->headers->set('Content-Type', 'application/xml');
        } elseif ($format === 'json') {
            $error = ['code' => $code, 'status' => $status];
            if ($approved !== null) {
                $error['safe_message'] = $approved->publicMessage;
                if ($approved->fields !== []) {
                    $error['fields'] = $approved->fields;
                }
            }
            // Keep the existing v1.0.0 "message" key for generic API clients.
            $payload = $approved !== null
                ? ['error' => $error]
                : ['message' => 'Request failed', 'error' => $error];
            $response = new JsonResponse($payload, $status, $headers);
            // Preserve v1.0.0 API Content-Type including its charset.
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        } else {
            $response = new Response($approved?->publicMessage ?? $title, $status, $headers);
            $response->headers->set('Content-Type', 'text/plain; charset=UTF-8');
        }
        if ($approved !== null) {
            foreach ($approved->headers as $name => $value) {
                $response->headers->set($name, $value);
            }
        }
        return $response;
    }

    private function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function title(int $status): string
    {
        return match ($status) {
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            409 => 'Conflict',
            422 => 'Unprocessable Content',
            429 => 'Too Many Requests',
            503 => 'Service Unavailable',
            default => 'Internal Server Error',
        };
    }
}
