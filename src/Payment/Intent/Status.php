<?php
declare(strict_types=1);

namespace Stancer\Payment\Intent;

/**
 * List of a payment intent status.
 */
enum Status: string
{
    case AUTHORIZED = 'authorized';
    case CANCELED = 'canceled';
    case CAPTURED = 'captured';
    case REQUIRE_AUTHENTICATION = 'require_authentication';
    case REQUIRE_PAYMENT_METHOD = 'require_payment_method';
    case UNPAID = 'unpaid';

    public function isCapturable():bool
    {
        return match($this){
            static::AUTHORIZED => true,
            default => false,
        };
    }
}
