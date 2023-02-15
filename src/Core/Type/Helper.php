<?php
declare(strict_types=1);

namespace Stancer\Core\Type;

use DateTimeInterface;
use DateTimeImmutable;

/**
 * Internal type helper.
 */
class Helper
{
    public const DATE_ONLY = 'dateOnly';
    public const INTEGER_TO_PERCENTAGE = 'integerToPercentage';
    public const PARSE_DATE_TIME = 'parseDateTime';
    public const TO_LOWER = 'toLower';
    public const UNIX_TIMESTAMP = 'unixTimestamp';

    /**
     * Simplify model creation.
     *
     * @param string|null $method Method name.
     *
     * @return callable
     */
    public static function get(?string $method): callable
    {
        if (!is_string($method) || !method_exists(static::class, $method)) {
            return function ($value) {
                return $value;
            };
        }

        return function ($value) use ($method) {
            // @phpstan-ignore-next-line We will allow that here.
            return static::$method($value);
        };
    }

    /**
     * Transform a `DateTime` to an ISO 8601 date only string.
     *
     * @param DateTimeInterface|null $value Date to transform.
     *
     * @return string|null
     */
    public static function dateOnly($value): ?string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return null;
    }

    /**
     * Transform an integer based percentage to a float based percentage.
     *
     * @param integer|null $value Value to transform.
     *
     * @return float|null
     */
    public static function integerToPercentage(?int $value): ?float
    {
        if (is_null($value)) {
            return $value;
        }

        return $value / 100;
    }

    /**
     * Parse an ISO 8601 string as a date or a integer as timestamp.
     *
     * @param string|integer|DateTimeInterface|null $value Value to parse.
     *
     * @return DateTimeInterface|null
     */
    public static function parseDateTime($value): ?DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        if (is_numeric($value)) {
            return new DateTimeImmutable('@' . $value);
        }

        if (is_string($value)) {
            return new DateTimeImmutable($value);
        }

        return null;
    }

    /**
     * Transform a string to lower case.
     *
     * @param string $value String to transform.
     *
     * @return string
     */
    public static function toLower(string $value): string
    {
        return strtolower($value);
    }

    /**
     * Transform DateTime into timestamps.
     *
     * @param DateTimeInterface|null $value DateTime to transform.
     *
     * @return integer|null
     */
    public static function unixTimestamp(?DateTimeInterface $value): ?int
    {
        if ($value instanceof DateTimeInterface) {
            return $value->getTimestamp();
        }

        return $value;
    }
}
