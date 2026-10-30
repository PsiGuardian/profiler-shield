<?php
namespace OmegaGuardian\ProfilerShield\Security;

/**
 * Strictly public, immutable error description. Never stores Throwable.
 * Only application-authored public values may be passed to this class.
 */
final readonly class PublicErrorDescriptor
{
    public int $status;
    public string $code;
    public string $publicMessage;
    public array $headers;
    public array $fields;

    public function __construct(int $status, string $code, string $publicMessage, array $headers = [], array $fields = [])
    {
        if ($status < 400 || $status > 599) {
            throw new \InvalidArgumentException('Public error status must be HTTP 400–599.');
        }
        if (preg_match('/^[A-Z][A-Z0-9_]{2,63}$/D', $code) !== 1) {
            throw new \InvalidArgumentException('Invalid public error code.');
        }
        if (strlen($publicMessage) > 160 || preg_match('/[\x00-\x1f\x7f]/', $publicMessage)) {
            throw new \InvalidArgumentException('Invalid public error message.');
        }
        $normalized = [];
        foreach ($headers as $name => $value) {
            $lower = strtolower((string) $name);
            if (!in_array($lower, [
                'allow', 'retry-after', 'www-authenticate', 'x-error-code',
                'access-control-allow-origin', 'access-control-allow-methods',
                'access-control-allow-headers', 'access-control-expose-headers',
                'vary',
            ], true) || !is_string($value) || strlen($value) > 256
                || preg_match('/[\x00-\x1f\x7f]/', $value)) {
                throw new \InvalidArgumentException('Unapproved public HTTP header.');
            }
            if ($lower === 'access-control-allow-origin'
                && $value !== '*' && !preg_match('~^https?://[A-Za-z0-9.-]+(?::[0-9]{1,5})?$~D', $value)) {
                throw new \InvalidArgumentException('Invalid public CORS origin.');
            }
            $normalized[$name] = $value;
        }
        if (count($fields) > 32) {
            throw new \InvalidArgumentException('Too many public error fields.');
        }
        foreach ($fields as $name => $value) {
            if (!is_string($name) || preg_match('/^[a-zA-Z][a-zA-Z0-9_]{0,63}$/D', $name) !== 1
                || !is_string($value) || strlen($value) > 256
                || preg_match('/[\x00-\x08\x0b\x0c\x0e-\x1f\x7f]/', $value)) {
                throw new \InvalidArgumentException('Invalid public error field.');
            }
        }
        $this->status = $status;
        $this->code = $code;
        $this->publicMessage = $publicMessage;
        $this->headers = $normalized;
        $this->fields = $fields;
    }
}
