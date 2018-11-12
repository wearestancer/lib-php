<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;

/**
 * The request was valid, but the server is refusing action.
 *
 * This represent an 403 HTTP return on the API.
 */
class ForbiddenException extends ClientException implements ExceptionInterface
{
    /** @var string */
    protected static $defaultMessage = 'Forbidden';

    /** @var string */
    protected static $status = '403';
}
