<?php

namespace ild78\tests\functional;

use atoum;
use ild78;

/**
 * @namespace \tests\functional
 */
class Customer extends atoum
{
    protected $config;

    public function beforeTestMethod($testMethod)
    {
        $env = [
            'ILD_API_HOST' => '',
            'ILD_API_KEY' => '',
        ];

        foreach ($env as $key => &$value) {
            $value = getenv($key);

            if (!$value) {
                $this->skip('Missing env ' . $key);
            }
        }

        if (!$this->config) {
            $this->config = ild78\Api\Config::init($env['ILD_API_KEY']);
        }

        $this->config->setHost($env['ILD_API_HOST']);
    }

    public function getRandomNumber()
    {
        $number = '+33' . (rand(0, 1) + 6); // Simulate a french mobile phone number

        for ($idx = 0; $idx < 4; $idx++) {
            $number .= str_pad(rand(0, 99), 2, '0');
        }

        return $number;
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
