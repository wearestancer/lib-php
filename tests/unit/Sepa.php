<?php

namespace Stancer\tests\unit;

use Stancer;
use Stancer\Sepa as testedClass;
use mock;

class Sepa extends Stancer\Tests\atoum
{
    use Stancer\Tests\Provider\Banks;

    public function testCheck()
    {
        $this
            ->given($config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setDebug(false))

            ->assert('Without ID')
                ->if($this->newTestedInstance)
                ->then
                    ->variable($this->testedInstance->getCheck())
                        ->isNull

                    ->variable($this->testedInstance->check)
                        ->isNull

            ->assert('With ID and without data')
                ->given($client = new mock\Stancer\Http\Client)
                ->and($this->calling($client)->request->throw = new Stancer\Exceptions\NotFoundException)
                ->and($config->setHttpClient($client))

                ->if($id = $this->getRandomString(29))
                ->and($this->newTestedInstance($id))
                ->then
                    ->variable($this->testedInstance->getCheck())
                        ->isNull

                    ->variable($this->testedInstance->check)
                        ->isNull

            ->assert('With ID and with validation data')
                ->given($client = new mock\Stancer\Http\Client)
                ->and($response = new mock\Stancer\Http\Response(200))
                ->and($body = file_get_contents(__DIR__ . '/fixtures/sepa/check/read.json'))
                ->and($this->calling($response)->getBody = new Stancer\Http\Stream($body))
                ->and($this->calling($client)->request = $response)
                ->and($config->setHttpClient($client))

                ->if($id = $this->getRandomString(29))
                ->and($this->newTestedInstance($id))
                ->then
                    // Method based style
                    ->object($this->testedInstance->getCheck())
                        ->isInstanceOf(Stancer\Sepa\Check::class)

                    ->string($this->testedInstance->getCheck()->getId())
                        ->isIdenticalTo('sepa_fZvOCm7oDmUJhqvezEtlZwXa')

                    ->boolean($this->testedInstance->getCheck()->getDateBirth())
                        ->isTrue

                    ->string($this->testedInstance->getCheck()->getResponse())
                        ->isIdenticalTo('00')

                    ->float($this->testedInstance->getCheck()->getScoreName())
                        ->isIdenticalTo(0.32)

                    ->string($this->testedInstance->getCheck()->getStatus())
                        ->isIdenticalTo(Stancer\Sepa\Check\Status::CHECKED)

                    // Property based style
                    ->object($this->testedInstance->check)
                        ->isInstanceOf(Stancer\Sepa\Check::class)

                    ->string($this->testedInstance->check->id)
                        ->isIdenticalTo('sepa_fZvOCm7oDmUJhqvezEtlZwXa')

                    ->boolean($this->testedInstance->check->dateBirth)
                        ->isTrue

                    ->string($this->testedInstance->check->response)
                        ->isIdenticalTo('00')

                    ->float($this->testedInstance->check->scoreName)
                        ->isIdenticalTo(0.32)

                    ->string($this->testedInstance->check->status)
                        ->isIdenticalTo(Stancer\Sepa\Check\Status::CHECKED)
        ;
    }

    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(Stancer\Core\AbstractObject::class)
                ->implements(Stancer\Interfaces\PaymentMeansInterface::class)
        ;
    }

    public function testDateBirth()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($dateBirth = $this->getRandomDate(1950, 2000))
            ->then
                ->variable($this->testedInstance->getDateBirth())
                    ->isNull

                ->object($this->testedInstance->setDateBirth($dateBirth))
                    ->isTestedInstance

                ->dateTime($date = $this->testedInstance->getDateBirth())

                ->variable($date->format('Y-m-d'))
                    ->isEqualTo($dateBirth)

                ->boolean($this->testedInstance->isModified())
                    ->isTrue

                ->array($this->testedInstance->jsonSerialize())
                    ->hasSize(1)
                    ->hasKey('date_birth')
                    ->string['date_birth']
                        ->isEqualTo($dateBirth)
        ;
    }

    public function testDateMandate()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($dateMandate = rand(946681200, 1893452400))
            ->then
                ->variable($this->testedInstance->getDateMandate())
                    ->isNull

                ->object($this->testedInstance->setDateMandate($dateMandate))
                    ->isTestedInstance

                ->dateTime($date = $this->testedInstance->getDateMandate())

                ->variable($date->format('U'))
                    ->isEqualTo($dateMandate)

                ->boolean($this->testedInstance->isModified())
                    ->isTrue

                ->array($this->testedInstance->jsonSerialize())
                    ->hasSize(1)
                    ->hasKey('date_mandate')
                    ->integer['date_mandate']
                        ->isEqualTo($dateMandate)
        ;
    }

    public function testGetEndpoint()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getEndpoint())
                    ->isIdenticalTo('sepa')
        ;
    }

    /**
     * @dataProvider ibanDataProvider
     */
    public function testGetFormattedIban($iban)
    {
        $this
            ->given($this->newTestedInstance)
            ->and($withoutSpaces = str_replace(' ', '', $iban))
            ->and($withSpaces = trim(chunk_split($withoutSpaces, 4, ' ')))
            ->then
                ->variable($this->testedInstance->getIban())
                    ->isNull

                ->variable($this->testedInstance->getFormattedIban())
                    ->isNull

                ->object($this->testedInstance->setIban($iban))
                    ->isTestedInstance

                ->string($this->testedInstance->getIban())
                    ->isIdenticalTo($withoutSpaces)

                ->string($this->testedInstance->getFormattedIban())
                    ->isIdenticalTo($withSpaces)
        ;
    }

    public function testMandate()
    {
        $mandate = '';

        for ($idx = 0; $idx < 40; $idx++) {
            $length = strlen($mandate);

            if ($length < 3 || $length > 35) {
                $this
                    ->assert($length . ' characters => Not valid')
                        ->exception(function () use ($mandate) {
                            $this->newTestedInstance->setMandate($mandate);
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidMandateException::class)
                            ->message
                                ->isIdenticalTo('A valid mandate must be between 3 and 35 characters.')

                        ->boolean($this->testedInstance->isModified())
                            ->isFalse
                ;
            } else {
                $this
                    ->assert($length . ' characters => Valid')
                        ->variable($this->newTestedInstance->getMandate())
                            ->isNull

                        ->object($this->testedInstance->setMandate($mandate))
                            ->isTestedInstance

                        ->string($this->testedInstance->getMandate())
                            ->isIdenticalTo($mandate)

                        ->boolean($this->testedInstance->isModified())
                            ->isTrue

                        ->array($this->testedInstance->jsonSerialize())
                            ->hasSize(1)
                            ->hasKey('mandate')
                            ->string['mandate']
                                ->isEqualTo($mandate)
                ;
            }

            $mandate .= chr(rand(65, 90));
        }
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

                    ->boolean($this->testedInstance->isModified())
                        ->isTrue

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasSize(1)
                        ->hasKey('bic')
                        ->string['bic']
                            ->isEqualTo($bic)
                ;
            } else {
                $this
                    ->exception(function () use ($bic) {
                        $this->testedInstance->setBic($bic);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidBicException::class)
                        ->message
                            ->contains($bic)

                    ->boolean($this->testedInstance->isModified())
                        ->isFalse
                ;
            }
        }
    }

    public function testSetIban()
    {
        $this
            ->assert('Add a valid french random IBAN')
                ->if($bban = rand())
                ->and($country = 'FR')
                ->and($validation = $bban . '1527' . '00') // 15 => F / 27 => R
                ->and($check = sprintf('%02d', 98 - ($validation % 97)))
                ->and($iban = $country . $check . $bban)
                ->and($last = substr($bban, -4))

                ->if($this->newTestedInstance)
                ->then
                    ->variable($this->testedInstance->getCountry())
                        ->isNull

                    ->variable($this->testedInstance->getIban())
                        ->isNull

                    ->variable($this->testedInstance->getLast4())
                        ->isNull

                    ->object($this->testedInstance->setIban($iban))
                        ->isTestedInstance

                    ->string($this->testedInstance->getCountry())
                        ->isIdenticalTo($country)

                    ->string($this->testedInstance->getIban())
                        ->isIdenticalTo($iban)

                    ->string($this->testedInstance->getLast4())
                        ->isIdenticalTo($last)

                    ->boolean($this->testedInstance->isModified())
                        ->isTrue

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasSize(1)
                        ->hasKey('iban')
                        ->string['iban']
                            ->isEqualTo($iban)

            ->assert('Add a valid BE IBAN from wikipedia')
                ->given($iban = 'BE71096123456769')
                ->and($last = '6769')
                ->and($country = substr($iban, 0, 2))
                ->and($this->newTestedInstance)
                ->then
                    ->variable($this->testedInstance->getCountry())
                        ->isNull

                    ->variable($this->testedInstance->getIban())
                        ->isNull

                    ->variable($this->testedInstance->getLast4())
                        ->isNull

                    ->object($this->testedInstance->setIban($iban))
                        ->isTestedInstance

                    ->string($this->testedInstance->getCountry())
                        ->isIdenticalTo($country)

                    ->string($this->testedInstance->getIban())
                        ->isIdenticalTo($iban)

                    ->string($this->testedInstance->getLast4())
                        ->isIdenticalTo($last)

                    ->boolean($this->testedInstance->isModified())
                        ->isTrue

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasSize(1)
                        ->hasKey('iban')
                        ->string['iban']
                            ->isEqualTo($iban)

            ->assert('Add a valid GB IBAN from wikipedia')
                ->given($iban = 'GB82WEST12345698765432')
                ->and($last = '5432')
                ->and($country = substr($iban, 0, 2))
                ->and($this->newTestedInstance)
                ->then
                    ->variable($this->testedInstance->getCountry())
                        ->isNull

                    ->variable($this->testedInstance->getIban())
                        ->isNull

                    ->variable($this->testedInstance->getLast4())
                        ->isNull

                    ->object($this->testedInstance->setIban($iban))
                        ->isTestedInstance

                    ->string($this->testedInstance->getCountry())
                        ->isIdenticalTo($country)

                    ->string($this->testedInstance->getIban())
                        ->isIdenticalTo($iban)

                    ->string($this->testedInstance->getLast4())
                        ->isIdenticalTo($last)

                    ->boolean($this->testedInstance->isModified())
                        ->isTrue

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasSize(1)
                        ->hasKey('iban')
                        ->string['iban']
                            ->isEqualTo($iban)

            ->assert('It should accept space for readeability')
                ->given($iban = 'GR96 0810 0010 0000 0123 4567 890')
                ->and($last = '7890')
                ->and($country = substr($iban, 0, 2))
                ->and($this->newTestedInstance)
                ->then
                    ->variable($this->testedInstance->getCountry())
                        ->isNull

                    ->variable($this->testedInstance->getIban())
                        ->isNull

                    ->variable($this->testedInstance->getLast4())
                        ->isNull

                    ->object($this->testedInstance->setIban($iban))
                        ->isTestedInstance

                    ->string($this->testedInstance->getCountry())
                        ->isIdenticalTo($country)

                    ->string($this->testedInstance->getIban())
                        ->isIdenticalTo(str_replace(' ', '', $iban))

                    ->string($this->testedInstance->getLast4())
                        ->isIdenticalTo($last)

                    ->boolean($this->testedInstance->isModified())
                        ->isTrue

                    ->array($this->testedInstance->jsonSerialize())
                        ->hasSize(1)
                        ->hasKey('iban')
                        ->string['iban']
                            ->isEqualTo(str_replace(' ', '', $iban))

            ->assert('Add an invalid IBAN')
                ->given($iban = 'FR87BARC20658244971655')
                ->and($this->newTestedInstance)
                ->then
                    ->exception(function () use ($iban) {
                        $this->testedInstance->setIban($iban);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidIbanException::class)
                        ->message
                            ->contains($iban)

                    ->boolean($this->testedInstance->isModified())
                        ->isFalse
        ;
    }

    public function testSetName()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->exception(function () {
                    $this->testedInstance->setName('');
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidNameException::class)
                    ->message
                        ->isIdenticalTo('A valid name must be between 4 and 64 characters.')

                ->boolean($this->testedInstance->isModified())
                    ->isFalse
        ;
    }

    public function testValidate()
    {
        $this
            ->given($config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]))
            ->and($config->setDebug(false))
            ->and($options = [
                'headers' => [
                    'Authorization' => $config->getBasicAuthHeader(),
                    'Content-Type' => 'application/json',
                    'User-Agent' => $config->getDefaultUserAgent(),
                ],
                'timeout' => $config->getTimeout(),
            ])

            ->assert('Ask for verification at SEPA creation')
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
                    'date_birth' => $dateBirth,
                    'date_mandate' => $dateMandate,
                    'iban' => $iban,
                    'mandate' => $mandate,
                    'name' => $name,
                ])
                ->and($this->newTestedInstance($data))

                ->if($checkResponse = new mock\Stancer\Http\Response(200))
                ->and($body = file_get_contents(__DIR__ . '/fixtures/sepa/check/create.json'))
                ->and($this->calling($checkResponse)->getBody = new Stancer\Http\Stream($body))

                ->and($sepaResponse = new mock\Stancer\Http\Response(200))
                ->and($body = file_get_contents(__DIR__ . '/fixtures/sepa/read.json'))
                ->and($this->calling($sepaResponse)->getBody = new Stancer\Http\Stream($body))

                ->if($client = new mock\Stancer\Http\Client)
                ->and($this->calling($client)->request[] = $checkResponse)
                ->and($this->calling($client)->request[] = $sepaResponse)
                ->and($config->setHttpClient($client))

                ->if($checkLocation = (new Stancer\Sepa\Check())->getUri())
                ->and($sepaLocation = (new Stancer\Sepa('sepa_bIvCZePYqfMlU11TANT8IqL1'))->getUri())
                ->then
                    // Sepa object
                    ->object($this->testedInstance->validate())
                        ->isTestedInstance

                    ->string($this->testedInstance->getId())
                        ->isIdenticalTo('sepa_bIvCZePYqfMlU11TANT8IqL1')

                    ->dateTime($this->testedInstance->getCreationDate()) // First to cheat on auto-populate
                        ->hasDate(2020, 9, 25)
                        ->hasTime(14, 56, 17)
                        ->isImmutable

                    ->string($this->testedInstance->getBic())
                        ->isIdenticalTo('TESTSEPP')

                    ->dateTime($this->testedInstance->getDateBirth())
                        ->hasDate(1977, 5, 25)
                        ->hasTime(0, 0, 0)
                        ->isImmutable

                    ->dateTime($this->testedInstance->getDateMandate())
                        ->hasDate(2020, 9, 25)
                        ->hasTime(14, 55, 28)
                        ->isImmutable

                    ->string($this->testedInstance->getLast4())
                        ->isIdenticalTo('0003')

                    ->string($this->testedInstance->getMandate())
                        ->isIdenticalTo('mandate-identifier')

                    ->string($this->testedInstance->getName())
                        ->isIdenticalTo('John Doe')

                    // Check object
                    ->object($this->testedInstance->getCheck())
                        ->isInstanceOf(Stancer\Sepa\Check::class)

                    ->string($this->testedInstance->getCheck()->getId())
                        ->isIdenticalTo('sepa_bIvCZePYqfMlU11TANT8IqL1') // Same as SEPA

                    ->dateTime($this->testedInstance->getCheck()->getCreationDate())
                        ->hasDate(2021, 2, 10)
                        ->hasTime(12, 59, 52)
                        ->isImmutable

                    ->variable($this->testedInstance->getCheck()->getDateBirth())
                        ->isNull

                    ->variable($this->testedInstance->getCheck()->getResponse())
                        ->isNull

                    ->object($this->testedInstance->getCheck()->getSepa())
                        ->isTestedInstance

                    ->variable($this->testedInstance->getCheck()->getScoreName())
                        ->isNull

                    ->string($this->testedInstance->getCheck()->getStatus())
                        ->isIdenticalTo(Stancer\Sepa\Check\Status::CHECK_SENT)

                    ->mock($client)
                        ->call('request')
                            ->withArguments('POST', $checkLocation, array_merge($options, ['body' => json_encode($data)]))
                                ->once
                        ->call('request')
                            ->withArguments('GET', $sepaLocation, $options)
                                ->once

            ->assert('Ask for verification on already created SEPA')
                ->given($id = $this->getRandomString(29))
                ->and($this->newTestedInstance($id))

                ->if($checkResponse = new mock\Stancer\Http\Response(200))
                ->and($body = file_get_contents(__DIR__ . '/fixtures/sepa/check/read.json'))
                ->and($this->calling($checkResponse)->getBody = new Stancer\Http\Stream($body))

                ->and($sepaResponse = new mock\Stancer\Http\Response(200))
                ->and($body = file_get_contents(__DIR__ . '/fixtures/sepa/read.json'))
                ->and($this->calling($sepaResponse)->getBody = new Stancer\Http\Stream($body))

                ->if($client = new mock\Stancer\Http\Client)
                ->and($this->calling($client)->request[] = $checkResponse)
                ->and($this->calling($client)->request[] = $sepaResponse)
                ->and($config->setHttpClient($client))

                ->if($checkLocation = (new Stancer\Sepa\Check())->getUri())
                ->then
                    ->object($this->testedInstance->validate())
                        ->isTestedInstance

                    ->string($this->testedInstance->getId())
                        ->isIdenticalTo('sepa_fZvOCm7oDmUJhqvezEtlZwXa') // SEPA id is updated during process

                    // Check object
                    ->object($this->testedInstance->getCheck())
                        ->isInstanceOf(Stancer\Sepa\Check::class)

                    ->string($this->testedInstance->getCheck()->getId())
                        ->isIdenticalTo('sepa_fZvOCm7oDmUJhqvezEtlZwXa')

                    ->dateTime($this->testedInstance->getCheck()->getCreationDate())
                        ->hasDate(2021, 2, 10)
                        ->hasTime(12, 59, 52)
                        ->isImmutable

                    ->boolean($this->testedInstance->getCheck()->getDateBirth())
                        ->isTrue

                    ->string($this->testedInstance->getCheck()->getResponse())
                        ->isIdenticalTo('00')

                    ->object($this->testedInstance->getCheck()->getSepa())
                        ->isTestedInstance

                    ->float($this->testedInstance->getCheck()->getScoreName())
                        ->isIdenticalTo(0.32)

                    ->string($this->testedInstance->getCheck()->getStatus())
                        ->isIdenticalTo(Stancer\Sepa\Check\Status::CHECKED)

                    ->mock($client)
                        ->call('request')
                            ->withArguments('POST', $checkLocation, array_merge($options, ['body' => json_encode(['id' => $id])]))
                                ->once
        ;
    }
}
