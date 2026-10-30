<?php
namespace OmegaGuardian\ProfilerShield\Security;

use Symfony\Component\HttpFoundation\Request;

/**
 * Explicit trust boundary provided by the application maintainer.
 *
 * Applications must register a mapper as a service tagged
 * "omega_guardian.public_error_mapper". No Throwable message, trace or
 * previous exception is ever considered public by default.
 */
interface PublicErrorMapperInterface
{
    public function supports(\Throwable $error, Request $request): bool;

    public function map(\Throwable $error, Request $request): ?PublicErrorDescriptor;
}
