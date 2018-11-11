<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;
use Psr;
use Throwable;

/**
 * Base exception class for all ild78 exceptions.
 *
 * Created for grouping purpose
 */
class Exception extends \Exception implements ExceptionInterface
{
    /** @var string Default log level */
    protected static $logLevel = Psr\Log\LogLevel::NOTICE;

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
     * Create an instance from an array
     *
     * @param array $params Parameters, keys must correspond to exception properties.
     * @return self
     */
    public static function create(array $params = []) : self
    {
        $keys = [
            'message' => '',
            'code' => 0,
            'previous' => null,
        ];

        foreach ($keys as $key => $default) {
            $$key = $default;

            if (array_key_exists($key, $params) && $params[$key]) {
                $$key = $params[$key];
            }
        }

        return new static($message, $code, $previous);
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

    /**
     * Return default log level for that kind of exception
     *
     * @return string
     */
    public static function getLogLevel() : string
    {
        return static::$logLevel;
    }
}
