<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * Indicates that the resource requested is locked and needs some payment.
 *
 * This is not used in API.
 *
 * This represent an 402 HTTP return.
 */
class PaymentRequiredException extends ClientException implements ExceptionInterface
{
    protected static string $defaultMessage = 'Payment Required';

    protected static string $logLevel = Psr\Log\LogLevel::ERROR;

    protected static string $status = '402';
}
