<?php
declare(strict_types=1);

namespace ild78\Auth;

/**
 * List of a auth status
 */
class Status
{
    const ATTEMPTED = 'attempted';
    const AVAILABLE = 'available';
    const DECLINED = 'declined';
    const EXPIRED = 'expired';
    const FAILED = 'failed';
    const REQUEST = 'request';
    const REQUESTED = 'requested';
    const SUCCESS = 'success';
    const UNAVAILABLE = 'unavailable';
}
