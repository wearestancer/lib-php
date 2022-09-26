<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * Exception thrown for 300 level errors.
 */
class RedirectionException extends HttpException implements ExceptionInterface
{
    /** @var string */
    protected static $defaultMessage = 'Redirection';

    /** @var string Default log level */
    protected static $logLevel = Psr\Log\LogLevel::WARNING;

    /** @var string */
    protected static $status = '3xx';
}
