<?php
declare(strict_types=1);

namespace ild78\Exceptions;

/**
 * Exception thrown when too many redirects are followed.
 *
 * This represent an 310 HTTP return on the API.
 */
class TooManyRedirectsException extends RedirectionException
{
}
