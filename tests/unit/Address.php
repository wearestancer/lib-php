<?php

namespace Stancer\tests\unit;

use DateTime;
use Stancer;
use mock;

class Address extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(Stancer\Core\AbstractObject::class)
        ;
    }

    public function testCity()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($city = $this->getRandomString(1, 50))
            ->and($cityTooLong = $this->getRandomString(51, 99))

            ->assert('city too long')
                ->exception(function () use ($cityTooLong) {
                    $this->testedInstance->setcity($cityTooLong);
                })
                ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                ->message
                    ->isIdenticalTo('A valid city must be between 1 and 50 characters.')

            ->assert('city can be set')
                ->object($this->testedInstance->set_city($city))
                    ->isTestedInstance
                ->object($this->testedInstance->setCity($city))
                    ->isTestedInstance

            ->assert('city can be get')
                ->string($this->testedInstance->get_city())
                    ->isIdenticalTo($city)
                ->string($this->testedInstance->getCity())
                    ->isIdenticalTo($city)
        ;
    }

    public function testCountry()
    {
        $this
            ->given($this->newTestedInstance)

            ->if($country = $this->getRandomString(3, 3))
            ->and($countryTooLong = $this->getRandomString(5))
            ->and($countryTooShort = $this->getRandomString(0, 2))

            ->assert('Country too short')
                ->exception(function () use ($countryTooShort) {
                    $this->testedInstance->setcountry($countryTooShort);
                })
                ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                ->message
                    ->isIdenticalTo('A valid country must have 3 characters.')

            ->assert('Country too long')
                ->exception(function () use ($countryTooLong) {
                        $this->testedInstance->setcountry($countryTooLong);
                    })
                    ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('A valid country must have 3 characters.')

            ->assert('Country null by default')
                    ->variable($this->testedInstance->getCountry())
                        ->isNull()

            ->assert('Country can be set')
                ->object($this->testedInstance->setCountry($country))
                    ->isTestedInstance

            ->assert('Country can be get')
                ->string($this->testedInstance->getCountry())
                    ->isIdenticalTo($country)
        ;
    }

    public function test_get()
    {
        $this
        ->given($client = new mock\Stancer\Http\Client)
        ->and($this->mockConfig($client))
        ->and($response = $this->mockJsonResponse('address','read'))
        ->and($this->calling($client)->request = $response)

        ->if($this->newTestedInstance('addr_m8H4p4n1Oyf1PbaHGBBPfU4a'))

        ->if($location = $this->testedInstance->getUri())

        ->assert('Getting a non populated value will hydrate the full object')
            ->mock($client)
                ->call('request')
                    ->withArguments('GET', $location)
                        ->never

            ->string($this->testedInstance->getCountry())
                ->isIdenticalTo('FRA')

            ->mock($client)
                ->call('request')
                    ->withArguments('GET', $location)
                        ->once

            ->string($this->testedInstance->getState())
                ->isIdenticalTo('IDF')

            ->dateTime($this->testedInstance->getCreated())
                ->isEqualTo(new DateTime('@1722435409'))

            ->boolean($this->testedInstance->getDeleted())
                ->isFalse()

            ->string($this->testedInstance->getLine1())
                ->isIdenticalTo('10 rue du test')

            ->string($this->testedInstance->getLine2())
                ->isIdenticalTo('Au fond de la cour')

            ->string($this->testedInstance->getLine3())
                ->isIdenticalTo('Deuxième étage porte de gauche')

            ->array($this->testedInstance->getMetadata())
                ->hasKey('origin')
                ->contains('test')

            ->string($this->testedInstance->getZipCode())
                ->isIdenticalTo('75000')

            ->mock($client)
                ->call('request')
                    ->withArguments('GET', $location)
                        ->once
        ;
    }

    public function testLineOne()
    {
        $this
            ->given($this->newTestedInstance)

            ->if($line1 = $this->getRandomString(1, 50))
            ->and($lineTooLong= $this->getRandomString(51, 70))

            ->assert('Line1 too long')
                ->exception(function () use ($lineTooLong) {
                    $this->testedInstance->setLine1($lineTooLong);
                })
                ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                ->message
                    ->isIdenticalTo('A valid line1 must be between 1 and 50 characters.')

            ->assert('Line1 null by default')
                    ->variable($this->testedInstance->getLine1())
                        ->isNull()

            ->assert('line1 can be set')
                ->object($this->testedInstance->setLine1($line1))
                    ->isTestedInstance

            ->assert('line1 can be get')
                ->string($this->testedInstance->getLine1())
                    ->isIdenticalTo($line1)

            ;
    }

    public function testLineTwo()
    {
        $this
            ->given($this->newTestedInstance)

            ->if($line2 = $this->getRandomString(1,50))
            ->and($lineTooLong= $this->getRandomString(51,70))

            ->assert('line2 too long')
                ->exception(function () use ($lineTooLong) {
                    $this->testedInstance->setLine2($lineTooLong);
                })
                ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                ->message
                    ->isIdenticalTo('A valid line2 must be between 1 and 50 characters.')

            ->assert('Line2 null by default')
                ->variable($this->testedInstance->getLine2())
                    ->isNull()

            ->assert('line2 can be set')
                ->object($this->testedInstance->setLine2($line2))
                    ->isTestedInstance

            ->assert('line2 can be get')
                ->string($this->testedInstance->getLine2())
                    ->isIdenticalTo($line2)
            ;
    }

    public function testLineThree()
    {
        $this
            ->given($this->newTestedInstance)

            ->if($line3 = $this->getRandomString(1,50))
            ->and($lineTooLong= $this->getRandomString(51,70))

            ->assert('line3 too long')
                ->exception(function () use ($lineTooLong) {
                    $this->testedInstance->setLine3($lineTooLong);
                })
                ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                ->message
                    ->isIdenticalTo('A valid line3 must be between 1 and 50 characters.')

            ->assert('Line3 null by default')
                ->variable($this->testedInstance->getLine3())
                        ->isNull()


            ->assert('line3 can be set')
                ->object($this->testedInstance->setLine3($line3))
                    ->isTestedInstance

            ->assert('line3 can be get')
                ->string($this->testedInstance->getLine3())
                    ->isIdenticalTo($line3)
            ;
    }

    public function testMetadata()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($defaultMetadata = ['origin' => 'sdk_PHP'])

            ->if($data1 = $this->getRandomString(50))
            ->and($data2 = $this->getRandomString(125))
            ->and($metadata = ['data1' => $data1])
            ->and($addMetadata= ['data2' => $data2])

            ->then($finalMetadata=[
                ...$metadata,
                ...$addMetadata,
            ])

            ->assert("unset metadata return ['origin' => 'sdk_PHP']")
                ->variable($this->testedInstance->getMetadata())
                    ->isEqualTo(['origin' => 'sdk_PHP'])

            ->assert("we don't accept flat json")
                ->if($this->testedInstance->setMetadata($this->getRandomString(25)))

                ->exception(fn() => $this->testedInstance->getMetadata())
                    ->isInstanceOf(Stancer\Exceptions\InvalidJsonException::class)
                        ->message
                            ->isIdenticalTo('Invalid Json, couldn\'t be parsed as an array.')

                ->exception(fn() => $this->testedInstance->addMetadata($addMetadata))
                    ->isInstanceOf(Stancer\Exceptions\InvalidJsonException::class)
                            ->message
                                ->isIdenticalTo('Invalid Json, couldn\'t be parsed as an array.')


            ->given($this->newTestedInstance)
            ->assert('we can add Metadata even if empty.')
                    ->object($this->testedInstance->addMetadata($addMetadata))
                        ->isTestedInstance
                    ->array($this->testedInstance->getMetadata())
                        ->isEqualTo(
                            [
                            ...$defaultMetadata,
                            ...$addMetadata
                            ])

            ->given($this->newTestedInstance)
            ->assert('Metadata can be set')
                    ->object($this->testedInstance->setMetadata($metadata))
                        ->isTestedInstance

            ->assert('Metadata can have data added')
                ->object($this->testedInstance->addMetadata($addMetadata))
                    ->isTestedInstance

            ->assert('Metadata can be get')
                ->array($this->testedInstance->getMetadata())
                    ->isEqualTo($finalMetadata)
            ;
        }
        public function test_metadata_invalid_json(){

        $this

            ->given($this->newTestedInstance)

            ->if($this->function->json_last_error = 2 )
            ->and($metadata = 'bad Json')

            ->assert("check that errors are thrown when bad json")
                    ->exception( fn() => $this->testedInstance->setMetadata($metadata))
                        ->isInstanceOf(Stancer\Exceptions\InvalidJsonException::class)
                            ->message
                                ->isIdenticalTo('Invalid Json, cannot be parsed.')
                ;
        }

    public function test_send()
    {
        $this
            ->given($client = new mock\Stancer\Http\Client)
            ->and($config = $this->mockConfig($client))
            ->and($response = $this->mockJsonResponse('address', 'create'))
            ->and($this->calling($client)->request = $response)

            ->given($this->newTestedInstance)

            ->then
                ->given($this->testedInstance->setCity('Paris'))
                ->and($this->testedInstance->setCountry('FRA'))
                ->and($this->testedInstance->setLine1('10 rue de l\'aumier'))
                ->and($this->testedInstance->setLine2('truc bidule chouete'))
                ->and($this->testedInstance->setLine3('deuxième porte à gauche'))
                ->and($this->testedInstance->setMetadata(['origin' => 'test']))
                ->and($this->testedInstance->setState('IDF'))
                ->and($this->testedInstance->setZipCode('75000'))

                ->if($location = $this->testedInstance->getUri())
                ->and($options = $this->mockRequestOptions($config, [
                    'body' => json_encode($this->testedInstance),
                ]))

            ->assert('Post an object')
                ->variable($this->testedInstance->getId())
                    ->isNull()

                ->object($this->testedInstance->send())
                    ->isTestedInstance

                ->mock($client)
                    ->call('request')
                        ->withArguments('POST', $location, $options)
                            ->once

            ->assert('Could not patch an object')
                ->if($this->testedInstance->setCity('London'))
                ->and($patchOptions = $this->mockRequestOptions($config, [
                    'body' => json_encode($this->testedInstance),
                ]))

                ->string($this->testedInstance->getId())
                    ->isIdenticalTo('addr_m8H4p4n1Oyf1PbaHGBBPfU4a')

                ->exception(fn() => $this->testedInstance->send())
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                        ->message
                            ->isIdenticalTo('Addresses cannot be patched.')

                ->mock($client)
                    ->call('request')
                        ->withArguments('PATCH', $location, $patchOptions)
                            ->never
            ;
    }

    public function testState()
    {
        $this
            ->given($this->newTestedInstance)

            ->if($state = $this->getRandomString(3,3))
            ->and($stateTooLong = $this->getRandomString(4,10))
            ->and($stateTooShort= '')

            ->assert('state too long')
                ->exception(function () use ($stateTooShort) {
                    $this->testedInstance->setstate($stateTooShort);
                })
                ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                ->message
                    ->isIdenticalTo('A valid state must be between 1 and 3 characters.')

            ->assert('state too long')
                ->exception(function () use ($stateTooLong) {
                        $this->testedInstance->setstate($stateTooLong);
                    })
                    ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                    ->message
                        ->isIdenticalTo('A valid state must be between 1 and 3 characters.')


            ->assert('State null by default')
                    ->variable($this->testedInstance->getLine3())
                            ->isNull()

            ->assert('State can be set')
                ->object($this->testedInstance->setState($state))
                    ->isTestedInstance

            ->assert('State can be get')
                ->string($this->testedInstance->getState())
                    ->isIdenticalTo($state)
        ;
    }

    public function testZipCode()
    {
        $this
            ->given($this->newTestedInstance)

            ->if($zipCode = $this->getRandomString(1, 16))
            ->and($zipCodeTooLong = $this->getRandomString(17, 20))

            ->assert('Zipcode too long')
                ->exception(function () use ($zipCodeTooLong) {
                    $this->testedInstance->setZipCode($zipCodeTooLong);
                })
                ->isInstanceOf(Stancer\Exceptions\InvalidArgumentException::class)
                ->message
                    ->isIdenticalTo('A valid zip code must be between 1 and 16 characters.')

            ->assert('Zipcode null by default')
                ->variable($this->testedInstance->getLine3())
                        ->isNull()

            ->assert('Zipcode can be set')
                ->object($this->testedInstance->setZipCode($zipCode))
                    ->isTestedInstance

            ->assert('Zipcode can be get')
                ->string($this->testedInstance->getZipCode())
                    ->isIdenticalTo($zipCode)
            ;
    }
}
