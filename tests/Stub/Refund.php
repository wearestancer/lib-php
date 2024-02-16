<?php

namespace Stancer\Stub;

use Stancer;

class Refund extends Stancer\Refund
{
    use Stancer\Stub\TestMethodTrait;

    public function testOnlySetAmount(int $amount): self
    {
        return $this->hydrate(['amount' => $amount]);
    }

    public function testOnlySetPayment(Stancer\Payment $payment): self
    {
        return $this->hydrate(['payment' => $payment]);
    }
}
