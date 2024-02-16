<?php
declare(strict_types=1);

namespace Stancer;

/**
 * Helpers.
 */
class Helper
{
    /**
     * Convert `camelCase` text to `snake_case`.
     *
     * @param string $text Text to convert.
     *
     * @return string
     */
    public static function camelCaseToSnakeCase(string $text): string
    {
        $replace = function ($matches): string {
            return '_' . strtolower($matches[0]);
        };

        $rep = preg_replace_callback('`[A-Z]`', $replace, $text);

        if (!$rep) {
            return '';
        }

        return $rep;
    }

    /**
     * Convert `snake_case` text to `camelCase`.
     *
     * @param string $text Text to convert.
     *
     * @return string
     */
    public static function snakeCaseToCamelCase(string $text): string
    {
        $replace = function ($matches): string {
            return strtoupper(ltrim($matches[0], '_'));
        };

        $rep = preg_replace_callback('`_[a-z]`', $replace, $text);

        if (!$rep) {
            return '';
        }

        return $rep;
    }
}
