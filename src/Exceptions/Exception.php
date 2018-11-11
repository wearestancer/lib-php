<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;
use Throwable;

/**
 * Base exception class for all ild78 exceptions.
 *
 * Created for grouping purpose
 */
class Exception extends \Exception implements ExceptionInterface
{
    /**
     * Construct the exception
     *
     * @param string $message The Exception message to throw.
     * @param integer $code The Exception code.
     * @param Throwable $previous The previous exception used for the exception chaining.
     */
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message ?: static::getDefaultMessage(), $code, $previous);
    }

    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage() : string
    {
        return 'Unexpected error';
    }
}
