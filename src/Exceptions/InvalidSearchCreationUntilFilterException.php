<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

/**
 * Exception thrown on invalid search until a date.
 */
class InvalidSearchCreationUntilFilterException extends InvalidSearchFilterException
{
    /**
     * Return default message for that kind of exception.
     *
     * @return string
     */
    public static function getDefaultMessage(): string
    {
        return 'Created until must be a positive integer or a DateTime object and must be in the past.';
    }
}
