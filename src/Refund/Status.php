<?php
declare(strict_types=1);

namespace Stancer\Refund;

use Stancer;

/**
 * List of a refund status.
 */
enum Status: string
{
    case NOT_HONORED = 'not_honored';
    case PAYMENT_CANCELED = 'payment_canceled';
    case REFUND_SENT = 'refund_sent';
    case REFUNDED = 'refunded';
    case TO_REFUND = 'to_refund';
}
