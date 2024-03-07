<?php

namespace Stancer\tests\unit\Payout;

use Stancer;

class Status extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->isBackedEnum()

            ->currentlyTestedClass
                ->hasConstant('FAILED')
                ->constant('FAILED')
                    ->isEqualTo('failed')

            ->currentlyTestedClass
                ->hasConstant('PAID')
                ->constant('PAID')
                    ->isEqualTo('paid')

            ->currentlyTestedClass
                ->hasConstant('PENDING')
                ->constant('PENDING')
                    ->isEqualTo('pending')

            ->currentlyTestedClass
                ->hasConstant('SENT')
                ->constant('SENT')
                    ->isEqualTo('sent')

            ->currentlyTestedClass
                ->hasConstant('TO_PAY')
                ->constant('TO_PAY')
                    ->isEqualTo('to_pay')
        ;
    }
}
