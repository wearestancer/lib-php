<?php

declare(strict_types=1);

namespace Stancer\Exceptions;

class InvalidEventException extends Exception
{
    protected static string $logLevel = \Psr\Log\LogLevel::WARNING;

    #[\Override]
    public static function getDefaultMessage(): string
    {
        return 'Data supplied are not valid event data,make sure that the data supplied come from an event request.';
    }
}
