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
    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage() : string
    {
        return 'HTTP 408 - Request Timeout';
    }
}
