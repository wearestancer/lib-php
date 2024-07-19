<?php

declare(strict_types=1);

namespace Stancer\PaymentIntent;

use Stancer\Traits\CapturableTrait;

/**
 * List of a payment intent status.
 */
enum Status: string
{
    use CapturableTrait;

    case AUTHORIZED = 'authorized';
    case CANCELED = 'canceled';
    case CAPTURED = 'captured';
    case REQUIRE_AUTHENTICATION = 'require_authentication';
    case REQUIRE_PAYMENT_METHOD = 'require_payment_method';
    case UNPAID = 'unpaid';
}
