<?php
declare(strict_types=1);

namespace ild78\Core;

use DateTimeImmutable;
use DateTimeInterface;
use ild78;
use JsonSerializable;
use ReflectionClass;

/**
 * Manage common code between API object.
 *
 * @throws ild78\Exceptions\BadMethodCallException when calling unknown method.
 *
 * @property-read DateTimeImmutable|null $created
 * @property-read DateTimeImmutable|null $creationDate
 */
abstract class AbstractObject implements JsonSerializable
{
    public const BOOLEAN = 'boolean';
    public const INTEGER = 'integer';
    public const STRING = 'string';

    /** @var array<string, mixed> */
    protected $apiData;

    /** @var string */
    protected $endpoint = '';

    /** @var array */
    protected $dataModel = [];

    /** @var string|null */
    protected $id;

    /** @var boolean */
    protected $populated = false;

    /** @var string[] */
    protected $modified = [];

    /** @var boolean */
    protected $cleanModified = false;

    /** @var array<string, string> */
    protected $aliases = [];

    /**
     * Create or get an API object.
     *
     * @param string|array<string, mixed>|null $id Object id or data for hydration.
     */
    public function __construct($id = null)
    {
        $defaultModel = [
            'allowedValues' => null,
            'coerce' => null,
            'exception' => null,
            'exportable' => null,
            'list' => false,
            'restricted' => false,
            'required' => false,
            'size' => [
                'fixed' => null,
                'min' => null,
                'max' => null,
            ],
            'value' => null,
        ];

        $defaultValues = [
            'created' => [
                'restricted' => true,
                'type' => DateTimeImmutable::class,
            ],
        ];

        $this->dataModel = array_merge($this->dataModel, $defaultValues);

        foreach ($this->dataModel as &$data) {
            $data = array_merge($defaultModel, $data);
            $data['size'] = array_merge($defaultModel['size'], $data['size']);

            if (is_null($data['exportable'])) {
                $data['exportable'] = !$data['restricted'];
            }

            if (is_a($data['type'], DateTimeInterface::class, true)) {
                $data['coerce'] = Type\Helper::PARSE_DATE_TIME;
            }

            if (is_string($data['coerce'])) {
                $data['coerce'] = Type\Helper::get($data['coerce']);
            }
        }

        if (is_array($id)) {
            $this->hydrate($id);
        } else {
            $this->id = $id;
        }
    }

