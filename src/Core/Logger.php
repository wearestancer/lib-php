<?php
declare(strict_types=1);

// Next line is required, we can not force type in function signature, it triggers a fatal error.
// phpcs:disable Squiz.Commenting.FunctionComment.ScalarTypeHintMissing

namespace Stancer\Core;

use Psr\Log\LoggerInterface;
use Stancer;
use Stringable;

/**
 * Basic and useless logger.
 */
class Logger implements LoggerInterface
{
    /**
     * System is unusable.
     *
     * @param string|Stringable $message The log message.
     * @param mixed[] $context The log context.
     * @return void
     */
    public function emergency(string|Stringable $message, array $context = []): void
    {
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string|Stringable $message The log message.
     * @param mixed[] $context The log context.
     * @return void
     */
    public function alert(string|Stringable $message, array $context = []): void
    {
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string|Stringable $message The log message.
     * @param mixed[] $context The log context.
     * @return void
     */
    public function critical(string|Stringable $message, array $context = []): void
    {
    }

    /**
     * Runtime errors that do not require immediate action but should typically.
     * be logged and monitored.
     *
     * @param string|Stringable $message The log message.
     * @param mixed[] $context The log context.
     * @return void
     */
    public function error(string|Stringable $message, array $context = []): void
    {
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string|Stringable $message The log message.
     * @param mixed[] $context The log context.
     * @return void
     */
    public function warning(string|Stringable $message, array $context = []): void
    {
    }

    /**
     * Normal but significant events.
     *
     * @param string|Stringable $message The log message.
     * @param mixed[] $context The log context.
     * @return void
     */
    public function notice(string|Stringable $message, array $context = []): void
    {
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string|Stringable $message The log message.
     * @param mixed[] $context The log context.
     * @return void
     */
    public function info(string|Stringable $message, array $context = []): void
    {
    }

    /**
     * Detailed debug information.
     *
     * @param string|Stringable $message The log message.
     * @param mixed[] $context The log context.
     * @return void
     */
    public function debug(string|Stringable $message, array $context = []): void
    {
    }

    /**
     * Logs with an arbitrary level.
     *
     * This method *is not* allowed here.
     *
     * The `$level` can be everything and we can not use it without choosing one for you.
     * Monolog use 100 as an emergency level, where Zend uses 0.
     *
     * It is simpler to forget this method and onl use strictly named method.
     *
     * @param mixed $level The log level.
     * @param string|Stringable $message The log message.
     * @param mixed[] $context The log context.
     * @return void
     * @throws Stancer\Exceptions\BadMethodCallException Every time, on every call ! Do not use this method.
     */
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        throw new Stancer\Exceptions\BadMethodCallException('This method is not allowed');
    }
}
