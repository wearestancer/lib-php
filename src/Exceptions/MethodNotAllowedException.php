<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * A request method is not supported for the requested resource.
 *
 * This represent an 405 HTTP return on the API.
 */
class MethodNotAllowedException extends ClientException implements ExceptionInterface
{
    /** @var string */
    protected static $defaultMessage = 'Method Not Allowed';

    /** @var string Default log level */
    protected static $logLevel = Psr\Log\LogLevel::CRITICAL;

    /** @var string */
    protected static $status = '405';
}
