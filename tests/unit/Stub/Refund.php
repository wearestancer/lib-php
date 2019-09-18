<?php

namespace ild78\tests\unit\Stub;

use ild78;

class Refund extends ild78\Tests\atoum
{
    public function testIsModified_isNotModified()
    {
        $this
            ->given($payment = new ild78\Stub\Payment)
            ->and($payment->testOnlySetModified(true))

            ->if($this->newTestedInstance)
            ->and($this->testedInstance->setPayment($payment))
            ->and($this->testedInstance->testOnlySetModified(false))
            ->then
                ->boolean($this->testedInstance->isModified())
                    ->isFalse

                ->boolean($this->testedInstance->isNotModified())
                    ->isTrue
        ;
    }
}
