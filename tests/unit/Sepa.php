<?php

namespace ild78\tests\unit;

use atoum;
use ild78\Api;
use ild78\Exceptions;
use ild78\Sepa as testedClass;

class Sepa extends atoum
{
    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf(Api\Object::class)
        ;
    }

    public function testSetBic()
    {
        $range = range(1, 20);

        foreach ($range as $index) {
            $isValid = $index === 8 || $index === 11;
            $message = sprintf('%d chars => %s', $index, $isValid ? 'valid' : 'invalid');

            $this
                ->assert($message)
                    ->given($this->newTestedInstance)
                    ->and($bic = substr(md5(uniqid()), 0, $index))
                    ->then // see below
            ;

            if ($isValid) {
                $this
                    ->variable($this->testedInstance->getBic())
                        ->isNull

                    ->object($this->testedInstance->setBic($bic))
                        ->isTestedInstance

                    ->string($this->testedInstance->getBic())
                        ->isIdenticalTo($bic)
                ;
            } else {
                $this
                    ->exception(function () use ($bic) {
                        $this->testedInstance->setBic($bic);
                    })
                        ->isInstanceOf(Exceptions\InvalidArgumentException::class)
                        ->message
                            ->contains($bic)
                ;
            }
        }
    }
}
