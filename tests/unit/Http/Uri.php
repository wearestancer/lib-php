<?php

namespace ild78\Http\tests\unit;

use ild78;
use Psr;

class Uri extends ild78\Tests\atoum
{
    use ild78\Tests\Provider\Http;

    public function cleanComponent($name, $obj)
    {
        $arr = $obj->getComponents();

        unset($arr[$name]);

        return $arr;
    }

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
    public function testCastToString($uri, $scheme, $host, $port, $user, $pass, $path, $query, $hash, $clean)
    {
        $this
            ->if($this->newTestedInstance($uri))
            ->then
                ->castToString($this->testedInstance)
                    ->isIdenticalTo($clean)
        ;
    }

    /**
     * @dataProvider urlProvider
     */
    public function testGetAuthority($uri, $scheme, $host, $port, $user, $pass, $path, $query, $hash, $clean)
    {
        $this
            ->if($this->newTestedInstance($uri))
            ->and($authority = '')
            ->when(function () use (&$authority, $user, $pass, $host, $port) {
                $authority = '';

                if ($user || $pass ) {
                    $authority .= $user . ($pass ? ':' : '') . $pass . '@';
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
    public function testGetComponents($uri, $scheme, $host, $port, $user, $pass, $path, $query, $hash, $clean)
    {
        $this
            ->if($this->newTestedInstance($uri))
            ->and($fragment = $hash)
            ->and($components = array_filter(compact('fragment', 'host', 'pass', 'path', 'port', 'query', 'scheme', 'user')))
            ->then
                ->array($this->testedInstance->getComponents())
                    ->hasKeys(array_keys($components))
                    ->isEqualTo($components)
        ;
    }

    /**
     * @dataProvider urlProvider
     */
    public function testGetFragment($uri, $scheme, $host, $port, $user, $pass, $path, $query, $hash, $clean)
    {
        $this
            ->if($this->newTestedInstance($uri))
            ->then
                ->string($this->testedInstance->getFragment())
                    ->isIdenticalTo($hash)
        ;
    }

    /**
     * @dataProvider urlProvider
     */
    public function testGetHost($uri, $scheme, $host, $port, $user, $pass, $path, $query, $hash, $clean)
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
    public function testGetPath($uri, $scheme, $host, $port, $user, $pass, $path, $query, $hash, $clean)
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
    public function testGetPort($uri, $scheme, $host, $port, $user, $pass, $path, $query, $hash, $clean)
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
    public function testGetQuery($uri, $scheme, $host, $port, $user, $pass, $path, $query, $hash, $clean)
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
    public function testGetScheme($uri, $scheme, $host, $port, $user, $pass, $path, $query, $hash, $clean)
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
    public function testGetUserInfo($uri, $scheme, $host, $port, $user, $pass, $path, $query, $hash, $clean)
    {
        $this
            ->if($this->newTestedInstance($uri))
            ->and($info = $user . ($pass ? ':' : '') . $pass)
            ->then
                ->string($this->testedInstance->getUserInfo())
                    ->isIdenticalTo($info)
        ;
    }

    /**
     * @dataProvider urlProvider
     */
    public function testWithFragment($uri, $scheme, $host, $port, $user, $pass, $path, $query, $hash, $clean)
    {
        $this
            ->if($this->newTestedInstance($uri))
            ->and($fragment = uniqid())
            ->then
                ->string($this->testedInstance->getFragment())
                    ->isIdenticalTo($hash)

                ->object($object = $this->testedInstance->withFragment($fragment))
                    ->isInstanceOfTestedClass
                    ->isNotTestedInstance

                ->string($this->testedInstance->getFragment())
                    ->isIdenticalTo($hash)

                ->string($object->getFragment())
                    ->isIdenticalTo($fragment)

                ->array($this->cleanComponent('fragment', $object))
                    ->isEqualTo($this->cleanComponent('fragment', $this->testedInstance))
        ;
    }

    /**
     * @dataProvider urlProvider
     */
    public function testWithHost($uri, $scheme, $host, $port, $user, $pass, $path, $query, $hash, $clean)
    {
        $this
            ->if($this->newTestedInstance($uri))
            ->and($newHost = uniqid())
            ->then
                ->string($this->testedInstance->getHost())
                    ->isIdenticalTo($host)

                ->object($object = $this->testedInstance->withHost($newHost))
                    ->isInstanceOfTestedClass
                    ->isNotTestedInstance

                ->string($this->testedInstance->getHost())
                    ->isIdenticalTo($host)

                ->string($object->getHost())
                    ->isIdenticalTo($newHost)

                ->array($this->cleanComponent('host', $object))
                    ->isEqualTo($this->cleanComponent('host', $this->testedInstance))
        ;
    }

    /**
     * @dataProvider urlProvider
     */
    public function testWithPath($uri, $scheme, $host, $port, $user, $pass, $path, $query, $hash, $clean)
    {
        $this
            ->if($this->newTestedInstance($uri))
            ->and($newPath = uniqid())
            ->then
                ->string($this->testedInstance->getPath())
                    ->isIdenticalTo($path)

                ->object($object = $this->testedInstance->withPath($newPath))
                    ->isInstanceOfTestedClass
                    ->isNotTestedInstance

                ->string($this->testedInstance->getPath())
                    ->isIdenticalTo($path)

                ->string($object->getPath())
                    ->isIdenticalTo($newPath)

                ->array($this->cleanComponent('path', $object))
                    ->isEqualTo($this->cleanComponent('path', $this->testedInstance))
        ;
    }

    /**
     * @dataProvider urlProvider
     */
    public function testWithPort($uri, $scheme, $host, $port, $user, $pass, $path, $query, $hash, $clean)
    {
        $this
            ->if($this->newTestedInstance($uri))
            ->and($newPort = rand(1, 65535))
            ->then
                ->variable($this->testedInstance->getPort())
                    ->isIdenticalTo($port)

                ->object($object = $this->testedInstance->withPort($newPort))
                    ->isInstanceOfTestedClass
                    ->isNotTestedInstance

                ->variable($this->testedInstance->getPort())
                    ->isIdenticalTo($port)

                ->integer($object->getPort())
                    ->isIdenticalTo($newPort)

                ->array($this->cleanComponent('port', $object))
                    ->isEqualTo($this->cleanComponent('port', $this->testedInstance))
        ;
    }

    /**
     * @dataProvider urlProvider
     */
    public function testWithQuery($uri, $scheme, $host, $port, $user, $pass, $path, $query, $hash, $clean)
    {
        $this
            ->if($this->newTestedInstance($uri))
            ->and($newQuery = uniqid())
            ->then
                ->string($this->testedInstance->getQuery())
                    ->isIdenticalTo($query)

                ->object($object = $this->testedInstance->withQuery($newQuery))
                    ->isInstanceOfTestedClass
                    ->isNotTestedInstance

                ->string($this->testedInstance->getQuery())
                    ->isIdenticalTo($query)

                ->string($object->getQuery())
                    ->isIdenticalTo($newQuery)

                ->array($this->cleanComponent('query', $object))
                    ->isEqualTo($this->cleanComponent('query', $this->testedInstance))
        ;
    }

    /**
     * @dataProvider urlProvider
     */
    public function testWithScheme($uri, $scheme, $host, $port, $user, $pass, $path, $query, $hash, $clean)
    {
        $this
            ->if($this->newTestedInstance($uri))
            ->and($newScheme = uniqid())
            ->then
                ->string($this->testedInstance->getScheme())
                    ->isIdenticalTo($scheme)

                ->object($object = $this->testedInstance->withScheme($newScheme))
                    ->isInstanceOfTestedClass
                    ->isNotTestedInstance

                ->string($this->testedInstance->getScheme())
                    ->isIdenticalTo($scheme)

                ->string($object->getScheme())
                    ->isIdenticalTo($newScheme)

                ->array($this->cleanComponent('scheme', $object))
                    ->isEqualTo($this->cleanComponent('scheme', $this->testedInstance))
        ;
    }
}
