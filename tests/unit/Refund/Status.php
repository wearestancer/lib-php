<?php

namespace Stancer\tests\unit\Refund;

use Stancer;

class Status extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->isBackedEnum()

            ->currentlyTestedClass
                ->hasConstant('NOT_HONORED')
                ->constant('NOT_HONORED')
                    ->isEqualTo('not_honored')

            ->currentlyTestedClass
                ->hasConstant('REFUND_SENT')
                ->constant('REFUND_SENT')
                    ->isEqualTo('refund_sent')

            ->currentlyTestedClass
                ->hasConstant('PAYMENT_CANCELED')
                ->constant('PAYMENT_CANCELED')
                    ->isEqualTo('payment_canceled')

            ->currentlyTestedClass
                ->hasConstant('REFUNDED')
                ->constant('REFUNDED')
                    ->isEqualTo('refunded')

            ->currentlyTestedClass
                ->hasConstant('TO_REFUND')
                ->constant('TO_REFUND')
                    ->isEqualTo('to_refund')
        ;
    }
}
