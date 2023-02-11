<?php

namespace Stancer\Interfaces;

use Throwable;

/**
 * Regrouping every exceptions.
 */
interface ExceptionInterface
{
    /**
     * Construct the exception.
     *
     * @param string|null $message The Exception message to throw.
     * @param integer $code The Exception code.
     * @param Throwable|null $previous The previous exception used for the exception chaining.
     */
    public function __construct(string $message = null, int $code = 0, Throwable $previous = null);

    /**
     * Return default message for that kind of exception.
     *
     * @return string
     */
    public static function getDefaultMessage(): string;

    /**
     * Return default log level for that kind of exception.
     *
     * @return string
     */
    public static function getLogLevel(): string;
}
