<?php

namespace ild78\tests\unit\Exceptions;

use atoum;
use ild78\Exceptions;

class InvalidCurrencyException extends atoum
{
    public function testClass()
    {
        $this
            ->testedClass
                ->isSubclassOf(Exceptions\InvalidArgumentException::class)
        ;
    }
}
