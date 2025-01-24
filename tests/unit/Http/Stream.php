<?php

namespace Stancer\Http\tests\unit;

use Psr;
use Stancer;

class Stream extends Stancer\Tests\atoum
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

    public function testGetContent_castToString()
    {
        $this
            ->given($content = md5(uniqid()))
            ->and($len = strlen($content))

            ->assert('From beginning')
                ->if($this->newTestedInstance($content))
                ->then
                    ->integer($this->testedInstance->tell())
                        ->isIdenticalTo(0)

                    ->string($this->testedInstance->getContents())
                        ->isIdenticalTo($content)

                    ->integer($this->testedInstance->tell())
                        ->isIdenticalTo(0)

                    ->castToString($this->testedInstance)
                        ->isIdenticalTo($content)

                    ->integer($this->testedInstance->tell())
                        ->isIdenticalTo(0)

            ->assert('From a position')
                ->if($this->newTestedInstance($content))
                ->and($offset = rand(1, $len - 5))
                ->and($this->testedInstance->seek($offset))
                ->then
                    ->integer($this->testedInstance->tell())
                        ->isIdenticalTo($offset)

                    ->string($this->testedInstance->getContents())
                        ->isIdenticalTo(substr($content, $offset))

                    ->integer($this->testedInstance->tell())
                        ->isIdenticalTo($len)

                    ->castToString($this->testedInstance)
                        ->isIdenticalTo($content)

                    ->integer($this->testedInstance->tell())
                        ->isIdenticalTo(0)

            ->assert('Already at end')
                ->if($this->newTestedInstance($content))
                ->and($this->testedInstance->seek($len))
                ->then
                    ->integer($this->testedInstance->tell())
                        ->isIdenticalTo($len)

                    ->string($this->testedInstance->getContents())
                        ->isEmpty

                    ->integer($this->testedInstance->tell())
                        ->isIdenticalTo($len)

                    ->castToString($this->testedInstance)
                        ->isIdenticalTo($content)

                    ->integer($this->testedInstance->tell())
                        ->isIdenticalTo(0)
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
                    ->variable($this->testedInstance->seek(1))
                        ->isNull

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(1)

                    ->variable($this->testedInstance->seek(1))
                        ->isNull

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(1)

                    ->variable($this->testedInstance->seek($len + 10))
                        ->isNull

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo($len)

                    ->variable($this->testedInstance->seek(-1))
                        ->isNull

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(0)

                    ->variable($this->testedInstance->seek(0))
                        ->isNull

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(0)

                ->assert('Absolute position')
                    ->variable($this->testedInstance->seek(1, SEEK_SET))
                        ->isNull

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(1)

                    ->variable($this->testedInstance->seek(1, SEEK_SET))
                        ->isNull

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(1)

                    ->variable($this->testedInstance->seek($len + 10, SEEK_SET))
                        ->isNull

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo($len)

                    ->variable($this->testedInstance->seek(-1, SEEK_SET))
                        ->isNull

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(0)

                    ->variable($this->testedInstance->seek($len * -2, SEEK_SET))
                        ->isNull

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(0)

                ->assert('Relative position')
                    ->variable($this->testedInstance->seek(1, SEEK_CUR))
                        ->isNull

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(1)

                    ->variable($this->testedInstance->seek(1, SEEK_CUR))
                        ->isNull

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(2)

                    ->variable($this->testedInstance->seek($len + 10, SEEK_CUR))
                        ->isNull

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo($len)

                    ->variable($this->testedInstance->seek(-1, SEEK_CUR))
                        ->isNull

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo($len - 1)

                    ->variable($this->testedInstance->seek($len * -2, SEEK_CUR))
                        ->isNull

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo(0)

                ->assert('From end')
                    ->variable($this->testedInstance->seek(1, SEEK_END))
                        ->isNull

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo($len)

                    ->variable($this->testedInstance->seek(1, SEEK_END))
                        ->isNull

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo($len)

                    ->variable($this->testedInstance->seek($len + 10, SEEK_END))
                        ->isNull

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo($len)

                    ->variable($this->testedInstance->seek(-1, SEEK_END))
                        ->isNull

                    ->integer($this->testedInstance->tell())
                        ->isEqualTo($len - 1)

                    ->variable($this->testedInstance->seek($len * -2, SEEK_END))
                        ->isNull

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
                    ->isInstanceOf(Stancer\Exceptions\BadMethodCallException::class)
                    ->message
                        ->isIdenticalTo('This method is not implemented.')
        ;
    }
}
