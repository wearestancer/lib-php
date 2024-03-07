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
    protected static string $defaultMessage = 'Proxy Authentication Required';

    protected static string $logLevel = Psr\Log\LogLevel::ERROR;

    protected static string $status = '407';
}
