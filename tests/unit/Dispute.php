<?php

namespace ild78\tests\unit;

use ild78;
use ild78\Dispute as testedClass;

class Dispute extends ild78\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass()
                ->isSubclassOf(ild78\Api\AbstractObject::class)
                ->hasTrait(ild78\Traits\AmountTrait::class)
        ;
    }

    public function testGetEndpoint()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getEndpoint())
                    ->isIdenticalTo('disputes')
        ;
    }
}
