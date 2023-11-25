<?php
declare(strict_types=1);

namespace Stancer\Payout;

use Stancer;

/**
 * List of a payout status.
 */
enum Status: string
{
    case FAILED = 'failed';
    case PAID = 'paid';
    case PENDING = 'pending';
    case SENT = 'sent';
    case TO_PAY = 'to_pay';
}
