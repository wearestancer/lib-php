<?php

namespace ild78\tests\unit;

use atoum;
use ild78\Card as testedClass;
use ild78\Core;

class Card extends atoum
{
    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf(Core::class)
        ;
    }
}
