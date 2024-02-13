<?php
declare(strict_types=1);

namespace Stancer\Payout;

use Stancer;

/**
 * List of a payout status.
 */
#[Stancer\WillChange\PHP8_1\Enumeration]
class Status
{
    public const FAILED = 'failed';
    public const PAID = 'paid';
    public const PENDING = 'pending';
    public const SENT = 'sent';
    public const TO_PAY = 'to_pay';
}
