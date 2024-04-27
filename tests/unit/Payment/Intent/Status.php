<?php

namespace Stancer\tests\unit\Payment\Intent;

use Stancer;

class Status extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->isEnum()

            ->currentlyTestedClass
                ->hasConstant('AUTHORIZED')
                ->constant('AUTHORIZED')
                    ->isEqualTo('authorized')

            ->currentlyTestedClass
                ->hasConstant('CANCELED')
                ->constant('CANCELED')
                    ->isEqualTo('canceled')

            ->currentlyTestedClass
                ->hasConstant('CAPTURED')
                ->constant('CAPTURED')
                    ->isEqualTo('captured')

            ->currentlyTestedClass
                ->hasConstant('REQUIRE_AUTHENTICATION')
                ->constant('REQUIRE_AUTHENTICATION')
                    ->isEqualTo('require_authentication')

            ->currentlyTestedClass
                ->hasConstant('REQUIRE_PAYMENT_METHOD')
                ->constant('REQUIRE_PAYMENT_METHOD')
                    ->isEqualTo('require_payment_method')

            ->currentlyTestedClass
                ->hasConstant('UNPAID')
                ->constant('UNPAID')
                    ->isEqualTo('unpaid')
        ;
    }
}
