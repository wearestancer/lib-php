<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * Exception thrown for 400 level errors.
 */
class ClientException extends HttpException implements ExceptionInterface
{
    /** @var string */
    protected static $defaultMessage = 'Client error';

    /** @var string Default log level */
    protected static $logLevel = Psr\Log\LogLevel::ERROR;

    /** @var string */
    protected static $status = '4xx';
}
