<?php

namespace ild78\Stub;

use ild78;

class Refund extends ild78\Refund
{
    use ild78\Stub\TestMethodTrait;

    public function testOnlySetPayment(ild78\Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }
}
