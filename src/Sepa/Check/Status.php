<?php
declare(strict_types=1);

namespace Stancer\Sepa\Check;

/**
 * List of a sepa check status.
 */
class Status
{
    public const AVAILABLE = 'available';
    public const CHECK_ERROR = 'check_error';
    public const CHECK_SENT = 'check_sent';
    public const CHECKED = 'checked';
    public const UNAVAILABLE = 'unavailable';
}
