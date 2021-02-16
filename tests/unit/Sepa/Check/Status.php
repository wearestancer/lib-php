<?php

namespace ild78\tests\unit\Sepa\Check;

use ild78;

class Status extends ild78\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->hasConstant('AVAILABLE')
                ->constant('AVAILABLE')
                    ->isEqualTo('available')

            ->currentlyTestedClass
                ->hasConstant('CHECK_ERROR')
                ->constant('CHECK_ERROR')
                    ->isEqualTo('check_error')

            ->currentlyTestedClass
                ->hasConstant('CHECK_SENT')
                ->constant('CHECK_SENT')
                    ->isEqualTo('check_sent')

            ->currentlyTestedClass
                ->hasConstant('CHECKED')
                ->constant('CHECKED')
                    ->isEqualTo('checked')

            ->currentlyTestedClass
                ->hasConstant('UNAVAILABLE')
                ->constant('UNAVAILABLE')
                    ->isEqualTo('unavailable')

        ;
    }
}
