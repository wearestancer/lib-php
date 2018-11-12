<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;

/**
 * Exception thrown on invalid amount.
 */
class InvalidAmountException extends InvalidArgumentException implements ExceptionInterface
{
    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage() : string
    {
        return 'Amount must be greater than or equal to 50.';
    }
}
