<?php

declare(strict_types=1);

namespace Stancer\Exceptions;

use Psr;

/**
 * Exception thrown on invalid signature.
 */
class InvalidSignatureException extends Exception
{
    protected static string $logLevel = Psr\Log\LogLevel::WARNING;

    #[\Override]
    public static function getDefaultMessage(): string
    {
        return 'Invalid signature';
    }
}
