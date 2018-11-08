<?php

namespace ild78\tests\functional;

use ild78;

/**
 * @namespace \tests\functional
 */
class Customer extends TestCase
{
    public function testSave()
    {
        $this
            ->assert('Complete customer')
                ->given($this->newTestedInstance)
                ->and($id = uniqid())
                ->and($this->testedInstance->setName('John Doe (' . $id . ')'))
                ->and($this->testedInstance->setEmail('john.doe+' . $id . '@example.com'))
                ->and($this->testedInstance->setMobile($this->getRandomNumber()))
                ->then
                    ->variable($this->testedInstance->getId())
                        ->isNull

                    ->object($this->testedInstance->save())
                        ->isTestedInstance

                    ->string($this->testedInstance->getId())
                        ->isNotEmpty

            ->assert('Only email is good')
                ->given($this->newTestedInstance)
                ->and($id = uniqid())
                ->and($this->testedInstance->setEmail('john.doe+' . $id . '@example.com'))
                ->then
                    ->variable($this->testedInstance->getId())
                        ->isNull

                    ->object($this->testedInstance->save())
                        ->isTestedInstance

                    ->string($this->testedInstance->getId())
                        ->isNotEmpty

            ->assert('Only mobile is good')
                ->given($this->newTestedInstance)
                ->and($id = uniqid())
                ->and($this->testedInstance->setMobile($this->getRandomNumber()))
                ->then
                    ->variable($this->testedInstance->getId())
                        ->isNull

                    ->object($this->testedInstance->save())
                        ->isTestedInstance

                    ->string($this->testedInstance->getId())
                        ->isNotEmpty
        ;
    }
}
