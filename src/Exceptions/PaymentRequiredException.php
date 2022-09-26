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
    /** @var string */
    protected static $defaultMessage = 'Payment Required';

    /** @var string Default log level */
    protected static $logLevel = Psr\Log\LogLevel::ERROR;

    /** @var string */
    protected static $status = '402';
}
