<?php
declare(strict_types=1);

namespace ild78\Core\Type;

use DateTimeInterface;
use DateTimeImmutable;

/**
 * Internal type helper.
 */
class Helper
{
    public const PARSE_DATE_TIME = 'parseDateTime';
    public const TO_LOWER = 'toLower';

    /**
     * Simplify model creation.
     *
     * @param string $method Method name.
     *
     * @return Helper function for dataModel.
     */
    public static function get(string $method): callable
    {
        if (!method_exists(static::class, $method)) {
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
     * Parse an ISO 8601 string as a date or a integer as timestamp.
     *
     * @param string|integer|DateTimeInterface $value Value to parse.
     *
     * @return DateTime object corresponding to value.
     */
    public static function parseDateTime($value): ?DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        if (!$value) {
            return null;
        }

        if (is_numeric($value)) {
            return new DateTimeImmutable('@' . $value);
        }

        return new DateTimeImmutable($value);
    }

    /**
     * Transform a string to lower case.
     *
     * @param string $value String to transform.
     *
     * @return Transformed text.
     */
    public static function toLower(string $value): string
    {
        return strtolower($value);
    }
}
