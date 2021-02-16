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
}
