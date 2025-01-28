<?php

declare(strict_types=1);

namespace Stancer\Exceptions;

use Psr;
use Stancer\Interfaces\ExceptionInterface;

/**
 * The server did not find a current representation for the target resource.
 *
 * This represent an 404 HTTP return on the API.
 */
class NotFoundException extends ClientException implements ExceptionInterface
{
    protected static string $defaultMessage = 'Not Found';

    protected static string $logLevel = Psr\Log\LogLevel::ERROR;

    protected static string $status = '404';

    /**
     * Return default message for that kind of exception.
     */
    public static function getDefaultMessage(): string
    {
        return 'Resource not found';
    }
}
