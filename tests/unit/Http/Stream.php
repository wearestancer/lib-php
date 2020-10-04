<?php

namespace ild78\Http\tests\unit;

use ild78;
use Psr;

class Stream extends ild78\Tests\atoum
{
    public function testClass()
    {
        $this
            ->currentlyTestedClass
                ->implements(Psr\Http\Message\StreamInterface::class)
        ;
    }

    public function testClose()
    {
        $this
            ->if($this->newTestedInstance())
            ->then
                ->variable($this->testedInstance->close()) // Will do nothing
                    ->isNull
        ;
    }

    public function testDetach()
    {
        $this
            ->if($this->newTestedInstance())
            ->then
                ->variable($this->testedInstance->detach()) // Will do nothing
                    ->isNull
        ;
    }

    public function testGetMetadata()
    {
        $this
            ->if($this->newTestedInstance())
            ->then
                ->array($this->testedInstance->getMetadata())
                    ->isEmpty

                ->variable($this->testedInstance->getMetadata(uniqid()))
                    ->isNull
        ;
    }

    public function testIsReadable()
    {
        $this
            ->if($this->newTestedInstance())
            ->then
                ->boolean($this->testedInstance->isReadable())
                    ->isTrue
        ;
    }

    public function testIsSeekable()
    {
        $this
            ->if($this->newTestedInstance())
            ->then
                ->boolean($this->testedInstance->isSeekable())
                    ->isTrue
        ;
    }

    public function testIsWritable()
    {
        $this
            ->if($this->newTestedInstance())
            ->then
                ->boolean($this->testedInstance->isWritable())
                    ->isfalse
        ;
    }

    public function testWrite()
    {
        $this
            ->if($this->newTestedInstance())
            ->then
                ->exception(function () {
                    $this->testedInstance->write(uniqid());
                })
                    ->isInstanceOf(ild78\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('This method is not implemented.')
        ;
    }
}
