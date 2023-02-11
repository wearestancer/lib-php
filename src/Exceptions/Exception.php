<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;
use Throwable;

/**
 * Base exception class for all Stancer exceptions.
 *
 * Created for grouping purpose.
 */
class Exception extends \Exception implements ExceptionInterface
{
    /** @var string Default log level */
    protected static $logLevel = Psr\Log\LogLevel::NOTICE;

    /**
     * Construct the exception.
     *
     * @param string|null $message The Exception message to throw.
     * @param integer $code The Exception code.
     * @param Throwable|null $previous The previous exception used for the exception chaining.
     */
    public function __construct(string $message = null, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message ?? static::getDefaultMessage(), $code, $previous);
    }

    /**
     * Create an instance from an array.
     *
     * @param array $params Parameters, keys must correspond to exception properties.
     * @return static
     *
     * @phpstan-param CreateExceptionParameters $params
     */
    public static function create(array $params = []): self
    {
        $code = 0;
        $message = null;
        $previous = null;

        if (array_key_exists('code', $params) && $params['code']) {
            $code = $params['code'];
        }

        if (array_key_exists('message', $params) && $params['message']) {
            $message = $params['message'];
        }

        if (array_key_exists('previous', $params) && $params['previous']) {
            $previous = $params['previous'];
        }

        return new static($message, $code, $previous);
    }

    /**
     * Return default message for that kind of exception.
     *
     * @return string
     */
    public static function getDefaultMessage(): string
    {
        return 'Unexpected error';
    }

    /**
     * Return default log level for that kind of exception.
     *
     * @return string
     */
    public static function getLogLevel(): string
    {
        return static::$logLevel;
    }
}
