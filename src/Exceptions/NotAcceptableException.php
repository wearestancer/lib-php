<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use ild78\Interfaces\ExceptionInterface;

/**
 * The requested resource is capable of generating only content not acceptable according to the Accept headers.
 *
 * This represent an 406 HTTP return.
 */
class NotAcceptableException extends ClientException implements ExceptionInterface
{
    /** @var string */
    protected static $defaultMessage = 'Not Acceptable';

    /** @var string */
    protected static $status = '406';
}
