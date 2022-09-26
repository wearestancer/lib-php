<?php

namespace Stancer\Traits;

use Stancer;

/**
 * Simple trait to handle everything around amounts
 */
trait AmountTrait
{
    /**
     * Update amount
     *
     * We allow float as input to prevent error during converting to integer.
     * We will not use the floating part.
     *
     * @param float $amount New amount.
     * @return $this
     * @throws Stancer\Exceptions\InvalidAmountException When the amount is invalid.
     */
    public function setAmount(float $amount): self
    {
        return parent::setAmount(intval(strval($amount)));
    }
}
