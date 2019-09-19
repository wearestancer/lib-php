<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;
use Psr;

/**
 * Exception thrown on invalid URL.
 *
 * Basically, you provided an HTTP URL instead of an HTTPS one.
 */
class InvalidUrlException extends InvalidArgumentException implements ExceptionInterface
{
    /** @var string Default log level */
    protected static $logLevel = Psr\Log\LogLevel::DEBUG;

    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage() : string
    {
        return 'Invalid URL';
    }
}
