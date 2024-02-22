<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * The requested resource is capable of generating only content not acceptable according to the Accept headers.
 *
 * This represent an 406 HTTP return.
 */
class NotAcceptableException extends ClientException implements ExceptionInterface
{
    protected static string $defaultMessage = 'Not Acceptable';

    protected static string $logLevel = Psr\Log\LogLevel::ERROR;

    protected static string $status = '406';
}
