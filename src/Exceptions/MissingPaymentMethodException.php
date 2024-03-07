<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * Exception thrown when no payment method was setted before a pay tentative.
 */
class MissingPaymentMethodException extends BadMethodCallException implements ExceptionInterface
{
    protected static string $logLevel = Psr\Log\LogLevel::ERROR;

    /**
     * Return default message for that kind of exception.
     *
     * @return string
     */
    public static function getDefaultMessage(): string
    {
        return 'You must provide a valid credit card or SEPA account to make a payment.';
    }
}
