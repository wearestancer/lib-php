<?php

namespace ild78\Http\Verb\tests\unit;

use ild78;

class AbstractVerb extends ild78\Tests\atoum
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
