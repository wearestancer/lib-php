<?php

namespace Stancer\tests\unit\Sepa;

use Stancer;

class Check extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(Stancer\Core\AbstractObject::class)
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
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
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
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
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
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('You are not allowed to modify "scoreName".')

            ->if($score = rand(1, 99))
            ->and($this->testedInstance->hydrate(['scoreName' => $score]))
            ->then
                ->float($this->testedInstance->getScoreName())
                    ->isIdenticalTo($score / 100)

                ->float($this->testedInstance->scoreName)
                    ->isIdenticalTo($score / 100)

            ->if($this->testedInstance->hydrate(['scoreName' => 1]))
            ->then
                ->float($this->testedInstance->getScoreName())
                    ->isIdenticalTo(0.01)

                ->float($this->testedInstance->scoreName)
                    ->isIdenticalTo(0.01)

            ->if($this->testedInstance->hydrate(['scoreName' => 100]))
            ->then
                ->float($this->testedInstance->getScoreName())
                    ->isIdenticalTo((float) 1)

                ->float($this->testedInstance->scoreName)
                    ->isIdenticalTo((float) 1)
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
                        ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('You are not allowed to modify "sepa".')

            ->assert('With an ID')
                ->if($id = $this->getRandomString(29))
                ->and($this->newTestedInstance($id))
                ->then
                    ->object($this->testedInstance->getSepa())
                        ->isInstanceOf(Stancer\Sepa::class)

                    ->string($this->testedInstance->getSepa()->getId())
                        ->isIdenticalTo($id)

                    ->object($this->testedInstance->sepa)
                        ->isInstanceOf(Stancer\Sepa::class)

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
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
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

    public function testToJson()
    {
        $this
            ->assert('SEPA without ID')
                ->given($bic = $this->getRandomString(8))
                ->and($dateBirth = $this->getRandomDate(1950, 2000))
                ->and($dateMandate = rand(946681200, 1893452400))
                ->and($mandate = $this->getRandomString(34))
                ->and($name = $this->getRandomString(10))

                ->if($bban = rand())
                ->and($country = 'FR')
                ->and($validation = $bban . '1527' . '00') // 15 => F / 27 => R
                ->and($check = sprintf('%02d', 98 - ($validation % 97)))
                ->and($iban = $country . $check . $bban)

                ->if($data = [
                    'bic' => $bic,
                    'dateBirth' => $dateBirth,
                    'dateMandate' => $dateMandate,
                    'iban' => $iban,
                    'mandate' => $mandate,
                    'name' => $name,
                ])
                ->and($sepa = new Stancer\Sepa($data))

                ->if($this->newTestedInstance(['sepa' => $sepa]))
                ->then
                    ->string($json = $this->testedInstance->toJson())

                    ->array(json_decode($json, true))
                        ->hasKeys(['bic', 'date_birth', 'date_mandate', 'iban', 'mandate', 'name'])

                        ->string['bic']
                            ->isIdenticalTo($bic)

                        ->string['date_birth']
                            ->isIdenticalTo($dateBirth)

                        ->integer['date_mandate']
                            ->isIdenticalTo($dateMandate)

                        ->string['iban']
                            ->isIdenticalTo($iban)

                        ->string['mandate']
                            ->isIdenticalTo($mandate)

                        ->string['name']
                            ->isIdenticalTo($name)

            ->assert('SEPA without ID')
                ->given($id = $this->getRandomString(29))
                ->and($bic = $this->getRandomString(8))
                ->and($dateBirth = $this->getRandomDate(1950, 2000))
                ->and($dateMandate = rand(946681200, 1893452400))
                ->and($mandate = $this->getRandomString(34))
                ->and($name = $this->getRandomString(10))

                ->if($bban = rand())
                ->and($country = 'FR')
                ->and($validation = $bban . '1527' . '00') // 15 => F / 27 => R
                ->and($check = sprintf('%02d', 98 - ($validation % 97)))
                ->and($iban = $country . $check . $bban)

                ->if($data = [
                    'id' => $id,
                    'bic' => $bic,
                    'dateBirth' => $dateBirth,
                    'dateMandate' => $dateMandate,
                    'iban' => $iban,
                    'mandate' => $mandate,
                    'name' => $name,
                ])
                ->and($sepa = new Stancer\Sepa($data))

                ->if($this->newTestedInstance(['sepa' => $sepa]))
                ->then
                    ->string($json = $this->testedInstance->toJson())

                    ->array(json_decode($json, true))
                        ->hasKey('id')
                        ->notHasKeys(['bic', 'date_birth', 'date_mandate', 'iban', 'mandate', 'name'])

                        ->string['id']
                            ->isIdenticalTo($id)

            ->assert('No SEPA')
                ->given($response = $this->getRandomString(2))
                ->and($score = rand(1, 100))
                ->and($birth = rand(0, 50) > 50)
                ->and($status = $this->getRandomString(10))

                ->if($data = [
                    'date_birth' => $birth,
                    'response' => $response,
                    'score_name' => $score,
                    'status' => $status,
                ])
                ->and($this->newTestedInstance()->hydrate($data))
                ->then
                    ->string($json = $this->testedInstance->toJson())

                    ->array(json_decode($json, true))
                        ->isEmpty
        ;
    }
}
