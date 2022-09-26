<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * Exception thrown for 500 level errors.
 */
class ServerException extends HttpException implements ExceptionInterface
{
    /** @var string */
    protected static $defaultMessage = 'Server error';

    /** @var string Default log level */
    protected static $logLevel = Psr\Log\LogLevel::CRITICAL;

    /** @var string */
    protected static $status = '5xx';
}
