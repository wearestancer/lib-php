<?php

namespace ild78\Http\Verb\tests\unit;

use atoum;
use ild78;

class Get extends atoum
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(ild78\Http\Verb\AbstractVerb::class)
        ;
    }
}
