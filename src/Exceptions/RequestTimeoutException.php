<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;

/**
 * The server timed out waiting for the request.
 *
 * This represent an 408 HTTP return on the API.
 */
class RequestTimeoutException extends ClientException implements ExceptionInterface
{
    /** @var string */
    protected static $defaultMessage = 'Request Timeout';

    /** @var string */
    protected static $status = '408';
}
