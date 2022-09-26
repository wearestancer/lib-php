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
    /** @var string */
    protected static $defaultMessage = 'Not Acceptable';

    /** @var string Default log level */
    protected static $logLevel = Psr\Log\LogLevel::ERROR;

    /** @var string */
    protected static $status = '406';
}
