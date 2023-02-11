<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * Exception thrown on miss-validation with expiration month.
 */
class InvalidExpirationMonthException extends InvalidExpirationException implements ExceptionInterface
{
    /**
     * Return default message for that kind of exception.
     *
     * @return string
     */
    public static function getDefaultMessage(): string
    {
        return 'Expiration month is invalid.';
    }
}
