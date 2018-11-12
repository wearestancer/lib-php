<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;

/**
 * A request method is not supported for the requested resource.
 *
 * This represent an 405 HTTP return on the API.
 */
class MethodNotAllowedException extends ClientException implements ExceptionInterface
{
    /** @var string */
    protected static $defaultMessage = 'Method Not Allowed';

    /** @var string */
    protected static $status = '405';
}
