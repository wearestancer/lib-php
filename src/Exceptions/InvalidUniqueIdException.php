<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * Exception thrown on invalid unique ID.
 */
class InvalidUniqueIdException extends InvalidArgumentException implements ExceptionInterface
{
    protected static string $logLevel = Psr\Log\LogLevel::DEBUG;

    /**
     * Return default message for that kind of exception.
     *
     * @return string
     */
    public static function getDefaultMessage(): string
    {
        return 'Invalid unique ID.';
    }
}
