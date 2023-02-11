<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

/**
 * Exception thrown on invalid search order id.
 */
class InvalidSearchOrderIdFilterException extends InvalidSearchFilterException
{
    /**
     * Return default message for that kind of exception.
     *
     * @return string
     */
    public static function getDefaultMessage(): string
    {
        return 'Invalid order ID.';
    }
}
