<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * Exception thrown when too many redirects are followed.
 *
 * This represent an 310 HTTP return on the API.
 */
class TooManyRedirectsException extends RedirectionException implements ExceptionInterface
{
    protected static string $defaultMessage = 'Too Many Redirection';

    protected static string $logLevel = Psr\Log\LogLevel::CRITICAL;

    protected static string $status = '310';
}
