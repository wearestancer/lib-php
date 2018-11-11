<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;

/**
 * An unexpected condition was encountered and no more specific message is suitable.
 *
 * This represent an 500 HTTP return on the API.
 */
class InternalServerErrorException extends ServerException implements ExceptionInterface
{
    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage() : string
    {
        return 'Servor error, please leave a minute to repair it and try again';
    }
}
