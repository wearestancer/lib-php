<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;

/**
 * The requested resource is capable of generating only content not acceptable according to the Accept headers.
 *
 * This represent an 406 HTTP return.
 */
class NotAcceptableException extends ClientException implements ExceptionInterface
{
    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage() : string
    {
        return 'HTTP 406 - Not Acceptable';
    }
}
