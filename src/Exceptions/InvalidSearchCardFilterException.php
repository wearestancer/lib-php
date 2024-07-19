<?php

declare(strict_types=1);

namespace Stancer\Exceptions;

/**
 * Exception thrown on invalid search card.
 */
class InvalidSearchCardFilterException extends InvalidSearchFilterException
{
    /**
     * Return default message for that kind of exception.
     */
    public static function getDefaultMessage(): string
    {
        return 'Invalid card.';
    }
}
