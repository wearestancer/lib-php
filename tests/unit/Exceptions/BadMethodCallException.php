<?php

namespace ild78\tests\unit\Exceptions;

use atoum;
use ild78\Exceptions;
use ild78\Exceptions\BadMethodCallException as testedClass;

class BadMethodCallException extends atoum
{
    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf(Exceptions\Exception::class)
        ;
    }
}
