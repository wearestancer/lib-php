<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * Indicates that the resource requested is no longer available and will not be available again.
 *
 * This represent an 410 HTTP return on the API.
 */
class GoneException extends ClientException implements ExceptionInterface
{
    /** @var string */
    protected static $defaultMessage = 'Gone';

    /** @var string Default log level */
    protected static $logLevel = Psr\Log\LogLevel::ERROR;

    /** @var string */
    protected static $status = '410';
}
