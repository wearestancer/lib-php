<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;

/**
 * The client must first authenticate itself with a proxy.
 *
 * This represent an 407 HTTP return.
 */
class ProxyAuthenticationRequiredException extends ClientException implements ExceptionInterface
{
    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage() : string
    {
        return 'HTTP 407 - Proxy Authentication Required';
    }
}
