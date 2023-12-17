<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * A request method is not supported for the requested resource.
 *
 * This represent an 405 HTTP return on the API.
 */
class MethodNotAllowedException extends ClientException implements ExceptionInterface
{
    protected static string $defaultMessage = 'Method Not Allowed';

    protected static string $logLevel = Psr\Log\LogLevel::CRITICAL;

    protected static string $status = '405';
}
