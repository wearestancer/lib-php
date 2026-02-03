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
     */
    public static function camelCaseToSnakeCase(string $text): string
    {
        $replace = function ($matches): string {
            return '_' . strtolower($matches[0]);
        };

        $rep = preg_replace_callback('`[0-9]*[A-Z]+`', $replace, $text);

        if (!$rep) {
            return '';
        }

        if (strpos($rep, '_') === 0) {
            return substr($rep, 1);
        }

        return $rep;
    }

    /**
     * Convert `snake_case` text to `camelCase`.
     *
     * @param string $text Text to convert.
     */
    public static function snakeCaseToCamelCase(string $text): string
    {
        $replace = function ($matches): string {
            return strtoupper(ltrim($matches[0], '_'));
        };

        $rep = preg_replace_callback('`_([a-z]|(?:[0-9][a-z]+))`', $replace, $text);

        if (!$rep) {
            return '';
        }

        return $rep;
    }
}
