<?php

namespace Stancer\Tests\functional;

use Stancer;

/**
 * @namespace \Tests\functional
 */
class Customer extends TestCase
{
    public function testDelete()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($key = $this->getRandomString(10))
            ->and($this->testedInstance->setName('John Doe (' . $key . ')'))
            ->and($this->testedInstance->setEmail('john.doe+' . $key . '@example.com'))
            ->and($this->testedInstance->setMobile($this->getRandomNumber()))
            ->and($id = $this->testedInstance->send()->getId())
            ->then
                ->object($this->testedInstance->delete())
                    ->isTestedInstance

                ->variable($this->testedInstance->getId())
                    ->isNull

                ->exception(function () use ($id) {
                    $this->newTestedInstance($id)->getName();
                })
                    ->isInstanceOf(Stancer\Exceptions\NotFoundException::class)
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
                        ->isInstanceOf(Stancer\Exceptions\NotFoundException::class)
                        ->message
                            ->isIdenticalTo('No such customer ' . $id)

            ->assert('Get test user')
                ->if($this->newTestedInstance('cust_PpdYwq0ZPdoags46d5cB9HpQ'))
                ->then
                    ->string($this->testedInstance->getName())
                        ->isIdenticalTo('John Doe')

                    ->string($this->testedInstance->getEmail())
                        ->isIdenticalTo('john.doe@example.com')

                    ->string($this->testedInstance->getMobile())
                        ->isIdenticalTo('+33666172730') // Random generated number

                    ->string($this->testedInstance->getExternalId())
                        ->isIdenticalTo('6d378a8b-0849-4ab6-96a7-c107bd613852')
        ;
    }

    public function testSend()
    {
        $this
            ->given($key = $this->getRandomString(10))
            ->and($name = 'John Doe (' . $key . ')')
            ->and($email = 'john.doe+' . $key . '@example.com')
            ->and($mobile = $this->getRandomNumber())

            ->assert('Complete customer')
                ->given($this->newTestedInstance)
                ->and($this->testedInstance->setName($name))
                ->and($this->testedInstance->setEmail($email))
                ->and($this->testedInstance->setMobile($mobile))
                ->then
                    ->variable($this->testedInstance->getId())
                        ->isNull

                    ->object($this->testedInstance->send())
                        ->isTestedInstance

                    ->string($id = $this->testedInstance->getId())
                        ->isNotEmpty

            ->assert('Same data throw conflicts')
                ->given($this->newTestedInstance)
                ->and($this->testedInstance->setName($name))
                ->and($this->testedInstance->setEmail($email))
                ->and($this->testedInstance->setMobile($mobile))
                ->then
                    ->exception(function () {
                        $this->testedInstance->send();
                    })
                        ->isInstanceOf(Stancer\Exceptions\ConflictException::class)
                        ->message
                            ->isIdenticalTo('Customer already exists, you may want to update it instead creating a new one (' . $id . ')')

            ->assert('External ID are used in conflicts resolver')
                ->given($externalId = $this->getUuid())

                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setName($name))
                ->and($this->testedInstance->setEmail($email))
                ->and($this->testedInstance->setMobile($mobile))
                ->and($this->testedInstance->setExternalId($externalId))
                ->then
                    ->object($this->testedInstance->send())
                        ->isTestedInstance

                    ->string($withUuid = $this->testedInstance->getId())
                        ->startWith('cust_')
                        ->isNotEqualTo($id)

                ->if($this->newTestedInstance)
                ->and($this->testedInstance->setName($name))
                ->and($this->testedInstance->setEmail($email))
                ->and($this->testedInstance->setMobile($mobile))
                ->and($this->testedInstance->setExternalId($externalId))
                ->then
                    ->exception(function () {
                        $this->testedInstance->send();
                    })
                        ->isInstanceOf(Stancer\Exceptions\ConflictException::class)
                        ->message
                            ->isIdenticalTo('Customer already exists, you may want to update it instead creating a new one (' . $withUuid . ')')

            ->assert('Only email is good')
                ->given($this->newTestedInstance)
                ->and($key = uniqid())
                ->and($this->testedInstance->setEmail('john.doe+' . $key . '@example.com'))
                ->then
                    ->variable($this->testedInstance->getId())
                        ->isNull

                    ->object($this->testedInstance->send())
                        ->isTestedInstance

                    ->string($this->testedInstance->getId())
                        ->isNotEmpty

            ->assert('Only mobile is good')
                ->given($this->newTestedInstance)
                ->and($this->testedInstance->setMobile($this->getRandomNumber()))
                ->then
                    ->variable($this->testedInstance->getId())
                        ->isNull

                    ->object($this->testedInstance->send())
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
            ->and($this->testedInstance->send())

            ->and($id = $this->testedInstance->getId())

            ->and($rand = $this->getRandomString(10))
            ->and($newName = 'John Doe (' . $rand . ')')
            ->and($newEmail = 'john.doe+' . $rand . '@example.com')
            ->and($newMobile = $this->getRandomNumber())
            ->and($newExternalId = $this->getUuid())
            ->then
                ->assert('Change name')
                    ->object($this->testedInstance->setName($newName)->send())
                        ->isTestedInstance

                    ->string($this->newTestedInstance($id)->getName())
                        ->isIdenticalTo($newName)

                ->assert('Change email')
                    ->object($this->testedInstance->setEmail($newEmail)->send())
                        ->isTestedInstance

                    ->string($this->newTestedInstance($id)->getEmail())
                        ->isIdenticalTo($newEmail)

                ->assert('Change mobile phone number')
                    ->object($this->testedInstance->setMobile($newMobile)->send())
                        ->isTestedInstance

                    ->string($this->newTestedInstance($id)->getMobile())
                        ->isIdenticalTo($newMobile)

                ->assert('Change external ID')
                    ->object($this->testedInstance->setExternalId($newExternalId)->send())
                        ->isTestedInstance

                    ->string($this->newTestedInstance($id)->getExternalId())
                        ->isIdenticalTo($newExternalId)
        ;
    }
}
