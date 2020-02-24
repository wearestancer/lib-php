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
     * @return self
     * @throws ild78\Exceptions\InvalidAmountException When the amount is invalid.
     */
    public function setAmount(float $amount): self
    {
        try {
            return parent::setAmount(intval(strval($amount)));
        } catch (ild78\Exceptions\InvalidArgumentException $excep) {
            throw new ild78\Exceptions\InvalidAmountException($excep->getMessage(), $excep->getCode(), $excep);
        }
    }
}
