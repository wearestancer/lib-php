<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;

/**
 * Exception thrown when too many redirects are followed.
 *
 * This represent an 310 HTTP return on the API.
 */
class TooManyRedirectsException extends RedirectionException implements ExceptionInterface
{
    /** @var string */
    protected static $defaultMessage = 'Too Many Redirection';

    /** @var string */
    protected static $status = '310';
}
