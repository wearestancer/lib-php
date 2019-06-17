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
     * @param integer $amount New amount.
     * @return self
     * @throws ild78\Exceptions\InvalidAmountException When the amount is invalid.
     */
    public function setAmount(int $amount) : self
    {
        try {
            return parent::setAmount($amount);
        } catch (ild78\Exceptions\InvalidArgumentException $excep) {
            throw new ild78\Exceptions\InvalidAmountException($excep->getMessage(), $excep->getCode(), $excep);
        }
    }
}
