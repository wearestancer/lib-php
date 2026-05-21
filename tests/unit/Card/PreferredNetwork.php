<?php

namespace Stancer\tests\unit\Card;

use Stancer;

/**
 *  @tags Card PreferredNetwork
 */
class PreferredNetwork extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->isBackedEnum()

            ->currentlyTestedClass
                ->hasConstant('National')
                ->constant('National')
                    ->isEqualTo('national')

            ->currentlyTestedClass
                ->hasConstant('Visa')
                ->constant('Visa')
                    ->isEqualTo('visa')

            ->currentlyTestedClass
                ->hasConstant('MasterCard')
                ->constant('MasterCard')
                    ->isEqualTo('mastercard')
        ;
    }
}
