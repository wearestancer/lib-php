<?php
declare(strict_types=1);

namespace Stancer\Interfaces;

/**
 * Regroup all the Method who must be implemented in payments object.
 */
interface PaymentInterface
{
    public function capture(): static;
}
