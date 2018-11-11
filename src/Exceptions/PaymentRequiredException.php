<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;

/**
 * Indicates that the resource requested is locked and needs some payment.
 *
 * This is not used in API.
 *
 * This represent an 402 HTTP return.
 */
class PaymentRequiredException extends ClientException implements ExceptionInterface
{
    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage() : string
    {
        return 'HTTP 402 - Payment Required';
    }
}
