<?php
namespace OmegaGuardian\ProfilerShield\Security;
final class LoginRateLimited extends \RuntimeException
{
    public function __construct(public readonly int $retryAfter) {parent::__construct('security.blocked');}
}
