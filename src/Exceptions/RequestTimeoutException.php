<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * The server timed out waiting for the request.
 *
 * This represent an 408 HTTP return on the API.
 */
class RequestTimeoutException extends ClientException implements ExceptionInterface
{
    /** @var string */
    protected static $defaultMessage = 'Request Timeout';

    /** @var string Default log level */
    protected static $logLevel = Psr\Log\LogLevel::ERROR;

    /** @var string */
    protected static $status = '408';
}
