<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;

/**
 * Exception thrown when no payment method was setted before a pay tentative.
 */
class MissingPaymentMethodException extends BadMethodCallException implements ExceptionInterface
{
    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage() : string
    {
        return 'You must provide a valid credit card or SEPA account to make a payment.';
    }
}
