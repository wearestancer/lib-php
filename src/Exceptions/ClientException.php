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
    protected static string $defaultMessage = 'Client error';

    protected static string $logLevel = Psr\Log\LogLevel::ERROR;

    protected static string $status = '4xx';
}
