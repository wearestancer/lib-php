<?php

namespace ild78\tests\unit\Exceptions;

use atoum;
use ild78\Exceptions;
use ild78\Exceptions\TooManyRedirectsException as testedClass;

class TooManyRedirectsException extends atoum
{
    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf(Exceptions\RedirectionException::class)
        ;
    }
}