    /**
     * Handle getter and setter for every properties.
     *
     * @uses self::dataModelAdder() When method starts with `add`.
     * @uses self::dataModelGetter() When method starts with `get`.
     * @uses self::dataModelSetter() When method starts with `set`.
     * @param string $method Method called.
     * @param mixed[] $arguments Arguments used during the call.
     * @return mixed
     * @throws ild78\Exceptions\BadMethodCallException When an unhandled method is called.
     */
    public function __call(string $method, array $arguments)
    {
        $lower = $this->snakeCaseToCamelCase($method);

        if (array_key_exists($lower, $this->aliases)) {
            return $this->{$this->aliases[$lower]}();
        }

        $message = sprintf('Method "%s::%s()" unknown', get_class($this), $method);
        $action = substr($method, 0, 3);
        $property = lcfirst(substr($this->snakeCaseToCamelCase($method), 3));

        if ($action === 'set' && property_exists($this, $property)) {
            $tmp = $property;

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
        $prop = $this->snakeCaseToCamelCase($property);

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
            case 'creationDate':
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
    public function __set(string $property, $value): void
    {
        $prop = $this->snakeCaseToCamelCase($property);
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
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Convert `camelCase` text to `snake_case`
     *
     * @param string $text Text to convert.
     *
     * @return string
     */
    public function camelCaseToSnakeCase(string $text): string
    {
        $replace = function ($matches): string {
            return '_' . strtolower($matches[0]);
        };

        $rep = preg_replace_callback('`[A-Z]`', $replace, $text);

        if (!$rep) {
            return '';
        }

        return $rep;
    }

    /**
     * Create a fresh instance of an API object
     *
     * @param mixed[] $data Additional data for creation.
     * @return static
     */
    public static function create(array $data): self
    {
        $obj = new static();

        return $obj->hydrate($data);
    }

    /**
     * Add a value stored list in data model.
     *
     * @param string $property Property to set.
     * @param mixed $value Value to set.
     * @return $this
     * @uses self::dataModelGetter() To get actual values.
     * @uses self::dataModelSetter() To set new values.
     * @throws ild78\Exceptions\InvalidArgumentException When asking an unknown property.
     * @throws ild78\Exceptions\InvalidArgumentException If used on properties not declared as list.
     */
    public function dataModelAdder(string $property, $value): self
    {
        $model = $this->getModel($property);

        if (!$model) {
            throw new ild78\Exceptions\InvalidArgumentException(sprintf('Unknown property "%s"', $property));
        }

        if (!$model['list']) {
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
     * This was initially in `self::__call()` method, I removed it for simplicity.
     *
     * @param string $property Property to get.
     * @param boolean $autoPopulate Auto populate the property.
     * @return mixed
     * @throws ild78\Exceptions\InvalidArgumentException When asking an unknown property.
     */
    public function dataModelGetter(string $property, bool $autoPopulate = true)
    {
        $model = $this->getModel($property);

        if (!$model) {
            throw new ild78\Exceptions\InvalidArgumentException(sprintf('Unknown property "%s"', $property));
        }

        $value = $model['value'];

        if (is_null($value) && $autoPopulate && $this->isNotModified()) {
            $model = $this->populate()->getModel($property);
            $value = $model['value'];
        }

        if (is_null($value) && $model['list']) {
            return [];
        }

        if (!is_null($value) && is_a($model['type'], DateTimeInterface::class, true)) {
            $tz = ild78\Config::getGlobal()->getDefaultTimeZone();

            if ($tz) {
                if ($model['list']) {
                    foreach ($value as &$val) {
                        $val = $val->setTimezone($tz);
                    }
                } else {
                    $value = $value->setTimezone($tz);
                }
            }
        }

        return $value;
    }

    /**
     * Set a value in data model.
     *
     * This was initially in `self::__call()` method, I removed it for simplicity.
     *
     * @param string $property Property to set.
     * @param mixed $value Value to set.
     * @return $this
     * @uses self::validateDataModel() To check value's integrity.
     * @throws ild78\Exceptions\BadMethodCallException When setting a restricted property.
     * @throws ild78\Exceptions\InvalidArgumentException When asking an unknown property.
     * @throws ild78\Exceptions\InvalidArgumentException When the value do not match expected pattern.
     */
    public function dataModelSetter(string $property, $value): self
    {
        $model = $this->getModel($property);

        if (!$model) {
            throw new ild78\Exceptions\InvalidArgumentException(sprintf('Unknown property "%s"', $property));
        }

        if ($model['restricted']) {
            $message = sprintf('You are not allowed to modify "%s".', $property);

            if ($property === 'created') {
                $message = 'You are not allowed to modify the creation date.';
            }

            throw new ild78\Exceptions\BadMethodCallException($message);
        }

        $type = gettype($value);
        $coerce = function ($v) {
            return $v;
        };
        $coercedValues = [];

        if (is_callable($model['coerce'])) {
            $coerce = $model['coerce'];
        }

        if ($model['list']) {
            if ($type !== 'array') {
                $message = sprintf('Type mismatch, given "%s" expected "array".', $type);

                throw new ild78\Exceptions\InvalidArgumentException($message);
            }

            foreach ($value as $val) {
                $coercedValues[] = $coerce($val);
                $this->validateDataModel($property, $coerce($val));
            }
        } else {
            $coercedValues = $coerce($value);
            $this->validateDataModel($property, $coerce($value));
        }

        $this->dataModel[$property]['value'] = $coercedValues;
        $this->modified[] = $this->camelCaseToSnakeCase($property);

        return $this;
    }

    /**
     * Delete the current object in the API
     *
     * @return $this
     * @throws ild78\Exceptions\InvalidArgumentException When configuration is missing.
     */
    public function delete(): self
    {
        $request = new Request();
        $request->delete($this);

        ild78\Config::getGlobal()->getLogger()->info(sprintf('%s "%s" deleted', $this->getEntityName(), $this->id));

        $this->id = null;

        return $this;
    }

    /**
     * Return raw data from the API.
     *
     * @param string $attr Optional attribute name.
     * @return mixed
     */
    public function get(string $attr = null)
    {
        if ($attr && $this->apiData) {
            $prop = $this->camelCaseToSnakeCase($attr);

            if (array_key_exists($prop, $this->apiData)) {
                return $this->apiData[$prop];
            }

            return null;
        }

        return $this->apiData;
    }

    /**
     * Return creation date
     *
     * @return DateTimeInterface|null
     */
    public function getCreationDate(): ?DateTimeInterface
    {
        return $this->created;
    }

    /**
     * Return API endpoint
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Return entity name
     *
     * @return string
     */
    public function getEntityName(): string
    {
        $parts = explode('\\', get_class($this));
        $last = end($parts);

        return $last ?: '';
    }

    /**
     * Return object ID
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Return property model
     *
     * @param string|null $property Property name.
     * @return array|null
     *
     * @phpstan-return DataModelResolved|array<string, DataModelResolved>|null
     */
    public function getModel(string $property = null): ?array
    {
        if ($property) {
            if (array_key_exists($property, $this->dataModel)) {
                return $this->dataModel[$property];
            }

            return null;
        }

        return $this->dataModel;
    }

    /**
     * Return resource location.
     *
     * @return string
     */
    public function getUri(): string
    {
        $tmp = [
            ild78\Config::getGlobal()->getUri(),
            $this->getEndpoint(),
        ];

        if ($this->getId()) {
            $tmp[] = $this->getId();
        }

        $trim = function ($value): string {
            return trim($value, '/');
        };

        return implode('/', array_map($trim, $tmp));
    }

    /**
     * Hydrate the current object.
     *
     * @param array<string, mixed> $data Data for hydration.
     * @return $this
     */
    public function hydrate(array $data): self
    {
        foreach ($data as $key => $value) {
            $property = $this->snakeCaseToCamelCase($key);

            if ($property === 'id') {
                $this->id = $value;
            } elseif (array_key_exists($property, $this->dataModel)) {
                $types = [
                    static::BOOLEAN,
                    static::INTEGER,
                    static::STRING,
                ];

                $coerce = function ($v) {
                    return $v;
                };

                $model = $this->getModel($property);

                if (is_callable($model['coerce'])) {
                    $coerce = $model['coerce'];
                }

                if ($value && !in_array($model['type'], $types, true) && !is_object($value)) {
                    $class = $model['type'];

                    if ($model['list']) {
                        $list = [];

                        foreach ($value as $val) {
                            if (is_subclass_of($class, self::class)) {
                                $id = null;

                                if (is_string($val)) {
                                    $id = $val;
                                    $val = [];
                                }

                                $missing = true;

                                if (!is_array($model['value'])) {
                                    $this->dataModel[$property]['value'] = [];
                                }

                                foreach ($this->dataModel[$property]['value'] as $obj) {
                                    if ($obj->getId() === $id) {
                                        $obj->hydrate($val);

                                        $missing = false;
                                        $list[] = $obj;
                                    }
                                }

                                if ($missing) {
                                    $obj = new $class($id);

                                    $obj->cleanModified = $this->cleanModified;
                                    $obj->hydrate($val);

                                    $list[] = $obj;
                                }
                            } else {
                                $list[] = $coerce($val);
                            }
                        }

                        $this->$property = $list;
                    } else {
                        if (is_subclass_of($class, self::class)) {
                            $id = null;

                            if (is_string($value)) {
                                $id = $value;
                                $value = [
                                    'id' => $id,
                                ];
                            }

                            if (!$this->dataModel[$property]['value']) {
                                if (is_null($id)) {
                                    $this->dataModel[$property]['value'] = new $class();
                                } else {
                                    $this->dataModel[$property]['value'] = new $class($id);
                                }
                            }

                            if (is_array($value)) {
                                $this->dataModel[$property]['value']->cleanModified = $this->cleanModified;
                                $this->dataModel[$property]['value']->hydrate($value);
                            }
                        } else {
                            if ($this->dataModel[$property]['restricted']) {
                                $this->dataModel[$property]['value'] = $coerce($value);
                            } else {
                                $this->$property = $value;
                            }
                        }
                    }
                } else {
                    if ($this->dataModel[$property]['restricted'] || is_null($value) || is_array($value)) {
                        $this->dataModel[$property]['value'] = $coerce($value);
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

        if ($this->cleanModified) {
            $this->modified = [];
            $this->cleanModified = false;
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
    public function isModified(): bool
    {
        if (!!count($this->modified)) {
            return true;
        }

        $struct = $this->toArray();

        foreach ($struct as $prop => $value) {
            $type = gettype($value);

            if ($type === 'object' && $value->isModified()) {
                return true;
            }

            if ($type === 'array') {
                foreach ($value as $val) {
                    if (gettype($val) === 'object' && $val->isModified()) {
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
    public function isNotModified(): bool
    {
        return !$this->isModified();
    }

    /**
     * Return a array representation of the current object for a conversion as JSON.
     *
     * @uses self::toArray()
     * @return string|integer|boolean|null|array<string, mixed>
     */
    public function jsonSerialize()
    {
        if ($this->getId() && $this->isNotModified()) {
            return $this->getId();
        }

        $struct = $this->toArray();

        foreach ($struct as $prop => &$value) {
            $type = gettype($value);
            $supp = !in_array($prop, $this->modified, true);

            if ($type === 'object') {
                if (in_array($prop, $this->modified, true) || $value->isModified()) {
                    $supp = false;

                    if (method_exists($value, 'jsonSerialize')) {
                        $value = $value->jsonSerialize();
                    } elseif ($value instanceof DateTimeInterface) {
                        $value = $value->getTimestamp();
                    }
                }
            }

            if ($type === 'array') {
                $keepIt = false;

                foreach ($value as &$val) {
                    if (gettype($val) === 'object') {
                        if ($val instanceof self) {
                            $keepIt |= $val->isModified();
                            $val = $val->jsonSerialize();
                        } elseif ($val instanceof DateTimeInterface) {
                            $val = $val->getTimestamp();
                        }
                    }
                }

                $supp &= !$keepIt;
            }

            if ($supp) {
                unset($struct[$prop]);
            }
        }

        return $struct;
    }

    /**
     * Populate object with API data.
     *
     * This method is not supposed to be used directly, it will be used automatically when ask for some data.
     * The purpose of this method is to limit API call (and avoid reaching the rate limit).
     *
     * @return $this
     */
    public function populate(): self
    {
        if ($this->populated || !$this->getId() || !$this->getEndpoint()) {
            return $this;
        }

        $request = new Request();
        $response = $request->get($this);
        $body = json_decode($response, true);

        $this->cleanModified = true;
        $this->populated = true;
        $this->apiData = $body;

        $this->hydrate($body);

        $struct = $this->toArray();

        foreach ($struct as $prop => $value) {
            if (is_object($value) && $value instanceof self) {
                $tmp = $this->camelCaseToSnakeCase($prop);

                if (array_key_exists($tmp, $body)) {
                    $value->apiData = $body[$tmp];
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve an API object
     *
     * Added to simply transition from Stripe.
     *
     * @param string $id Identifier of the object.
     * @return static
     */
    static public function retrieve(string $id): self
    {
        return new static($id);
    }

    /**
     * Send the current object.
     *
     * @uses ild78\Core\Request::post()
     * @return $this
     * @throws ild78\Exceptions\InvalidArgumentException When all requirement are not provided.
     */
    public function send(): self
    {
        if ($this->isNotModified()) {
            return $this;
        }

        $request = new Request();
        $action = 'created';

        if ($this->getId()) {
            $action = 'updated';
            $response = $request->patch($this);
        } else {
            // phpcs:disable Squiz.PHP.DisallowBooleanStatement.Found
            $filter = function ($model): bool {
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

            $response = $request->post($this);
        }

        $this->populated = true;

        $body = json_decode($response, true);

        if ($body) {
            $this->cleanModified = true;

            $this->hydrate($body);
        }

        $this->modified = [];

        ild78\Config::getGlobal()->getLogger()->info(sprintf('%s "%s" %s', $this->getEntityName(), $this->id, $action));

        return $this;
    }

    /**
     * Convert `snake_case` text to `camelCase`
     *
     * @param string $text Text to convert.
     *
     * @return string
     */
    public function snakeCaseToCamelCase(string $text): string
    {
        $replace = function ($matches): string {
            return strtoupper(ltrim($matches[0], '_'));
        };

        $rep = preg_replace_callback('`_[a-z]`', $replace, $text);

        if (!$rep) {
            return '';
        }

        return $rep;
    }

    /**
     * Return a array representation of the current object.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $json = [];
        $data = [
            'id' => [
                'exportable' => true,
                'value' => $this->id,
            ],
        ];
        $data = array_merge($data, $this->dataModel);

        foreach ($data as $property => $infos) {
            if ($infos['exportable'] && $infos['value'] !== null) {
                $prop = $this->camelCaseToSnakeCase($property);

                $json[$prop] = $infos['value'];
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
    public function toJson(): string
    {
        $json = json_encode($this);

        if (!$json) {
            return '{}';
        }

        return $json;
    }

    /**
     * Return a string representation (as a JSON) of the current object.
     *
     * @return string
     */
    public function toString(): string
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
     * @return $this
     * @throws ild78\Exceptions\InvalidArgumentException When the value do not match expected pattern.
     */
    protected function validateDataModel(string $property, $value): self
    {
        $model = $this->getModel($property);

        if (!$model) {
            throw new ild78\Exceptions\InvalidArgumentException(sprintf('Unknown property "%s"', $property));
        }

        $exceptionList = [
            'ild78\\Exceptions\\Invalid' . ucfirst($property) . 'Exception',
            $model['exception'],
        ];
        $exceptionClass = ild78\Exceptions\InvalidArgumentException::class;

        foreach ($exceptionList as $except) {
            if ($except && class_exists($except)) {
                $exceptionClass = $except;
            }
        }

        $type = gettype($value);
        $length = $value;

        $mismatchType = $type !== $model['type'];

        if ($type === 'object') {
            $mismatchType = !($value instanceof $model['type']);
        }

        if ($mismatchType) {
            $params = [
                $type,
                $model['type'],
            ];

            $message = vsprintf('Type mismatch, given "%s" expected "%s".', $params);

            throw new $exceptionClass($message);
        }

        if ($model['allowedValues']) {
            $names = null;

            if (is_string($model['allowedValues'])) {
                $class = $model['allowedValues'];
                $ref = new ReflectionClass($class);

                $model['allowedValues'] = $ref->getConstants();

                $clean = function ($name) use ($class): string {
                    return $class . '::' . $name;
                };

                $names = implode(', ', array_map($clean, array_keys($model['allowedValues'])));
            }

            if (!$names) {
                $names = implode(', ', $model['allowedValues']);
            }

            $allowValue = function ($v) use ($model, $names, $property): string {
                if (!in_array($v, $model['allowedValues'], true)) {
                    $params = [
                        $v,
                        $property,
                        $names,
                    ];
                    $message = vsprintf('"%s" is not a valid %s, please use one of the following : %s', $params);

                    return $message;
                }

                return '';
            };

            $message = $allowValue($value);

            if ($message) {
                throw new $exceptionClass($message);
            }
        }

        if ($type === 'string') {
            $length = strlen($value);
        }

        $hasMax = false;
        $hasMin = false;
        $isLower = false;
        $isUpper = false;

        if (!is_null($model['size']['fixed']) && $model['size']['fixed'] !== $length) {
            $message = sprintf('A valid %s must have %d characters.', $property, $model['size']['fixed']);

            throw new $exceptionClass($message);
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
            $readable = str_replace(['_id', '_'], [' ID', ' '], $this->camelCaseToSnakeCase($property));

            $params = [
                $readable,
                ucfirst($readable),
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
                $message = vsprintf('A valid %1$s must be between %3$d and %4$d characters.', $params);

                if (!$hasMax) {
                    $message = vsprintf('A valid %1$s must be at least %3$d characters.', $params);
                }

                if (!$hasMin) {
                    $message = vsprintf('A valid %1$s must have less than %4$d characters.', $params);
                }
            }

            throw new $exceptionClass($message);
        }

        return $this;
    }
}
