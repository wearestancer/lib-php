<?php

namespace ild78\tests\unit\Sepa;

use ild78;
use ild78\Sepa\Check as testedClass;

class Check extends ild78\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(ild78\Core\AbstractObject::class)
        ;
    }

    public function testGetEndpoint()
    {
        $this
            ->string($this->newTestedInstance->getEndpoint())
                ->isIdenticalTo('sepa/check')
        ;
    }

    public function testGetStatus()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->getStatus())
                    ->isNull

                ->variable($this->testedInstance->status)
                    ->isNull

                ->exception(function () {
                    $this->testedInstance->setStatus(uniqid());
                })
                    ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "status".')

            ->if($status = uniqid())
            ->and($this->testedInstance->hydrate(['status' => $status]))
            ->then
                ->string($this->testedInstance->getStatus())
                    ->isIdenticalTo($status)

                ->string($this->testedInstance->status)
                    ->isIdenticalTo($status)
        ;
    }
}
