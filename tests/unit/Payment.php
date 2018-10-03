<?php

namespace ild78\tests\unit;

use atoum;
use ild78\Core;
use ild78\Payment as testedClass;

class Payment extends atoum
{
    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf(Core::class)
        ;
    }
}
