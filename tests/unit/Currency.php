<?php

namespace Stancer\tests\unit;

use Stancer;

class Currency extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->isBackedEnum()

            ->currentlyTestedClass
                ->HASCONSTANT('AUD')
                ->constant('AUD')
                    ->isEqualTo('aud')

            ->currentlyTestedClass
                ->HASCONSTANT('CAD')
                ->constant('CAD')
                    ->isEqualTo('cad')

            ->currentlyTestedClass
                ->HASCONSTANT('CHF')
                ->constant('CHF')
                    ->isEqualTo('chf')

            ->currentlyTestedClass
                ->HASCONSTANT('DKK')
                ->constant('DKK')
                    ->isEqualTo('dkk')

            ->currentlyTestedClass
                ->HASCONSTANT('EUR')
                ->constant('EUR')
                    ->isEqualTo('eur')

            ->currentlyTestedClass
                ->HASCONSTANT('GBP')
                ->constant('GBP')
                    ->isEqualTo('gbp')

            ->currentlyTestedClass
                ->HASCONSTANT('JPY')
                ->constant('JPY')
                    ->isEqualTo('jpy')

            ->currentlyTestedClass
                ->HASCONSTANT('NOK')
                ->constant('NOK')
                    ->isEqualTo('nok')

            ->currentlyTestedClass
                ->HASCONSTANT('PLN')
                ->constant('PLN')
                    ->isEqualTo('pln')

            ->currentlyTestedClass
                ->HASCONSTANT('SEK')
                ->constant('SEK')
                    ->isEqualTo('sek')

            ->currentlyTestedClass
                ->HASCONSTANT('USD')
                ->constant('USD')
                    ->isEqualTo('usd')
        ;
    }
}
