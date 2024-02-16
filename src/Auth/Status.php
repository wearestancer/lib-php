<?php
declare(strict_types=1);

namespace Stancer\Auth;

use Stancer;

/**
 * List of a auth status.
 */
#[Stancer\WillChange\PHP8_1\Enumeration]
class Status
{
    public const ATTEMPTED = 'attempted';
    public const AVAILABLE = 'available';
    public const DECLINED = 'declined';
    public const EXPIRED = 'expired';
    public const FAILED = 'failed';
    public const REQUEST = 'request';
    public const REQUESTED = 'requested';
    public const SUCCESS = 'success';
    public const UNAVAILABLE = 'unavailable';
}
