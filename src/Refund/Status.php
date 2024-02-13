<?php
declare(strict_types=1);

namespace Stancer\Refund;

use Stancer;

/**
 * List of a refund status.
 */
#[Stancer\WillChange\PHP8_1\Enumeration]
class Status
{
    public const NOT_HONORED = 'not_honored';
    public const PAYMENT_CANCELED = 'payment_canceled';
    public const REFUND_SENT = 'refund_sent';
    public const REFUNDED = 'refunded';
    public const TO_REFUND = 'to_refund';
}
