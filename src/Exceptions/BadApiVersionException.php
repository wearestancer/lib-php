<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Exceptions\Exception;

/**
 * Error Raised when The user try to use an endpoint not available to his API version
 */
class BadApiVersionException extends Exception
{
    public string $defaultMessage = "You're Api version doesn't permit you to do that";
}
