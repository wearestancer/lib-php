<?php
declare(strict_types=1);

namespace ild78\Exceptions;

/**
 * The request could not be completed due to a conflict with the current state of the target resource.
 *
 * This represent an 409 HTTP return on the API.
 */
class ConflictException extends Exception
{
}
