<?php
declare(strict_types=1);

namespace Stancer\Traits;

use ReturnTypeWillChange;
use Stancer;

/**
 * Simple trait to handle aliases.
 */
trait AliasTrait
{
    /**
     * Handle getter and setter for every properties.
     *
     * @param string $method Method called.
     * @param mixed[] $arguments Arguments used during the call.
     * @return mixed
     * @throws Stancer\Exceptions\BadMethodCallException When an unhandled method is called.
     */
    public function __call(string $method, array $arguments): mixed
    {
        $name = Stancer\Helper::snakeCaseToCamelCase($method);

        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        }

        if (!$arguments) {
            $property = lcfirst(substr($name, 3));

            if (property_exists($this, $property)) {
                return $this->{$property};
            }
        }

        $message = sprintf('Method "%s::%s()" unknown', get_class($this), $method);

        throw new Stancer\Exceptions\BadMethodCallException($message);
    }

    /**
     * Handle aliases for static method.
     *
     * @param string $method Method called.
     * @param mixed[] $arguments Arguments used during the call.
     * @return mixed
     * @throws Stancer\Exceptions\BadMethodCallException When an unhandled method is called.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        $name = Stancer\Helper::snakeCaseToCamelCase($method);

        if (method_exists(static::class, $name)) {
            return static::{$name}(...$arguments);
        }

        $message = sprintf('Method "%s::%s()" unknown', static::class, $method);

        throw new Stancer\Exceptions\BadMethodCallException($message);
    }

    /**
     * Handle aliases.
     *
     * @param string $property Property called.
     * @return mixed
     */
    public function __get(string $property): mixed
    {
        $prop = Stancer\Helper::snakeCaseToCamelCase($property);

        if (method_exists($this, $prop)) {
            return $this->{$prop}();
        }

        $method = 'get' . $prop;

        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        if ($this instanceof Stancer\Core\AbstractObject) {
            return $this->{$method}();
        }

        return $this->{$prop};
    }

    /**
     * Setter alias.
     *
     * @param string $property Property to modify.
     * @param mixed $value New value.
     * @return void
     * @throws Stancer\Exceptions\BadPropertyAccessException When an unhandled property is called.
     */
    public function __set(string $property, mixed $value): void
    {
        $prop = Stancer\Helper::snakeCaseToCamelCase($property);
        $method = 'set' . $prop;
        $message = sprintf('Property "%s::$%s" unknown', get_class($this), $property);

        try {
            if (method_exists($this, $method)) {
                $this->{$method}($value);

                return;
            }

            if ($this instanceof Stancer\Core\AbstractObject) {
                $this->{$method}($value);

                return;
            }
        } catch (Stancer\Exceptions\BadMethodCallException $error) {
            if (strpos($error->getMessage(), 'You are not allowed to modify') === 0) {
                $message = $error->getMessage();
            }

            // Leave the next exception been thrown.
        }

        throw new Stancer\Exceptions\BadPropertyAccessException($message);
    }
}
