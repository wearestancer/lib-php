<?php

namespace ild78\tests\unit\Exceptions;

use atoum;
use GuzzleHttp;
use ild78\Exceptions;
use mock;

class HttpException extends atoum
{
    public function testClass()
    {
        $this
            ->testedClass
                ->isSubclassOf(Exceptions\Exception::class)
        ;
    }
}
