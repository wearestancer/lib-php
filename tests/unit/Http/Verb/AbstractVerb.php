<?php

namespace Stancer\Http\Verb\tests\unit;

use Stancer;

class AbstractVerb extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->isAbstract
        ;
    }

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
