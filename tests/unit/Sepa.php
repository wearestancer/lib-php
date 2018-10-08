<?php

namespace ild78\tests\unit;

use atoum;
use ild78\Api;
use ild78\Exceptions;
use ild78\Sepa as testedClass;

class Sepa extends atoum
{
    public function testClass()
    {
        $this
            ->class(testedClass::class)
                ->isSubclassOf(Api\Object::class)
        ;
    }

    public function testGetForbiddenProperties()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->getForbiddenProperties())
                    ->contains('created') // from parent
                    ->contains('endpoint') // from parent
                    ->contains('id') // from parent
                    ->contains('country')
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
                        ->isInstanceOf(Exceptions\InvalidArgumentException::class)
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

                ->if($this->newTestedInstance)
                ->then
                    ->variable($this->testedInstance->getCountry())
                        ->isNull

                    ->variable($this->testedInstance->getIban())
                        ->isNull

                    ->object($this->testedInstance->setIban($iban))
                        ->isTestedInstance

                    ->string($this->testedInstance->getCountry())
                        ->isIdenticalTo($country)

                    ->string($this->testedInstance->getIban())
                        ->isIdenticalTo($iban)

            ->assert('Add a valid BE IBAN from wikipedia')
                ->given($iban = 'BE71096123456769')
                ->and($country = substr($iban, 0, 2))
                ->and($this->newTestedInstance)
                ->then
                    ->variable($this->testedInstance->getCountry())
                        ->isNull

                    ->variable($this->testedInstance->getIban())
                        ->isNull

                    ->object($this->testedInstance->setIban($iban))
                        ->isTestedInstance

                    ->string($this->testedInstance->getCountry())
                        ->isIdenticalTo($country)

                    ->string($this->testedInstance->getIban())
                        ->isIdenticalTo($iban)

            ->assert('Add a valid GB IBAN from wikipedia')
                ->given($iban = 'GB82WEST12345698765432')
                ->and($country = substr($iban, 0, 2))
                ->and($this->newTestedInstance)
                ->then
                    ->variable($this->testedInstance->getCountry())
                        ->isNull

                    ->variable($this->testedInstance->getIban())
                        ->isNull

                    ->object($this->testedInstance->setIban($iban))
                        ->isTestedInstance

                    ->string($this->testedInstance->getCountry())
                        ->isIdenticalTo($country)

                    ->string($this->testedInstance->getIban())
                        ->isIdenticalTo($iban)

            ->assert('It should accept space for readeability')
                ->given($iban = 'GR96 0810 0010 0000 0123 4567 890')
                ->and($country = substr($iban, 0, 2))
                ->and($this->newTestedInstance)
                ->then
                    ->variable($this->testedInstance->getCountry())
                        ->isNull

                    ->variable($this->testedInstance->getIban())
                        ->isNull

                    ->object($this->testedInstance->setIban($iban))
                        ->isTestedInstance

                    ->string($this->testedInstance->getCountry())
                        ->isIdenticalTo($country)

                    ->string($this->testedInstance->getIban())
                        ->isIdenticalTo(str_replace(' ', '', $iban))

            ->assert('Add an invalid IBAN')
                ->given($iban = 'FR87BARC20658244971655')
                ->and($this->newTestedInstance)
                ->then
                    ->exception(function () use ($iban) {
                        $this->testedInstance->setIban($iban);
                    })
                        ->isInstanceOf(Exceptions\InvalidArgumentException::class)
                        ->message
                            ->contains($iban)
        ;
    }
}
