<?php

namespace ild78\tests\unit;

use atoum;
use ild78\Api;
use ild78\Sepa as testedClass;

class Sepa extends atoum
{
    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf(Api\Object::class)
        ;
    }
}
