<?php

namespace ild78\tests\unit\Exceptions;

use atoum;
use ild78\Exceptions\Exception as testedClass;

class Exception extends atoum
{
    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf('Exception')
        ;
    }
}
