<?php
declare(strict_types=1);

namespace ild78\Api;

use DateTime;
use GuzzleHttp;
use ild78;
use JsonSerializable;

/**
 * Manage common code between API object
 *
 * @throws ild78\Exceptions\BadMethodCallException when calling unknown method
 */
abstract class AbstractObject implements JsonSerializable
{
    const BOOLEAN = 'boolean';
    const INTEGER = 'integer';
    const STRING = 'string';

    /** @var string */
    protected $endpoint = '';

    /** @var array */
    protected $dataModel = [];

    /** @var string */
    protected $id;

    /** @var DateTime */
    protected $created;

    /** @var boolean */
    protected $populated = false;

    /** @var boolean */
    protected $modified = false;

    /** @var array */
    protected $aliases = [];

    /**
     * Create or get an API object
     *
     * @param string|null $id Object id.
     * @return self
     */
    public function __construct(string $id = null)
    {
        $defaults = [
            'list' => false,
            'size' => [
                'fixed' => null,
                'min' => null,
                'max' => null,
            ],
            'restricted' => false,
            'required' => false,
            'value' => null,
        ];

        foreach ($this->dataModel as &$data) {
            $data = array_merge($defaults, $data);
            $data['size'] = array_merge($defaults['size'], $data['size']);
        }

        $this->id = $id;
    }

    /**
     * Handle getter and setter for every properties.
     *
     * @uses self::dataModelAdder() When method starts with `add`.
     * @uses self::dataModelGetter() When method starts with `get`.
     * @uses self::dataModelSetter() When method starts with `set`.
     * @param string $method Method called.
     * @param array $arguments Arguments used during the call.
     * @return mixed
     * @throws ild78\Exceptions\BadMethodCallException When an unhandled method is called.
     */
    public function __call(string $method, array $arguments)
    {
        $lower = strtolower($method);

        if (array_key_exists($lower, $this->aliases)) {
            return $this->{$this->aliases[$lower]}();
        }

        $message = sprintf('Method "%s::%s()" unknown', get_class($this), $method);
        $action = substr($method, 0, 3);
        $property = lcfirst(substr($method, 3));

        if ($action === 'set' && property_exists($this, $property)) {
            $tmp = $property;

            if ($property === 'created') {
                $tmp = 'creation date';
            }

            if ($property === 'dataModel') {
                $tmp = 'data model';
            }

            $message = sprintf('You are not allowed to modify the %s.', $tmp);
        }

        if (array_key_exists($property, $this->dataModel)) {
            if ($action === 'get') {
                return $this->dataModelGetter($property);
            }

            if ($action === 'set') {
                return $this->dataModelSetter($property, $arguments[0]);
            }

            if ($action === 'add') {
                return $this->dataModelAdder($property, $arguments[0]);
            }
        }

        throw new ild78\Exceptions\BadMethodCallException($message);
    }

    /**
     * Aliases
     *
     * @param string $property Property called.
     * @return mixed
     */
    public function __get(string $property)
    {
        $prop = strtolower($property);

        if (array_key_exists($prop, $this->aliases)) {
            return $this->{$this->aliases[$prop]}();
        }

        if (array_key_exists($prop, $this->dataModel)) {
            return $this->{'get' . $prop}();
        }

        if (property_exists($this, $prop)) {
            return $this->{'get' . $prop}();
        }

        switch ($prop) {
            case 'creationdate':
                return $this->getCreationDate();

            default:
                return $this->$prop();
        }
    }

    /**
     * Setter alias
     *
     * @param string $property Property to modify.
     * @param mixed $value New value.
     * @return void
     */
    public function __set(string $property, $value) : void
    {
        $prop = strtolower($property);
        $method = 'set' . $prop;

        if (method_exists($this, $method)) {
            $this->{$method}($value);
        }

        if (array_key_exists($prop, $this->dataModel)) {
            $this->{$method}($value);
        }
    }

    /**
     * Return a string representation (as a JSON) of the current object.
     *
     * @uses self::toString()
     * @return string
     */
    public function __toString() : string
    {
        return $this->toString();
    }

    /**
     * Create a fresh instance of an API object
     *
     * @param array $data Additionnal data for creation.
     * @return self
     */
    public static function create(array $data) : self
    {
        $obj = new static();

        return $obj->hydrate($data);
    }

