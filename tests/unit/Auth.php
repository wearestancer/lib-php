<?php

namespace Stancer\tests\unit;

use Stancer;

class Auth extends Stancer\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(Stancer\Core\AbstractObject::class)

            ->string($this->newTestedInstance->getStatus())
                ->isIdenticalTo(Stancer\Auth\Status::REQUEST)

            ->array($this->testedInstance->jsonSerialize())
                ->hasSize(1)
                ->hasKey('status')
                ->string['status']
                    ->isIdenticalTo(Stancer\Auth\Status::REQUEST)
        ;
    }

    public function testGetReturnUrl_SetReturnUrl()
    {
        $this
            ->given($https = 'https://www.example.org/?' . uniqid())
            ->and($http = 'http://www.example.org/?' . uniqid())

            ->if($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->getReturnUrl())
                    ->isNull

                ->object($this->testedInstance->setReturnUrl($https))
                    ->isTestedInstance

                ->string($this->testedInstance->getReturnUrl())
                    ->isIdenticalTo($https)

                ->exception(function () use ($http) {
                    $this->testedInstance->setReturnUrl($http);
                })
                    ->isInstanceOf(Stancer\Exceptions\InvalidUrlException::class)
                    ->message
                        ->isIdenticalTo('You must provide an HTTPS URL.')
        ;
    }
}
