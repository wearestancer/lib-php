<?php

namespace Stancer\tests\unit\Payment;

use Stancer;

class MethodsAllowed extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->isBackedEnum()

            ->currentlyTestedClass
                ->hasConstant('CARD')
                ->constant('CARD')
                    ->isEqualTo('card')

            ->currentlyTestedClass
                ->hasConstant('SEPA')
                ->constant('SEPA')
                    ->isEqualTo('sepa')
        ;
    }
}
