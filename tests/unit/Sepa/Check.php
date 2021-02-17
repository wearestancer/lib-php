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

    public function testGetDateBirth()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->getDateBirth())
                    ->isNull

                ->variable($this->testedInstance->dateBirth)
                    ->isNull

                ->exception(function () {
                    $this->testedInstance->setDateBirth(uniqid());
                })
                    ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "dateBirth".')

            ->if($this->testedInstance->hydrate(['dateBirth' => true]))
            ->then
                ->boolean($this->testedInstance->getDateBirth())
                    ->isTrue

                ->boolean($this->testedInstance->dateBirth)
                    ->isTrue

            ->if($this->testedInstance->hydrate(['dateBirth' => false]))
            ->then
                ->boolean($this->testedInstance->getDateBirth())
                    ->isFalse

                ->boolean($this->testedInstance->dateBirth)
                    ->isFalse
        ;
    }

    public function testGetEndpoint()
    {
        $this
            ->string($this->newTestedInstance->getEndpoint())
                ->isIdenticalTo('sepa/check')
        ;
    }

    public function testGetResponse()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->getResponse())
                    ->isNull

                ->variable($this->testedInstance->response)
                    ->isNull

                ->exception(function () {
                    $this->testedInstance->setResponse(uniqid());
                })
                    ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "response".')

            ->if($response = $this->getRandomString(2))
            ->and($this->testedInstance->hydrate(['response' => $response]))
            ->then
                ->string($this->testedInstance->getResponse())
                    ->isIdenticalTo($response)

                ->string($this->testedInstance->response)
                    ->isIdenticalTo($response)
        ;
    }

    public function testGetScoreName()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->getScoreName())
                    ->isNull

                ->variable($this->testedInstance->scoreName)
                    ->isNull

                ->exception(function () {
                    $this->testedInstance->setScoreName(uniqid());
                })
                    ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "scoreName".')

            ->if($score = rand(0, 100))
            ->and($this->testedInstance->hydrate(['scoreName' => $score]))
            ->then
                ->float($this->testedInstance->getScoreName())
                    ->isIdenticalTo($score / 100)

                ->float($this->testedInstance->scoreName)
                    ->isIdenticalTo($score / 100)
        ;
    }

    public function testGetSepa()
    {
        $this
            ->assert('Without ID')
                ->if($this->newTestedInstance)
                ->then
                    ->variable($this->testedInstance->getSepa())
                        ->isNull

                    ->variable($this->testedInstance->sepa)
                        ->isNull

                    ->exception(function () {
                        $this->testedInstance->setSepa(uniqid());
                    })
                        ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "sepa".')

            ->assert('With an ID')
                ->if($id = $this->getRandomString(29))
                ->and($this->newTestedInstance($id))
                ->then
                    ->object($this->testedInstance->getSepa())
                        ->isInstanceOf(ild78\Sepa::class)

                    ->string($this->testedInstance->getSepa()->getId())
                        ->isIdenticalTo($id)

                    ->object($this->testedInstance->sepa)
                        ->isInstanceOf(ild78\Sepa::class)

                    ->string($this->testedInstance->sepa->id)
                        ->isIdenticalTo($id)
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
