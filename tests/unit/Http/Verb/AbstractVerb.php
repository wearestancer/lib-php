<?php

namespace ild78\Http\Verb\tests\unit;

use atoum;
use ild78;

class AbstractVerb extends atoum
{
    public function testClass()
    {
        $this
            ->testedClass
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
