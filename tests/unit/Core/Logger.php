<?php

namespace ild78\tests\unit\Core;

use ild78;
use ild78\Core\Logger as testedClass;
use Psr;

class Logger extends ild78\Tests\atoum
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
                    ->variable($this->testedInstance->$method(uniqid()))
                        ->isNull

                    ->variable($this->testedInstance->$method(uniqid(), $params))
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
            ->given($level = uniqid())
            ->and($message = uniqid())
            ->exception(function () use ($level, $message) {
                $this->newTestedInstance->log($level, $message);
            })
                ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)
                    ->message
                        ->contains('not allowed')
                        ->contains($level)
                        ->notContains($message)
        ;
    }
}
