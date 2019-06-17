<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use Psr;

/**
 * Exception thrown on invalid search creation date.
 */
class InvalidSearchCreationFilter extends InvalidSearchFilter
{
    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage() : string
    {
        return 'Created must be a position integer or a DateTime object and must be in the past.';
    }
}
