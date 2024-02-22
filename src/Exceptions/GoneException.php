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
    protected static string $defaultMessage = 'Gone';

    protected static string $logLevel = Psr\Log\LogLevel::ERROR;

    protected static string $status = '410';
}
