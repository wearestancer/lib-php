<?php
declare(strict_types=1);

namespace Stancer\Sepa\Check;

use Stancer;

/**
 * List of a sepa check status.
 */
enum Status: string
{
    case AVAILABLE = 'available';
    case CHECKED = 'checked';
    case ERROR = 'check_error';
    case SENT = 'check_sent';
    case UNAVAILABLE = 'unavailable';
}
