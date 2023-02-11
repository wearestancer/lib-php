<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;

/**
 * Exception thrown on invalid search start.
 */
class InvalidSearchStartException extends InvalidArgumentException implements ExceptionInterface
{
    /**
     * Return default message for that kind of exception.
     *
     * @return string
     */
    public static function getDefaultMessage(): string
    {
        return 'Start must be a positive integer.';
    }
}
