<?php

namespace ild78\Http\tests\unit;

use atoum;
use ild78;

class Client extends atoum
{
    public function test__construct__destruct()
    {
        $this
            ->given($ressource = uniqid())
            ->and($this->function->curl_init = $ressource)
            ->and($this->function->curl_close = true)
            ->then
                ->object($this->newTestedInstance)
                ->function('curl_init')->wasCalled->once
                ->function('curl_close')->wasCalled->never

                ->variable($this->testedInstance->__destruct())
                ->function('curl_close')
                    ->wasCalledWithArguments($ressource)
                        ->once
        ;
    }
}
