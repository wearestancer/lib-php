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
    /** @var string */
    protected static $defaultMessage = 'Too Many Redirection';

    /** @var string Default log level */
    protected static $logLevel = Psr\Log\LogLevel::CRITICAL;

    /** @var string */
    protected static $status = '310';
}
