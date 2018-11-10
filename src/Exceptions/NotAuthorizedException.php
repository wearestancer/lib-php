<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;

/**
 * The request has not been applied because it lacks valid authentication credentials.
 *
 * This represent an 401 HTTP return on the API.
 */
class NotAuthorizedException extends ClientException implements ExceptionInterface
{
    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage() : string
    {
        return 'You are not authorized to access that resource';
    }
}
