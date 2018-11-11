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
    /** @var string */
    protected static $defaultMessage = 'Proxy Authentication Required';

    /** @var string */
    protected static $status = '407';
}
