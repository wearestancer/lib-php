<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * The client must first authenticate itself with a proxy.
 *
 * This represent an 407 HTTP return.
 */
class ProxyAuthenticationRequiredException extends ClientException implements ExceptionInterface
{
    /** @var string */
    protected static $defaultMessage = 'Proxy Authentication Required';

    /** @var string Default log level */
    protected static $logLevel = Psr\Log\LogLevel::ERROR;

    /** @var string */
    protected static $status = '407';
}