    /**
     * Add a value stored list in data model.
     *
     * @param string $property Property to set.
     * @param mixed $value Value to set.
     * @return self
     * @uses self::dataModelGetter() To get actual values.
     * @uses self::dataModelSetter() To set new values.
     * @throws ild78\Exceptions\InvalidArgumentException When asking an unknown property.
     * @throws ild78\Exceptions\InvalidArgumentException If used on properties not declared as list.
     */
    public function dataModelAdder(string $property, $value) : self
    {
        if (!array_key_exists($property, $this->dataModel)) {
            throw new ild78\Exceptions\InvalidArgumentException(sprintf('Unknown property "%s"', $property));
        }

        if (!$this->dataModel[$property]['list']) {
            $message = sprintf('"%s" is not a list, you can not add elements in it.', $property);

            throw new ild78\Exceptions\InvalidArgumentException($message);
        }

        $values = $this->dataModelGetter($property);
        $values[] = $value;

        return $this->dataModelSetter($property, $values);
    }

    /**
     * Get a value stored in data model.
     *
     * This was initialy in `self::__call()` method, I removed it for simplicity.
     *
     * @param string $property Property to get.
     * @return mixed
     * @throws ild78\Exceptions\InvalidArgumentException When asking an unknown property.
     */
    public function dataModelGetter(string $property)
    {
        if (!array_key_exists($property, $this->dataModel)) {
            throw new ild78\Exceptions\InvalidArgumentException(sprintf('Unknown property "%s"', $property));
        }

        $value = $this->dataModel[$property]['value'];

        if (is_null($value) && $this->id && $this->isNotModified()) {
            $value = $this->populate()->dataModel[$property]['value'];
        }

        if (is_null($value) && $this->dataModel[$property]['list']) {
            return [];
        }

        return $value;
    }

    /**
     * Set a value in data model.
     *
     * This was initialy in `self::__call()` method, I removed it for simplicity.
     *
     * @param string $property Property to set.
     * @param mixed $value Value to set.
     * @return self
     * @uses self::validateDataModel() To check value's integrity.
     * @throws ild78\Exceptions\InvalidArgumentException When asking an unknown property.
     * @throws ild78\Exceptions\InvalidArgumentException When setting a restricted property.
     * @throws ild78\Exceptions\InvalidArgumentException When the value do not match expected pattern.
     */
    public function dataModelSetter(string $property, $value) : self
    {
        if (!array_key_exists($property, $this->dataModel)) {
            throw new ild78\Exceptions\InvalidArgumentException(sprintf('Unknown property "%s"', $property));
        }

        if ($this->dataModel[$property]['restricted']) {
            $message = sprintf('You are not allowed to modify "%s".', $property);

            throw new ild78\Exceptions\InvalidArgumentException($message);
        }

        $type = gettype($value);

        if ($this->dataModel[$property]['list']) {
            if ($type !== 'array') {
                $message = sprintf('Type mismatch, given "%s" expected "array".', $type);

                throw new ild78\Exceptions\InvalidArgumentException($message);
            }

            foreach ($value as $val) {
                $this->validateDataModel($property, $val);
            }
        } else {
            $this->validateDataModel($property, $value);
        }

        $this->dataModel[$property]['value'] = $value;
        $this->modified = true;

        return $this;
    }

    /**
     * Delete the current object in the API
     *
     * @return self
     */
    public function delete() : self
    {
        $request = new Request();
        $request->delete($this);

        $this->id = null;

        return $this;
    }

    /**
     * Return creation date
     *
     * @return DateTime|null
     */
    public function getCreationDate() : ?DateTime
    {
        $date = $this->created;

        if (is_null($date) && $this->id && !$this->populated) {
            $date = $this->populate()->created;
        }

        return $date;
    }

    /**
     * Return API endpoint
     *
     * @return string
     */
    public function getEndpoint() : string
    {
        return $this->endpoint;
    }

    /**
     * Return object ID
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return property model
     *
     * @param string|null $property Property name.
     * @return array
     */
    public function getModel(string $property = null)
    {
        $model = $this->populate()->dataModel;

        foreach ($model as $key => &$infos) {
            $infos = array_diff_key($infos, ['value' => null]);
        }

        if ($property) {
            return $model[$property];
        }

        return $model;
    }

    /**
     * Return ressource location
     *
     * @return string
     */
    public function getUri() : string
    {
        $tmp = [
            Config::getGlobal()->getUri(),
            $this->getEndpoint(),
        ];

        if ($this->getId()) {
            $tmp[] = $this->getId();
        }

        $trim = function ($value) {
            return trim($value, '/');
        };

        return implode('/', array_map($trim, $tmp));
    }

