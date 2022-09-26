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
    /** @var string */
    protected static $defaultMessage = 'Forbidden';

    /** @var string Default log level */
    protected static $logLevel = Psr\Log\LogLevel::ERROR;

    /** @var string */
    protected static $status = '403';
}
