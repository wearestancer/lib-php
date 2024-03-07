<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * The request has not been applied because it lacks valid authentication credentials.
 *
 * This represent an 401 HTTP return on the API.
 */
class NotAuthorizedException extends ClientException implements ExceptionInterface
{
    protected static string $defaultMessage = 'Unauthorized';

    protected static string $logLevel = Psr\Log\LogLevel::CRITICAL;

    protected static string $status = '401';

    /**
     * Return default message for that kind of exception.
     *
     * @return string
     */
    public static function getDefaultMessage(): string
    {
        return 'You are not authorized to access that resource.';
    }
}
