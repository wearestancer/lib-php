<?php

namespace ild78\Tests\functional;

use ild78;

/**
 * @namespace \Tests\functional
 */
class Customer extends TestCase
{
    public function testDelete()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($key = uniqid())
            ->and($this->testedInstance->setName('John Doe (' . $key . ')'))
            ->and($this->testedInstance->setEmail('john.doe+' . $key . '@example.com'))
            ->and($this->testedInstance->setMobile($this->getRandomNumber()))
            ->and($id = $this->testedInstance->save()->getId())
            ->then
                ->object($this->testedInstance->delete())
                    ->isTestedInstance

                ->variable($this->testedInstance->getId())
                    ->isNull

                ->exception(function () use ($id) {
                    $this->newTestedInstance($id)->getName();
                })
                    ->isInstanceOf(ild78\Exceptions\NotFoundException::class)
                    ->message
                        ->isIdenticalTo('No such customer ' . $id)
        ;
    }

    public function testGetData()
    {
        $this
            ->assert('Unknown user result a 404 exception')
                ->if($this->newTestedInstance($id = 'cust_' . $this->getRandomString(24)))
                ->then
                    ->exception(function () {
                        $this->testedInstance->getName();
                    })
                        ->isInstanceOf(ild78\Exceptions\NotFoundException::class)
                        ->message
                            ->isIdenticalTo('No such customer ' . $id)

            ->assert('Get test user')
                ->if($this->newTestedInstance('cust_9y1U3mHPd1yPvbx07VBRqd9C'))
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

    public function testUpdate()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($this->testedInstance->setMobile($this->getRandomNumber()))
            ->and($this->testedInstance->save())

            ->and($id = $this->testedInstance->getId())

            ->and($rand = uniqid())
            ->and($newName = 'John Doe (' . $rand . ')')
            ->and($newEmail = 'john.doe+' . $rand . '@example.com')
            ->and($newMobile = $this->getRandomNumber())
            ->then
                ->assert('Change name')
                    ->object($this->testedInstance->setName($newName)->save())
                        ->isTestedInstance

                    ->string($this->newTestedInstance($id)->getName())
                        ->isIdenticalTo($newName)

                ->assert('Change email')
                    ->object($this->testedInstance->setEmail($newEmail)->save())
                        ->isTestedInstance

                    ->string($this->newTestedInstance($id)->getEmail())
                        ->isIdenticalTo($newEmail)

                ->assert('Change mobile phone number')
                    ->object($this->testedInstance->setMobile($newMobile)->save())
                        ->isTestedInstance

                    ->string($this->newTestedInstance($id)->getMobile())
                        ->isIdenticalTo($newMobile)
        ;
    }
}
