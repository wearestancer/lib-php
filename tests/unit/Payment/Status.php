<?php

namespace ild78\tests\unit\Payment;

use ild78;

class Status extends ild78\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->hasConstant('AUTHORIZE')
                ->constant('AUTHORIZE')
                    ->isEqualTo('authorize')

            ->currentlyTestedClass
                ->hasConstant('AUTHORIZED')
                ->constant('AUTHORIZED')
                    ->isEqualTo('authorized')

            ->currentlyTestedClass
                ->hasConstant('CANCELED')
                ->constant('CANCELED')
                    ->isEqualTo('canceled')

            ->currentlyTestedClass
                ->hasConstant('CAPTURE')
                ->constant('CAPTURE')
                    ->isEqualTo('capture')

            ->currentlyTestedClass
                ->hasConstant('CAPTURED')
                ->constant('CAPTURED')
                    ->isEqualTo('captured')

            ->currentlyTestedClass
                ->hasConstant('DISPUTED')
                ->constant('DISPUTED')
                    ->isEqualTo('disputed')

            ->currentlyTestedClass
                ->hasConstant('EXPIRED')
                ->constant('EXPIRED')
                    ->isEqualTo('expired')

            ->currentlyTestedClass
                ->hasConstant('FAILED')
                ->constant('FAILED')
                    ->isEqualTo('failed')

            ->currentlyTestedClass
                ->hasConstant('TO_CAPTURE')
                ->constant('TO_CAPTURE')
                    ->isEqualTo('to_capture')
        ;
    }
}
