<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;

/**
 * The request could not be completed due to a conflict with the current state of the target resource.
 *
 * This represent an 409 HTTP return on the API.
 */
class ConflictException extends ClientException implements ExceptionInterface
{
    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage() : string
    {
        return 'HTTP 409 - Conflict';
    }
}
