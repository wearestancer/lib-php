<?php

namespace ild78\tests\unit;

use atoum;
use ild78\Api;
use ild78\Exceptions;
use ild78\Sepa as testedClass;

class Sepa extends atoum
{
    public function ibanDataProvider()
    {
        // Thanks Wikipedia
        return [
            'BE71 0961 2345 6769',
            'FR76 3000 6000 0112 3456 7890 189',
            'DE91 1000 0000 0123 4567 89',
            'GR9608100010000001234567890',
            'RO09 BCYP 0000 0012 3456 7890',
            'SA4420000001234567891234',
            'ES79 2100 0813 6101 2345 6789',
            'CH56 0483 5012 3456 7800 9 ',
            'GB98 MIDL 0700 9312 3456 78',
            'GB82WEST12345698765432',
        ];
    }

    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf(Api\Object::class)
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
            ->and($withSpaces = chunk_split($withoutSpaces, 4, ' '))
            ->and($this->testedInstance->setIban($iban))
            ->then
                ->string($this->testedInstance->getIban())
                    ->isIdenticalTo($withoutSpaces)

                ->string($this->testedInstance->getFormattedIban())
                    ->isIdenticalTo($withSpaces)
        ;
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
                ;
            } else {
                $this
                    ->exception(function () use ($bic) {
                        $this->testedInstance->setBic($bic);
                    })
                        ->isInstanceOf(Exceptions\InvalidBicException::class)
                        ->message
                            ->contains($bic)
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

            ->assert('Add an invalid IBAN')
                ->given($iban = 'FR87BARC20658244971655')
                ->and($this->newTestedInstance)
                ->then
                    ->exception(function () use ($iban) {
                        $this->testedInstance->setIban($iban);
                    })
                        ->isInstanceOf(Exceptions\InvalidIbanException::class)
                        ->message
                            ->contains($iban)
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
                    ->isInstanceOf(Exceptions\InvalidNameException::class)
                    ->hasNestedException
                    ->message
                        ->isIdenticalTo('A valid name must be between 4 and 64 characters.')
        ;
    }
}
