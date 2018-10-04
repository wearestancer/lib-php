<?php
declare(strict_types=1);

namespace ild78\Exceptions;

/**
 * The server cannot or will not process the request due to something that is perceived to be a client error.
 *
 * This represent an 400 HTTP return on the API.
 */
class BadRequestException extends ClientException
{
}
