<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * An unexpected condition was encountered and no more specific message is suitable.
 *
 * This represent an 500 HTTP return on the API.
 */
class InternalServerErrorException extends ServerException implements ExceptionInterface
{
    protected static string $defaultMessage = 'Internal Server Error';

    protected static string $logLevel = Psr\Log\LogLevel::CRITICAL;

    protected static string $status = '500';

    /**
     * Return default message for that kind of exception.
     *
     * @return string
     */
    public static function getDefaultMessage(): string
    {
        return 'Server error, please leave a minute to repair it and try again';
    }
}
