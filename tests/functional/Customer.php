<?php

namespace ild78\tests\functional;

use ild78;

/**
 * @namespace \tests\functional
 */
class Customer extends TestCase
{
    public function testGetData()
    {
        $this
            ->assert('Unknonw user result a 404 exception')
                ->if($this->newTestedInstance(md5(uniqid())))
                ->then
                    ->exception(function () {
                        $this->testedInstance->getName();
                    })
                        ->isInstanceOf(ild78\Exceptions\NotFoundException::class)
                        ->message
                            ->isIdenticalTo('Resource not found')

            ->assert('Get test user')
                ->if($this->newTestedInstance('cust_aEBF8w2szNdhreJ0uEmrplen'))
                ->then
                    ->string($this->testedInstance->getName())
                        ->isIdenticalTo('John Doe')

                    ->string($this->testedInstance->getEmail())
                        ->isIdenticalTo('john.doe@example.com')

                    ->string($this->testedInstance->getMobile())
                        ->isIdenticalTo('+33666172730') // Random generated number
        ;
    }

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
