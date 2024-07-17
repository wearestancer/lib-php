<?php
declare(strict_types=1);

namespace Stancer\Interfaces;

/**
 * Regroup all the Method who must be implemented in payments object.
 */
interface PaymentInterface
{
    /**
     * Capture a Payment(or payment intent).
     *
     * @return static
     * @throws \Stancer\Exceptions\BadRequestException If the payment isn't Capturable.
     */
    public function capture(): static;
}
