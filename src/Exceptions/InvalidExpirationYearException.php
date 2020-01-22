<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;
use Psr;

/**
 * Exception thrown on miss-validation with expiration year.
 */
class InvalidExpirationYearException extends InvalidExpirationException implements ExceptionInterface
{
    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage(): string
    {
        return 'Expiration year is invalid.';
    }
}
