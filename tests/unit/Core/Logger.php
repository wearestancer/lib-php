<?php

namespace Stancer\tests\unit\Core;

use Psr;
use Stancer;

class Logger extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->isSubclassOf(Psr\Log\LoggerInterface::class)
        ;
    }

    public function test_methods()
    {
        // stupid test, but nothing better to do here
        $methods = [
            'alert',
            'critical',
            'debug',
            'emergency',
            'error',
            'info',
            'notice',
            'warning',
        ];

        foreach ($methods as $method) {
            $this
                ->given($this->newTestedInstance)
                ->and($params = [])
                ->if($this->function->fopen = true)
                ->and($this->function->file_put_contents = true)
                ->when(function () use (&$params) {
                    for ($idx = 0; $idx < rand(3, 9); $idx++) {
                        $params[] = uniqid();
                    }
                })
                ->then
                    ->variable($this->testedInstance->{$method}(uniqid()))
                        ->isNull

                    ->variable($this->testedInstance->{$method}(uniqid(), $params))
                        ->isNull

                    ->function('fopen') // We do not open any file
                        ->wasCalled->never

                    ->function('file_put_contents') // We do not write in any file
                        ->wasCalled->never
            ;
        }
    }

    public function testLog()
    {
        // This method is not allowed in our implementation.
        // We do not wanted to use one implementation for `$level` or an other.
        // It is simpler to forget it

        $this
            ->exception(function () {
                $this->newTestedInstance->log(uniqid(), uniqid());
            })
                ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('This method is not allowed')
        ;
    }
}
