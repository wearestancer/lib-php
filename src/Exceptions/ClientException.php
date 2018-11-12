<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;

/**
 * Exception thrown for 400 level errors.
 */
class ClientException extends HttpException implements ExceptionInterface
{
    /** @var string */
    protected static $defaultMessage = 'Client error';

    /** @var string */
    protected static $status = '4xx';
}
