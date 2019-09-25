<?php

namespace ild78\tests\unit\Auth;

use ild78;

class Status extends ild78\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->hasConstant('ATTEMPTED')
                ->constant('ATTEMPTED')
                    ->isEqualTo('attempted')

            ->currentlyTestedClass
                ->hasConstant('AVAILABLE')
                ->constant('AVAILABLE')
                    ->isEqualTo('available')

            ->currentlyTestedClass
                ->hasConstant('DECLINED')
                ->constant('DECLINED')
                    ->isEqualTo('declined')

            ->currentlyTestedClass
                ->hasConstant('EXPIRED')
                ->constant('EXPIRED')
                    ->isEqualTo('expired')

            ->currentlyTestedClass
                ->hasConstant('FAILED')
                ->constant('FAILED')
                    ->isEqualTo('failed')

            ->currentlyTestedClass
                ->hasConstant('REQUEST')
                ->constant('REQUEST')
                    ->isEqualTo('request')

            ->currentlyTestedClass
                ->hasConstant('REQUESTED')
                ->constant('REQUESTED')
                    ->isEqualTo('requested')

            ->currentlyTestedClass
                ->hasConstant('SUCCESS')
                ->constant('SUCCESS')
                    ->isEqualTo('success')

            ->currentlyTestedClass
                ->hasConstant('UNAVAILABLE')
                ->constant('UNAVAILABLE')
                    ->isEqualTo('unavailable')
        ;
    }
}
