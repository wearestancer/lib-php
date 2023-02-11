<?php
declare(strict_types=1);

namespace Stancer\Auth;

/**
 * List of a auth status.
 */
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
