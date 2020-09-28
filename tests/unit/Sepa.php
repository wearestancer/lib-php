<?php

namespace ild78\tests\unit;

use ild78;
use ild78\Sepa as testedClass;

class Sepa extends ild78\Tests\atoum
{
    use ild78\Tests\Provider\Banks;

    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(ild78\Core\AbstractObject::class)
                ->implements(ild78\Interfaces\PaymentMeansInterface::class)
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
            ->and($this->testedInstance->setIban($iban))
            ->then
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
                            ->isInstanceOf(ild78\Exceptions\InvalidMandateException::class)
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
                        ->isInstanceOf(ild78\Exceptions\InvalidBicException::class)
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
                        ->isInstanceOf(ild78\Exceptions\InvalidIbanException::class)
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
                    ->isInstanceOf(ild78\Exceptions\InvalidNameException::class)
                    ->message
                        ->isIdenticalTo('A valid name must be between 4 and 64 characters.')

                ->boolean($this->testedInstance->isModified())
                    ->isFalse
        ;
    }
}
