<?php
namespace OmegaGuardian\ProfilerShield\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Deny by default. Only explicitly tagged, trusted application services
 * may supply public error data. Broken mappers fail closed.
 */
final class PublicErrorRegistry
{
    /** @var list<PublicErrorMapperInterface> */
    private array $mappers = [];

    public function addMapper(PublicErrorMapperInterface $mapper): void
    {
        $this->mappers[] = $mapper;
    }

    public function resolve(\Throwable $error, Request $request): ?PublicErrorDescriptor
    {
        $status = $error instanceof HttpExceptionInterface ? $error->getStatusCode() : 500;
        if ($status < 400 || $status > 599) {
            $status = 500;
        }
        foreach ($this->mappers as $mapper) {
            try {
                if (!$mapper->supports($error, $request)) {
                    continue;
                }
                $description = $mapper->map($error, $request);
                if ($description === null) {
                    return null;
                }
                // Never let a mapper turn a server error into success or a
                // different HTTP failure class without an explicit change in app logic.
                return $description->status === $status ? $description : null;
            } catch (\Throwable) {
                return null; // no secondary exception, no pass-through
            }
        }
        return null;
    }
}
