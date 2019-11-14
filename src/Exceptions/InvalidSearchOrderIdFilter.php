<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use Psr;

/**
 * Exception thrown on invalid search order id.
 */
class InvalidSearchOrderIdFilter extends InvalidSearchFilter
{
    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage(): string
    {
        return 'Invalid order ID.';
    }
}
