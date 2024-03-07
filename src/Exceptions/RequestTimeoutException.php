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
    protected static string $defaultMessage = 'Request Timeout';

    protected static string $logLevel = Psr\Log\LogLevel::ERROR;

    protected static string $status = '408';
}
