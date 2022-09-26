<?php

namespace Stancer\Stub;

use Stancer;

class Refund extends Stancer\Refund
{
    use Stancer\Stub\TestMethodTrait;

    public function testOnlySetPayment(Stancer\Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }
}
