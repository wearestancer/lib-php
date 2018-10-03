<?php
declare(strict_types=1);

namespace ild78\Exceptions;

/**
 * The request has not been applied because it lacks valid authentication credentials.
 *
 * This represent an 401 HTTP return on the API.
 */
class NotAuthorizedException extends Exception
{
}
