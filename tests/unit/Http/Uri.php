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
    public function testGetHost($uri, $scheme, $host)
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
    public function testGetScheme($uri, $scheme, $host)
    {
        $this
            ->if($this->newTestedInstance($uri))
            ->then
                ->string($this->testedInstance->getScheme())
                    ->isIdenticalTo($scheme)
        ;
    }
}
