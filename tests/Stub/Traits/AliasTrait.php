<?php

namespace Stancer\Stub\Traits;

use Stancer;

class AliasTrait extends Stancer\Stub\Core\StubObject
{
    use Stancer\Traits\AliasTrait;

    protected $fakeMethodCount = 0;
    protected static $fakeStaticMethodCount = 0;

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

    /**
     * Return methods/properties aliases.
     *
     * @param string $name Searched method or property.
     *
     * @return string|false
     */
    #[\ReturnTypeWillChange]
    protected function findAlias(string $name)
    {
        if ($name === 'aliasedMethod') {
            return 'fakeMethod';
        }

        return false;
    }
}
