<?php
declare(strict_types=1);

namespace Stancer\Payment;

/**
 * List of a payment methods allowed.
 */
enum MethodsAllowed: string
{
    case CARD = 'card';
    case SEPA = 'sepa';
}
