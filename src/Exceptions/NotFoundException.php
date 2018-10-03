<?php
declare(strict_types=1);

namespace ild78\Exceptions;

/**
 * The server did not find a current representation for the target resource.
 *
 * This represent an 404 HTTP return on the API.
 */
class NotFoundException extends Exception
{
}
