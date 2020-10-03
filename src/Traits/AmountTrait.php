<?php

namespace ild78\Traits;

use ild78;

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
     * @throws ild78\Exceptions\InvalidAmountException When the amount is invalid.
     */
    public function setAmount(float $amount): self
    {
        return parent::setAmount(intval(strval($amount)));
    }
}
