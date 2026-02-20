<?php

namespace Stancer\tests\unit\ThreeDomainsSecure;

use Stancer;

class Status extends Stancer\Tests\atoum
{
    /**
     * @ThreeDomainSecureStatus
     */
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->isEnum()

            ->currentlyTestedClass
                ->hasConstant('NONE')
                ->constant('NONE')
                    ->isEqualTo('none')

            ->currentlyTestedClass
                ->hasConstant('REQUIRED')
                ->constant('REQUIRED')
                    ->isEqualTo('required')
        ;
    }
}
