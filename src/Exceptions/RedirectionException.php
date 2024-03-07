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
    protected static string $defaultMessage = 'Redirection';

    protected static string $logLevel = Psr\Log\LogLevel::WARNING;

    protected static string $status = '3xx';
}
