<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;

/**
 * Exception thrown for 500 level errors.
 */
class ServerException extends HttpException implements ExceptionInterface
{
    /** @var string */
    protected static $defaultMessage = 'Server error';

    /** @var string */
    protected static $status = '5xx';
}
