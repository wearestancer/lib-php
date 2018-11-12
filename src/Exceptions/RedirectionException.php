<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;

/**
 * Exception thrown for 300 level errors.
 */
class RedirectionException extends HttpException implements ExceptionInterface
{
    /** @var string */
    protected static $defaultMessage = 'Redirection';

    /** @var string */
    protected static $status = '3xx';
}
