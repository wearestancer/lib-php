<?php

namespace ild78\tests\unit;

use atoum;
use ild78\Api;
use ild78\Card as testedClass;

class Card extends atoum
{
    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf(Api\Object::class)
        ;
    }
}
