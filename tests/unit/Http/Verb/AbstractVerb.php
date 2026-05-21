<?php

namespace Stancer\tests\unit\Http\Verb;

use Stancer;

class AbstractVerb extends Stancer\Tests\atoum
{
    /**
     * @tags AbstractVerb
     */
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->isAbstract
        ;
    }

    /**
     * @tags AbstractVerb
     */
    public function testIsAllowed()
    {
        $this
            ->boolean($this->newTestedInstance->isAllowed())
                ->isFalse

            ->boolean($this->newTestedInstance->isNotAllowed())
                ->isTrue
        ;
    }
}
