<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;

/**
 * Exception thrown on miss-validation with expiration month.
 */
class InvalidExpirationMonthException extends InvalidArgumentException implements ExceptionInterface
{
    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage() : string
    {
        return 'Expiration month is invalid.';
    }
}
