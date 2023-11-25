<?php
declare(strict_types=1);

namespace Stancer\Auth;

use Stancer;

/**
 * List of a auth status.
 */
enum Status: string
{
    case ATTEMPTED = 'attempted';
    case AVAILABLE = 'available';
    case DECLINED = 'declined';
    case EXPIRED = 'expired';
    case FAILED = 'failed';
    case REQUEST = 'request';
    case REQUESTED = 'requested';
    case SUCCESS = 'success';
    case UNAVAILABLE = 'unavailable';
}
