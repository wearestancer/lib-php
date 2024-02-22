<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * The request was valid, but the server is refusing action.
 *
 * This represent an 403 HTTP return on the API.
 */
class ForbiddenException extends ClientException implements ExceptionInterface
{
    protected static string $defaultMessage = 'Forbidden';

    protected static string $logLevel = Psr\Log\LogLevel::ERROR;

    protected static string $status = '403';
}
