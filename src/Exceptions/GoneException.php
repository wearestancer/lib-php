<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;

/**
 * Indicates that the resource requested is no longer available and will not be available again.
 *
 * This represent an 410 HTTP return on the API.
 */
class GoneException extends ClientException implements ExceptionInterface
{
    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage() : string
    {
        return 'HTTP 410 - Gone';
    }
}
