<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * Exception thrown if an argument is not of the expected type.
 */
class InvalidArgumentException extends Exception implements ExceptionInterface
{
    protected static string $logLevel = Psr\Log\LogLevel::NOTICE;

    /**
     * Return default message for that kind of exception.
     *
     * @return string
     */
    public static function getDefaultMessage(): string
    {
        return 'Invalid argument';
    }
}
