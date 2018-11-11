<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;

/**
 * The request was valid, but the server is refusing action.
 *
 * This represent an 403 HTTP return on the API.
 */
class ForbiddenException extends ClientException implements ExceptionInterface
{
    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage() : string
    {
        return 'HTTP 403 - Forbidden';
    }
}
