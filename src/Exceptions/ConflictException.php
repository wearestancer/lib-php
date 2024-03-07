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
    protected static string $defaultMessage = 'Conflict';

    protected static string $logLevel = Psr\Log\LogLevel::ERROR;

    protected static string $status = '409';
}
