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
}
