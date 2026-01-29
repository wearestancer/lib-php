<?php

namespace Stancer\Stub\Traits;

class CapturableTrait extends Stancer\Stub\Core\StubObject
{
    use Stancer\CapturableTrait;
    protected $fakeMethodCount = 0;
    protected static $fakeStaticMethodCount = 0;

    public function aliasedMethod(): self
    {
        return $this->fakeMethod();
    }

    public function fakeMethod(): self
    {
        $this->fakeMethodCount++;

        return $this;
    }

    public function fakeMethodCallcount(): int
    {
        return $this->fakeMethodCount;
    }

    public static function fakeStaticMethod(): self
    {
        static::$fakeStaticMethodCount++;

        return new static();
    }

    public static function fakeStaticMethodCallCount(): int
    {
        return static::$fakeStaticMethodCount;
    }
}
