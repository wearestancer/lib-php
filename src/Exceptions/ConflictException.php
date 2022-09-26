<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * The request could not be completed due to a conflict with the current state of the target resource.
 *
 * This represent an 409 HTTP return on the API.
 */
class ConflictException extends ClientException implements ExceptionInterface
{
    /** @var string */
    protected static $defaultMessage = 'Conflict';

    /** @var string Default log level */
    protected static $logLevel = Psr\Log\LogLevel::ERROR;

    /** @var string */
    protected static $status = '409';
}
