<?php

namespace Stancer\tests\unit;

use Stancer;

class Auth extends Stancer\Tests\atoum
{
    public function randomStatus(): Stancer\Auth\Status
    {
        return $this->choose(Stancer\Auth\Status::cases());
    }

    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->extends(Stancer\Core\AbstractObject::class)

            ->enum($this->newTestedInstance->getStatus())
                ->isIdenticalTo(Stancer\Auth\Status::REQUEST)

            ->array($this->testedInstance->jsonSerialize())
                ->hasSize(1)
                ->hasKey('status')
                ->string['status']
                    ->isIdenticalTo('request')
        ;
    }

    public function testGetRedirectUrl()
    {
        $this
            ->given($https = 'https://www.example.org/?' . uniqid())
            ->and($data = ['redirectUrl' => $https])

            ->if($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->getRedirectUrl())
                    ->isNull

                ->variable($this->testedInstance->redirectUrl)
                    ->isNull

                ->variable($this->testedInstance->get_redirect_url())
                    ->isNull

                ->variable($this->testedInstance->redirect_url)
                    ->isNull

            ->if($this->testedInstance->hydrate($data))
            ->then
                ->string($this->testedInstance->getRedirectUrl())
                    ->isIdenticalTo($https)

                ->string($this->testedInstance->redirectUrl)
                    ->isIdenticalTo($https)

                ->string($this->testedInstance->get_redirect_url())
                    ->isIdenticalTo($https)

                ->string($this->testedInstance->redirect_url)
                    ->isIdenticalTo($https)
        ;
    }

    public function testGetReturnUrl_SetReturnUrl()
    {
        $this
            ->given($https = 'https://www.example.org/?' . uniqid())
            ->and($http = 'http://www.example.org/?' . uniqid())

            ->then
                ->assert('With camel case method')
                    ->variable($this->newTestedInstance->getReturnUrl())
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

                ->assert('With camel case property')
                    ->variable($this->newTestedInstance->returnUrl)
                        ->isNull

                    ->if($this->testedInstance->returnUrl = $https)
                    ->then
                        ->string($this->testedInstance->returnUrl)
                            ->isIdenticalTo($https)

                        ->exception(function () use ($http) {
                            $this->testedInstance->returnUrl = $http;
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidUrlException::class)
                            ->message
                                ->isIdenticalTo('You must provide an HTTPS URL.')

                ->assert('With snake case method')
                    ->variable($this->newTestedInstance->get_return_url())
                        ->isNull

                    ->object($this->testedInstance->set_return_url($https))
                        ->isTestedInstance

                    ->string($this->testedInstance->get_return_url())
                        ->isIdenticalTo($https)

                    ->exception(function () use ($http) {
                        $this->testedInstance->set_return_url($http);
                    })
                        ->isInstanceOf(Stancer\Exceptions\InvalidUrlException::class)
                        ->message
                            ->isIdenticalTo('You must provide an HTTPS URL.')

                ->assert('With snake case property')
                    ->variable($this->newTestedInstance->return_url)
                        ->isNull

                    ->if($this->testedInstance->return_url = $https)
                    ->then
                        ->string($this->testedInstance->return_url)
                            ->isIdenticalTo($https)

                        ->exception(function () use ($http) {
                            $this->testedInstance->return_url = $http;
                        })
                            ->isInstanceOf(Stancer\Exceptions\InvalidUrlException::class)
                            ->message
                                ->isIdenticalTo('You must provide an HTTPS URL.')
        ;
    }

    public function testGetStatus()
    {
        $this
            ->given($status = $this->randomStatus())
            ->and($data = ['status' => $status])

            ->if($this->newTestedInstance)
            ->then
                ->enum($this->testedInstance->getStatus())
                    ->isIdenticalTo(Stancer\Auth\Status::REQUEST)

                ->enum($this->testedInstance->status)
                    ->isIdenticalTo(Stancer\Auth\Status::REQUEST)

                ->enum($this->testedInstance->get_status())
                    ->isIdenticalTo(Stancer\Auth\Status::REQUEST)

                ->enum($this->testedInstance->status)
                    ->isIdenticalTo(Stancer\Auth\Status::REQUEST)

            ->if($this->testedInstance->hydrate($data))
            ->then
                ->enum($this->testedInstance->getStatus())
                    ->isIdenticalTo($status)

                ->enum($this->testedInstance->status)
                    ->isIdenticalTo($status)

                ->enum($this->testedInstance->get_status())
                    ->isIdenticalTo($status)

                ->enum($this->testedInstance->status)
                    ->isIdenticalTo($status)
        ;
    }
}