    /**
     * Hydrate the current object.
     *
     * @param array $data Data for hydratation.
     * @param boolean $modified Do we need to modify the flag.
     * @return self
     */
    public function hydrate(array $data, bool $modified = true) : self
    {
        foreach ($data as $key => $value) {
            $property = $key;

            if (strpos($key, '_') !== false) {
                $replace = function ($matches) {
                    return trim(strtoupper($matches[0]), '_');
                };

                $property = preg_replace_callback('`_\w`', $replace, $key);
            }

            if ($property === 'id') {
                $this->id = $value;
            } elseif ($property === 'created') {
                $this->created = new DateTime('@' . $value);
            } elseif (array_key_exists($property, $this->dataModel)) {
                $types = [
                    static::BOOLEAN,
                    static::INTEGER,
                    static::STRING,
                ];

                if ($value && !in_array($this->dataModel[$property]['type'], $types, true) && !is_object($value)) {
                    $id = null;

                    if (is_string($value)) {
                        $id = $value;
                        $value = [
                            'id' => $id,
                        ];
                    }

                    if (!$this->dataModel[$property]['value']) {
                        $class = $this->dataModel[$property]['type'];
                        $this->dataModel[$property]['value'] = new $class($id);
                    }

                    if (is_array($value)) {
                        $this->dataModel[$property]['value']->hydrate($value);
                    }

                    $this->dataModel[$property]['value']->modified = $modified;
                } else {
                    if ($this->dataModel[$property]['restricted'] || is_null($value) || !$modified) {
                        $this->dataModel[$property]['value'] = $value;
                    } else {
                        $this->$property = $value;
                    }
                }

                if ($this->populated) {
                    if ($this->dataModel[$property]['value'] instanceof self) {
                        $this->dataModel[$property]['value']->populated = $this->populated;
                    }

                    if (is_array($this->dataModel[$property]['value'])) {
                        foreach ($this->dataModel[$property]['value'] as $obj) {
                            if ($obj instanceof self) {
                                $obj->populated = $this->populated;
                            }
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Indicate if the current object is modified
     *
     * This exists only to perform a deeper search into the current object to find inner updated object.
     *
     * @return boolean
     */
    public function isModified() : bool
    {
        if ($this->modified) {
            return true;
        }

        $struct = $this->toArray();

        foreach ($struct as $prop => $value) {
            $type = gettype($value);

            if ($type === 'object' && $value->modified) {
                return true;
            }

            if ($type === 'array') {
                foreach ($value as $val) {
                    if (gettype($val) === 'object' && $val->modified) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Indicate if the current object is not modified
     *
     * @return boolean
     */
    public function isNotModified() : bool
    {
        return !$this->isModified();
    }

    /**
     * Return a array representation of the current object for a convertion as JSON.
     *
     * @uses self::toArray()
     * @return string|array
     */
    public function jsonSerialize()
    {
        if ($this->getId() && $this->isNotModified()) {
            return $this->getId();
        }

        $struct = $this->toArray();

        foreach ($struct as $prop => &$value) {
            $type = gettype($value);

            if ($type === 'object') {
                $value = $value->jsonSerialize();
            }

            if ($type === 'array') {
                foreach ($value as &$val) {
                    if (gettype($val) === 'object') {
                        $val = $val->jsonSerialize();
                    }
                }
            }
        }

        if (array_key_exists('id', $struct)) {
            unset($struct['id']);
        }

        return $struct;
    }

    /**
     * Populate object with API data.
     *
     * This method is not supposed to be used directly, it will be used automaticaly when ask for some data.
     * The purpose of this method is to limitate API call (and  avoid reaching the rate limit).
     *
     * @return self
     */
    public function populate() : self
    {
        if ($this->id && $this->getEndpoint() && (!$this->populated || $this->modified)) {
            $request = new Request();
            $response = $request->get($this);
            $body = json_decode($response, true);
            $this->hydrate($body);

            $this->populated = true;
            $this->modified = false;
        }

        return $this;
    }

    /**
     * Retrieve an API object
     *
     * Added to simply transition from Stripe.
     *
     * @param string $id Identifier of the object.
     * @return self
     */
    static public function retrieve(string $id) : self
    {
        return new static($id);
    }

    /**
     * Save the current object.
     *
     * @uses Request::post()
     * @return self
     * @throws ild78\Exceptions\InvalidArgumentException When all requirement are not provided.
     */
    public function save() : self
    {
        if ($this->modified) {
            // phpcs:disable Squiz.PHP.DisallowBooleanStatement.Found
            $filter = function ($model) {
                return $model['required'] && is_null($model['value']);
            };
            // phpcs:enable
            $required = array_filter($this->dataModel, $filter);

            if ($required) {
                $keys = array_keys($required);
                sort($keys);
                $properties = implode(', ', $keys);
                $message = sprintf('You need to provide a value for : %s', $properties);

                throw new ild78\Exceptions\InvalidArgumentException($message);
            }

            $request = new Request();

            if ($this->getId() && $this->isModified()) {
                $response = $request->patch($this);
            } else {
                $response = $request->post($this);
            }

            $body = json_decode($response, true);

            if ($body) {
                $this->hydrate($body);
            }

            $this->populated = true;
            $this->modified = false;
        }

        return $this;
    }

    /**
     * Return a array representation of the current object.
     *
     * @return string
     */
    public function toArray() : array
    {
        $json = [];
        $data = [
            'id' => [
                'restricted' => false,
                'value' => $this->id,
            ],
        ];
        $data = array_merge($data, $this->dataModel);

        $replace = function ($matches) {
            return '_' . strtolower($matches[0]);
        };

        foreach ($data as $property => $infos) {
            $value = $infos['value'];

            if ($value !== null && !$infos['restricted']) {
                $prop = preg_replace_callback('`[A-Z]`', $replace, $property);

                $json[$prop] = $value;
            }
        }

        return $json;
    }

    /**
     * Return a JSON representation of the current object.
     *
     * @uses self::__toString()
     * @return string
     */
    public function toJson() : string
    {
        return json_encode($this);
    }

    /**
     * Return a string representation (as a JSON) of the current object.
     *
     * @return string
     */
    public function toString() : string
    {
        return $this->toJson();
    }

    /**
     * Validate a value in a defined model
     *
     * We do not handle array here, this method only check one value.
     * Array are checked in `self::dataModelSetter()`.
     *
     * @param string $property Property reference.
     * @param mixed $value Value to validate.
     * @return self
     * @throws ild78\Exceptions\InvalidArgumentException When the value do not match expected pattern.
     */
    protected function validateDataModel(string $property, $value) : self
    {
        $model = $this->dataModel[$property];

        $type = gettype($value);
        $length = $value;

        if ($type === 'object') {
            $type = get_class($value);
        }

        if ($type !== $model['type']) {
            $params = [
                $type,
                $model['type'],
            ];

            $message = vsprintf('Type mismatch, given "%s" expected "%s".', $params);

            throw new ild78\Exceptions\InvalidArgumentException($message);
        }

        if ($type === 'string') {
            $length = strlen($value);
        }

        $hasMax = false;
        $hasMin = false;
        $isLower = false;
        $isUpper = false;

        if (array_key_exists('fixed', $model['size'])
            && !is_null($model['size']['fixed'])
            && $model['size']['fixed'] !== $length
        ) {
            $message = sprintf('A valid %s must have %d characters.', $property, $model['size']['fixed']);

            throw new ild78\Exceptions\InvalidArgumentException($message);
        }

        if (!is_null($model['size']['max'])) {
            $hasMax = true;
            $isUpper = $length > $model['size']['max'];
        }

        if (!is_null($model['size']['min'])) {
            $hasMin = true;
            $isLower = $length < $model['size']['min'];
        }

        if ($isLower || $isUpper) {
            $params = [
                $property,
                ucfirst($property),
                $model['size']['min'],
                $model['size']['max'],
            ];

            if ($type === 'integer') {
                $message = vsprintf('%2$s must be ', $params);
                $value = null;

                if ($isLower || ($hasMin && $hasMax)) {
                    $message .= vsprintf('greater than or equal to %3$d', $params);

                    if ($hasMax) {
                        $message .= ' and be ';
                    }
                }

                if ($isUpper || ($hasMin && $hasMax)) {
                    $message .= vsprintf('less than or equal to %4$d', $params);
                }

                $message .= '.';
            } else {
                if ($property === 'orderId') {
                    $params[0] = 'order ID';
                }

                $message = vsprintf('A valid %1$s must be between %3$d and %4$d characters.', $params);

                if (!$hasMax) {
                    $message = vsprintf('A valid %1$s must be at least %3$d characters.', $params);
                }

                if (!$hasMin) {
                    $message = vsprintf('A valid %1$s must have less than %4$d characters.', $params);
                }
            }

            throw new ild78\Exceptions\InvalidArgumentException($message);
        }

        return $this;
    }
}
