<?php

namespace Stancer\tests\unit\Sepa\Check;

use Stancer;

class Status extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->isBackedEnum()

            ->currentlyTestedClass
                ->hasConstant('AVAILABLE')
                ->constant('AVAILABLE')
                    ->isEqualTo('available')

            ->currentlyTestedClass
                ->hasConstant('CHECKED')
                ->constant('CHECKED')
                    ->isEqualTo('checked')

            ->currentlyTestedClass
                ->hasConstant('ERROR')
                ->constant('ERROR')
                    ->isEqualTo('check_error')

            ->currentlyTestedClass
                ->hasConstant('SENT')
                ->constant('SENT')
                    ->isEqualTo('check_sent')

            ->currentlyTestedClass
                ->hasConstant('UNAVAILABLE')
                ->constant('UNAVAILABLE')
                    ->isEqualTo('unavailable')

        ;
    }
}
