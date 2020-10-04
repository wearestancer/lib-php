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
            ->if($this->newTestedInstance(''))
            ->then
                ->variable($this->testedInstance->close()) // Will do nothing
                    ->isNull
        ;
    }

    public function testDetach()
    {
        $this
            ->if($this->newTestedInstance(''))
            ->then
                ->variable($this->testedInstance->detach()) // Will do nothing
                    ->isNull
        ;
    }

    public function testGetMetadata()
    {
        $this
            ->if($this->newTestedInstance(''))
            ->then
                ->array($this->testedInstance->getMetadata())
                    ->isEmpty

                ->variable($this->testedInstance->getMetadata(uniqid()))
                    ->isNull
        ;
    }

    public function testGetSize()
    {
        $this
            ->given($content = uniqid())
            ->and($len = strlen($content))

            ->if($this->newTestedInstance($content))
            ->then
                ->integer($this->testedInstance->getSize())
                    ->isEqualTo($len)
        ;
    }

    public function testIsReadable()
    {
        $this
            ->if($this->newTestedInstance(''))
            ->then
                ->boolean($this->testedInstance->isReadable())
                    ->isTrue
        ;
    }

    public function testIsSeekable()
    {
        $this
            ->if($this->newTestedInstance(''))
            ->then
                ->boolean($this->testedInstance->isSeekable())
                    ->isTrue
        ;
    }

    public function testIsWritable()
    {
        $this
            ->if($this->newTestedInstance(''))
            ->then
                ->boolean($this->testedInstance->isWritable())
                    ->isfalse
        ;
    }

    public function testRead()
    {
        $this
            ->given($content = uniqid())
            ->and($len = strlen($content))

            ->if($this->newTestedInstance($content))
            ->then
                ->string($this->testedInstance->read(0))
                    ->isEmpty

                ->integer($this->testedInstance->tell())
                    ->isEqualTo(0)

                ->boolean($this->testedInstance->eof())
                    ->isFalse

                ->string($this->testedInstance->read(1))
                    ->isIdenticalTo(substr($content, 0, 1))

                ->integer($this->testedInstance->tell())
                    ->isEqualTo(1)

                ->boolean($this->testedInstance->eof())
                    ->isFalse

                ->string($this->testedInstance->read(10))
                    ->isIdenticalTo(substr($content, 1, 10))

                ->integer($this->testedInstance->tell())
                    ->isEqualTo(11)

                ->boolean($this->testedInstance->eof())
                    ->isFalse

                ->string($this->testedInstance->read($len))
                    ->isIdenticalTo(substr($content, 11))

                ->integer($this->testedInstance->tell())
                    ->isEqualTo($len)

                ->boolean($this->testedInstance->eof())
                    ->isTrue
        ;
    }

    public function testSeek_Tell()
    {
        $this
            ->given($content = uniqid())
            ->and($len = strlen($content))

            ->if($this->newTestedInstance($content))
            ->then
                ->assert('Default behavior')
                    ->object($this->testedInstance->seek(1))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(1)

                    ->object($this->testedInstance->seek(1))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(1)

                    ->object($this->testedInstance->seek($len + 10))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo($len)

                    ->object($this->testedInstance->seek(-1))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(0)

                    ->object($this->testedInstance->seek(0))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(0)

                ->assert('Absolute position')
                    ->object($this->testedInstance->seek(1, SEEK_SET))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(1)

                    ->object($this->testedInstance->seek(1, SEEK_SET))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(1)

                    ->object($this->testedInstance->seek($len + 10, SEEK_SET))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo($len)

                    ->object($this->testedInstance->seek(-1, SEEK_SET))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(0)

                    ->object($this->testedInstance->seek($len * -2, SEEK_SET))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(0)

                ->assert('Relative position')
                    ->object($this->testedInstance->seek(1, SEEK_CUR))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(1)

                    ->object($this->testedInstance->seek(1, SEEK_CUR))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(2)

                    ->object($this->testedInstance->seek($len + 10, SEEK_CUR))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo($len)

                    ->object($this->testedInstance->seek(-1, SEEK_CUR))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo($len - 1)

                    ->object($this->testedInstance->seek($len * -2, SEEK_CUR))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(0)

                ->assert('From end')
                    ->object($this->testedInstance->seek(1, SEEK_END))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo($len)

                    ->object($this->testedInstance->seek(1, SEEK_END))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo($len)

                    ->object($this->testedInstance->seek($len + 10, SEEK_END))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo($len)

                    ->object($this->testedInstance->seek(-1, SEEK_END))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo($len - 1)

                    ->object($this->testedInstance->seek($len * -2, SEEK_END))
                        ->isTestedInstance

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(0)
        ;
    }

    public function testWrite()
    {
        $this
            ->if($this->newTestedInstance(''))
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
