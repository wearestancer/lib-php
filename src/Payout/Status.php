<?php
declare(strict_types=1);

namespace Stancer\Payout;

/**
 * List of a payout status.
 */
class Status
{
    public const FAILED = 'failed';
    public const PAID = 'paid';
    public const PENDING = 'pending';
    public const SENT = 'sent';
    public const TO_PAY = 'to_pay';
}
