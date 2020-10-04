<?php

namespace ild78\Http\tests\unit;

use ild78;
use Psr;

class Uri extends ild78\Tests\atoum
{
    use ild78\Tests\Provider\Http;

    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->implements(Psr\Http\Message\UriInterface::class)
        ;
    }

    /**
     * @dataProvider urlProvider
     */
    public function testGetAuthority($uri, $scheme, $host, $port, $user, $path, $query)
    {
        $this
            ->if($this->newTestedInstance($uri))
            ->and($authority = '')
            ->when(function () use (&$authority, $user, $host, $port) {
                $authority = '';

                if ($user) {
                    $authority .= $user . '@';
                }

                $authority .= $host;

                if (!is_null($port)) {
                    $authority .= ':' . $port;
                }
            })
            ->then
                ->string($this->testedInstance->getAuthority())
                    ->isIdenticalTo($authority)
        ;
    }

    /**
     * @dataProvider urlProvider
     */
    public function testGetHost($uri, $scheme, $host, $port, $user, $path, $query)
    {
        $this
            ->if($this->newTestedInstance($uri))
            ->then
                ->string($this->testedInstance->getHost())
                    ->isIdenticalTo($host)
        ;
    }

    /**
     * @dataProvider urlProvider
     */
    public function testGetPath($uri, $scheme, $host, $port, $user, $path, $query)
    {
        $this
            ->if($this->newTestedInstance($uri))
            ->then
                ->string($this->testedInstance->getPath())
                    ->isIdenticalTo($path)
        ;
    }

    /**
     * @dataProvider urlProvider
     */
    public function testGetPort($uri, $scheme, $host, $port, $user, $path, $query)
    {
        $this
            ->if($this->newTestedInstance($uri))
            ->then
                ->variable($this->testedInstance->getPort())
                    ->isIdenticalTo($port)
        ;
    }

    /**
     * @dataProvider urlProvider
     */
    public function testGetQuery($uri, $scheme, $host, $port, $user, $path, $query)
    {
        $this
            ->if($this->newTestedInstance($uri))
            ->then
                ->variable($this->testedInstance->getQuery())
                    ->isIdenticalTo($query)
        ;
    }

    /**
     * @dataProvider urlProvider
     */
    public function testGetScheme($uri, $scheme, $host, $port, $user, $path, $query)
    {
        $this
            ->if($this->newTestedInstance($uri))
            ->then
                ->string($this->testedInstance->getScheme())
                    ->isIdenticalTo($scheme)
        ;
    }

    /**
     * @dataProvider urlProvider
     */
    public function testGetUserInfo($uri, $scheme, $host, $port, $user, $path, $query)
    {
        $this
            ->if($this->newTestedInstance($uri))
            ->then
                ->string($this->testedInstance->getUserInfo())
                    ->isIdenticalTo($user)
        ;
    }
}
