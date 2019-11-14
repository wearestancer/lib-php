<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;
use Psr;

/**
 * Exception thrown when API key are missing.
 */
class MissingApiKeyException extends InvalidArgumentException implements ExceptionInterface
{
    /** @var string Default log level */
    protected static $logLevel = Psr\Log\LogLevel::ERROR;

    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage(): string
    {
        return 'You did not provide valid API key.';
    }
}
