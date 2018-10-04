<?php

namespace ild78\tests\unit\Exceptions;

use atoum;
use ild78\Exceptions;
use ild78\Exceptions\ClientException as testedClass;

class ClientException extends atoum
{
    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf(Exceptions\Exception::class)
        ;
    }
}
