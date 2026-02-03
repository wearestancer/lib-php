<?php

declare(strict_types=1);

namespace Stancer\Exceptions;

use Psr;
use Stancer\Interfaces\ExceptionInterface;

/**
 * Exception thrown to indicate range errors during program execution.
 */
class RangeException extends Exception implements ExceptionInterface
{
    protected static string $logLevel = Psr\Log\LogLevel::NOTICE;

    /**
     * Return default message for that kind of exception.
     */
    public static function getDefaultMessage(): string
    {
        return 'Range error';
    }
}
