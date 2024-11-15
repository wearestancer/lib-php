<?php
declare(strict_types=1);

namespace Stancer\Core;

use BackedEnum;
use DateTimeImmutable;
use DateTimeInterface;
use Stancer;
use JsonSerializable;
use ReflectionClass;

/**
 * Manage common code between API object.
 *
 * @method ?\DateTimeImmutable getCreated() Get creation date.
 * @method ?\DateTimeImmutable get_created() Get creation date.
 * @method ?\DateTimeImmutable get_creation_date() Get creation date.
 * @method string get_endpoint() Get API endpoint.
 * @method string get_entity_name() Get entity name.
 * @method ?string get_id() Get object ID.
 * @method ?array<mixed> get_model(string $property = null) Return property model.
 * @method string get_uri() Get entity resource location.
 * @method boolean is_modified() Indicate if the current object is modified.
 * @method boolean is_not_modified() Indicate if the current object is not modified.
 * @method array<mixed> to_array() Return a array representation of the current object.
 * @method string to_json() Return a JSON representation of the current object.
 * @method string to_string() Return a string representation (as a JSON) of the current object.
 * @property-read ?\DateTimeImmutable $created Creation date.
 * @property-read ?\DateTimeImmutable $creationDate Creation date.
 * @property-read ?\DateTimeImmutable $creation_date Creation date.
 * @property-read string $endpoint API endpoint.
 * @property-read string $entityName Entity name.
 * @property-read string $entity_name Entity name.
 * @property-read ?string $id Object ID.
 * @property-read boolean $isModified Alias for `Stancer\Core\AbstractObject::isModified()`.
 * @property-read boolean $isNotModified Alias for `Stancer\Core\AbstractObject::isNotModified()`.
 * @property-read boolean $is_modified Alias for `Stancer\Core\AbstractObject::isModified()`.
 * @property-read boolean $is_not_modified Alias for `Stancer\Core\AbstractObject::isNotModified()`.
 * @property-read ?mixed $jsonSerialize Alias for `Stancer\Core\AbstractObject::jsonSerialize()`.
 * @property-read ?mixed $json_serialize Alias for `Stancer\Core\AbstractObject::jsonSerialize()`.
 * @property-read array<mixed> $toArray Alias for `Stancer\Core\AbstractObject::toArray()`.
 * @property-read string $toJson Alias for `Stancer\Core\AbstractObject::toJson()`.
 * @property-read string $toString Alias for `Stancer\Core\AbstractObject::toString()`.
 * @property-read array<mixed> $to_array Alias for `Stancer\Core\AbstractObject::toArray()`.
 * @property-read string $to_json Alias for `Stancer\Core\AbstractObject::toJson()`.
 * @property-read string $to_string Alias for `Stancer\Core\AbstractObject::toString()`.
 * @property-read string $uri Entity resource location.
 *
 * @throws Stancer\Exceptions\BadMethodCallException When calling an unknown method.
 * @throws Stancer\Exceptions\BadPropertyAccessException When calling an unknown property.
 */
#[Stancer\Core\Documentation\AddProperty(
    'created',
    description: 'Creation date',
    restricted: true,
    type: DateTimeImmutable::class,
)]
abstract class AbstractObject implements JsonSerializable
{
    use Stancer\Traits\AliasTrait;

    #[Stancer\WillChange\PHP8_3\TypedClassConstants]
    final public const BOOLEAN = 'boolean';
    #[Stancer\WillChange\PHP8_3\TypedClassConstants]
    final public const FLOAT = 'float';
    #[Stancer\WillChange\PHP8_3\TypedClassConstants]
    final public const INTEGER = 'integer';
    #[Stancer\WillChange\PHP8_3\TypedClassConstants]
    final public const STRING = 'string';

    #[Stancer\WillChange\PHP8_3\TypedClassConstants]
    public const ENDPOINT = '';

    /**
     * @var array|string
     * @phpstan-var array<string, mixed>|string
     */
    protected array|string $apiData = [];

    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected array $dataModel = [];

    protected ?string $id = null;

    protected bool $populated = false;

    /** @var string[] */
    protected array $modified = [];

    protected bool $cleanModified = false;

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
            'format' => null,
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
                'desc' => 'Creation date',
                'restricted' => true,
                'type' => DateTimeImmutable::class,
            ],
        ];

        $this->dataModel = array_merge($this->dataModel, $defaultValues);

        /** @var DataModel $data */
        foreach ($this->dataModel as &$data) {
            $size = array_merge($defaultModel['size'], $data['size'] ?? []);
            $data = array_merge($defaultModel, $data, ['size' => $size]);

            if (is_null($data['exportable'])) {
                $data['exportable'] = !$data['restricted'];
            }

            if (is_a($data['type'], DateTimeInterface::class, true)) {
                $data['coerce'] = Type\Helper::PARSE_DATE_TIME;

                if (!$data['format']) {
                    $data['format'] = Type\Helper::UNIX_TIMESTAMP;
                }
            }

            foreach (['coerce', 'format'] as $type) {
                if (is_string($data[$type])) {
                    $data[$type] = Type\Helper::get($data[$type]);
                }
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
     * @throws Stancer\Exceptions\BadMethodCallException When an unhandled method is called.
     */
    public function __call(string $method, array $arguments): mixed
    {
        $name = Stancer\Helper::snakeCaseToCamelCase($method);

        if ($name !== $method && method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        }

        $message = sprintf('Method "%s::%s()" unknown', get_class($this), $method);
        $action = substr($method, 0, 3);
        $property = lcfirst(substr(Stancer\Helper::snakeCaseToCamelCase($method), 3));

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

        throw new Stancer\Exceptions\BadMethodCallException($message);
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
     * Create a fresh instance of an API object.
     *
     * @param mixed[] $data Additional data for creation.
     * @return static
     */
    public static function create(array $data): static
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
     * @throws Stancer\Exceptions\InvalidArgumentException When asking an unknown property.
     * @throws Stancer\Exceptions\InvalidArgumentException If used on properties not declared as list.
     */
    protected function dataModelAdder(string $property, mixed $value): static
    {
        $model = $this->getModel($property);

        if (!$model) {
            throw new Stancer\Exceptions\InvalidArgumentException(sprintf('Unknown property "%s"', $property));
        }

        if (!$model['list']) {
            $message = sprintf('"%s" is not a list, you can not add elements in it.', $property);

            throw new Stancer\Exceptions\InvalidArgumentException($message);
        }

        $values = $this->dataModelGetter($property);

        if (is_array($values)) {
            $values[] = $value;
        } else {
            $values = [$value];
        }

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
     * @throws Stancer\Exceptions\InvalidArgumentException When asking an unknown property.
     */
    protected function dataModelGetter(string $property, bool $autoPopulate = true): mixed
    {
        $model = $this->getModel($property);

        if (!$model) {
            throw new Stancer\Exceptions\InvalidArgumentException(sprintf('Unknown property "%s"', $property));
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
            $tz = Stancer\Config::getGlobal()->getDefaultTimeZone();

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
     * @throws Stancer\Exceptions\BadMethodCallException When setting a restricted property.
     * @throws Stancer\Exceptions\InvalidArgumentException When asking an unknown property.
     * @throws Stancer\Exceptions\InvalidArgumentException When the value do not match expected pattern.
     */
    protected function dataModelSetter(string $property, mixed $value): static
    {
        $model = $this->getModel($property);

        if (!$model) {
            throw new Stancer\Exceptions\InvalidArgumentException(sprintf('Unknown property "%s"', $property));
        }

        if ($model['restricted']) {
            $message = sprintf('You are not allowed to modify "%s".', $property);

            if ($property === 'created') {
                $message = 'You are not allowed to modify the creation date.';
            }

            throw new Stancer\Exceptions\BadMethodCallException($message);
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
            if (!is_array($value)) {
                $message = sprintf('Type mismatch, given "%s" expected "array".', $type);

                throw new Stancer\Exceptions\InvalidArgumentException($message);
            }

            foreach ($value as $val) {
                $coercedValues[] = $this->validateDataModel($property, $coerce($val));
            }
        } else {
            $coercedValues = $this->validateDataModel($property, $coerce($value));
        }

        $this->dataModel[$property]['value'] = $coercedValues;
        $this->modified[] = Stancer\Helper::camelCaseToSnakeCase($property);

        return $this;
    }

    /**
     * Delete the current object in the API.
     *
     * @return $this
     * @throws Stancer\Exceptions\InvalidArgumentException When configuration is missing.
     */
    public function delete(): static
    {
        $request = new Request();
        $request->delete($this);

        Stancer\Config::getGlobal()->getLogger()->info(sprintf('%s "%s" deleted', $this->getEntityName(), $this->id));

        $this->id = null;

        return $this;
    }

    /**
     * Return raw data from the API.
     *
     * @param string $attr Optional attribute name.
     * @return mixed
     */
    public function get(string $attr = null): mixed
    {
        if ($attr && $this->apiData) {
            $prop = Stancer\Helper::camelCaseToSnakeCase($attr);

            if (is_array($this->apiData) && array_key_exists($prop, $this->apiData)) {
                return $this->apiData[$prop] ?? null;
            }

            return null;
        }

        if ($this->apiData) {
            return $this->apiData;
        }

        return null;
    }

    /**
     * Return creation date.
     *
     * @return DateTimeImmutable|null
     */
    #[Stancer\Core\Documentation\FormatProperty(
        description: 'Creation date',
        restricted: true,
        type: DateTimeImmutable::class,
    )]
    public function getCreationDate(): ?DateTimeImmutable
    {
        return $this->created;
    }

    /**
     * Return API endpoint.
     *
     * @return string
     */
    #[Stancer\Core\Documentation\FormatProperty(description: 'API endpoint', nullable: false, restricted: true)]
    public function getEndpoint(): string
    {
        return static::ENDPOINT;
    }

    /**
     * Return entity name.
     *
     * @return string
     */
    #[Stancer\Core\Documentation\FormatProperty(description: 'Entity name', nullable: false, restricted: true)]
    public function getEntityName(): string
    {
        $parts = explode('\\', get_class($this));
        $last = end($parts);

        return $last ?: '';
    }

    /**
     * Return object ID.
     *
     * @return string|null
     */
    #[Stancer\Core\Documentation\FormatProperty(description: 'Object ID', restricted: true)]
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Return property model.
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

            $prop = Stancer\Helper::snakeCaseToCamelCase($property);

            if ($prop !== $property) {
                return $this->getModel($prop);
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
    #[Stancer\Core\Documentation\FormatProperty(
        description: 'Entity resource location',
        nullable: false,
        restricted: true,
    )]
    public function getUri(): string
    {
        $tmp = [
            Stancer\Config::getGlobal()->getUri(),
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
    public function hydrate(array $data): static
    {
        foreach ($data as $key => $value) {
            $property = Stancer\Helper::snakeCaseToCamelCase($key);

            if ($property === 'id') {
                if (is_string($value) || is_null($value)) {
                    $this->id = $value;
                }
            } elseif (array_key_exists($property, $this->dataModel)) {
                $types = [
                    static::BOOLEAN,
                    static::INTEGER,
                    static::STRING,
                ];

                $coerce = fn($v) => $v;
                $model = $this->getModel($property);

                if (is_callable($model['coerce'])) {
                    $coerce = $model['coerce'];
                }

                if (is_a($model['type'], BackedEnum::class, true)) {
                    $coerce = fn($data) => is_string($data) ? $model['type']::from($data) : $data;
                }

                if ($value && !in_array($model['type'], $types, true) && !is_object($value)) {
                    $class = $model['type'];

                    if ($model['list']) {
                        $list = [];

                        if (is_array($value)) {
                            foreach ($value as $val) {
                                if (is_subclass_of($class, self::class)) {
                                    $id = null;

                                    if (is_string($val)) {
                                        $id = $val;
                                        $val = [];
                                    }

                                    $missing = true;

                                    if (
                                        !array_key_exists('value', $this->dataModel[$property])
                                        || !is_array($this->dataModel[$property]['value'])
                                    ) {
                                        $this->dataModel[$property]['value'] = [];
                                    } else {
                                        foreach ($this->dataModel[$property]['value'] as $obj) {
                                            if (is_object($obj) && $obj instanceof $this && $obj->getId() === $id) {
                                                /** @var array<string, mixed> $val */
                                                $obj->hydrate($val);

                                                $missing = false;
                                                $list[] = $obj;
                                            }
                                        }
                                    }

                                    if ($missing) {
                                        $obj = new $class($id);

                                        $obj->cleanModified = $this->cleanModified;
                                        /** @var array<string, mixed> $val */
                                        $obj->hydrate($val);

                                        $list[] = $obj;
                                    }
                                } else {
                                    $list[] = $coerce($val);
                                }
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

                            if (
                                !array_key_exists('value', $this->dataModel[$property])
                                || !$this->dataModel[$property]['value']
                            ) {
                                $this->dataModel[$property]['value'] = new $class($id);
                            }

                            if (
                                is_array($value)
                                && array_key_exists('value', $this->dataModel[$property])
                                && $this->dataModel[$property]['value'] instanceof self
                            ) {
                                $this->dataModel[$property]['value']->cleanModified = $this->cleanModified;
                                /** @var array<string, mixed> $value */
                                $this->dataModel[$property]['value']->hydrate($value);
                            }
                        } else {
                            if (
                                array_key_exists('restricted', $this->dataModel[$property])
                                && $this->dataModel[$property]['restricted']
                            ) {
                                $this->dataModel[$property]['value'] = $coerce($value);
                            } else {
                                $this->$property = $value;
                            }
                        }
                    }
                } else {
                    $restricted = false;

                    if (array_key_exists('restricted', $this->dataModel[$property])) {
                        $restricted = $this->dataModel[$property]['restricted'];
                    }

                    if (
                        $restricted
                        || is_null($value)
                        || is_array($value)
                    ) {
                        $this->dataModel[$property]['value'] = $coerce($value);
                    } else {
                        $this->$property = $value;
                    }
                }

                if ($this->populated) {
                    if (
                        array_key_exists('value', $this->dataModel[$property])
                        && $this->dataModel[$property]['value'] instanceof self
                    ) {
                        $this->dataModel[$property]['value']->populated = $this->populated;
                    }

                    if (
                        array_key_exists('value', $this->dataModel[$property])
                        && is_array($this->dataModel[$property]['value'])
                    ) {
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
     * Indicate if the current object is modified.
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

        foreach ($struct as $value) {
            if (is_object($value) && $value instanceof self && $value->isModified()) {
                return true;
            }

            if (is_array($value)) {
                foreach ($value as $val) {
                    if (is_object($val) && $val instanceof self && $val->isModified()) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Indicate if the current object is not modified.
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
    public function jsonSerialize(): mixed
    {
        if ($this->getId() && $this->isNotModified()) {
            return $this->getId();
        }

        $struct = $this->toArray();

        foreach ($struct as $prop => &$value) {
            $supp = !in_array($prop, $this->modified, true);
            $model = $this->getModel($prop);
            $format = $model['format'] ?? function ($v) {
                return $v;
            };

            if (is_object($value)) {
                if (is_a($value, BackedEnum::class)) {
                    $value = $value->value;
                } elseif (in_array($prop, $this->modified, true) || ($value instanceof self && $value->isModified())) {
                    $supp = false;

                    if ($value instanceof JsonSerializable) {
                        $value = $value->jsonSerialize();
                    }
                }
            }

            if (is_array($value)) {
                $keepIt = false;

                foreach ($value as &$val) {
                    if (gettype($val) === 'object') {
                        if ($val instanceof self) {
                            $keepIt |= $val->isModified();
                            $val = $val->jsonSerialize();
                        }
                    }

                    $val = $format($val);
                }

                $supp &= !$keepIt;
            } else {
                $value = $format($value);
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
    public function populate(): static
    {
        if ($this->populated || !$this->getId() || !$this->getEndpoint()) {
            return $this;
        }

        $request = new Request();
        $response = $request->get($this);
        /** @var array<string, array<string, string>|string> $body */
        $body = json_decode($response, true);

        $this->cleanModified = true;
        $this->populated = true;

        if ($body) {
            $this->apiData = $body;

            $this->hydrate($body);

            $struct = $this->toArray();

            foreach ($struct as $prop => $value) {
                if (is_object($value) && $value instanceof self) {
                    $tmp = Stancer\Helper::camelCaseToSnakeCase($prop);

                    if (array_key_exists($tmp, $body)) {
                        $value->apiData = $body[$tmp];
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve an API object.
     *
     * Added to simply transition from Stripe.
     *
     * @param string $id Identifier of the object.
     * @return static
     */
    static public function retrieve(string $id): static
    {
        return new static($id);
    }

    /**
     * Send the current object.
     *
     * @uses Stancer\Core\Request::post()
     * @return $this
     * @throws Stancer\Exceptions\BadMethodCallException When the method is called on an empty object.
     * @throws Stancer\Exceptions\InvalidArgumentException When all requirement are not provided.
     */
    public function send(): static
    {
        if ($this->isNotModified()) {
            throw new Stancer\Exceptions\BadMethodCallException('The object you tried to send is empty.');
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

                throw new Stancer\Exceptions\InvalidArgumentException($message);
            }

            $response = $request->post($this);
        }

        $this->populated = true;

        /** @phpstan-var array<string, mixed> $body */
        $body = json_decode($response, true);

        if ($body) {
            $this->cleanModified = true;

            $this->hydrate($body);
        }

        $this->modified = [];

        $message = sprintf('%s "%s" %s', $this->getEntityName(), $this->id, $action);
        Stancer\Config::getGlobal()->getLogger()->info($message);

        return $this;
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
            if (
                array_key_exists('exportable', $infos)
                && array_key_exists('value', $infos)
                && $infos['exportable']
                && $infos['value'] !== null
            ) {
                $prop = Stancer\Helper::camelCaseToSnakeCase($property);

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
        return json_encode($this) ?: '';
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
     * Validate a value in a defined model.
     *
     * We do not handle array here, this method only check one value.
     * Array are checked in `self::dataModelSetter()`.
     *
     * @param string $property Property reference.
     * @param mixed $value Value to validate.
     * @return mixed Validated value.
     * @throws Stancer\Exceptions\InvalidArgumentException When the value do not match expected pattern.
     */
    protected function validateDataModel(string $property, mixed $value): mixed
    {
        $model = $this->getModel($property);

        if (!$model) {
            throw new Stancer\Exceptions\InvalidArgumentException(sprintf('Unknown property "%s"', $property));
        }

        /** @var array<class-string<Stancer\Exceptions\Exception>> $exceptionList */
        $exceptionList = [
            'Stancer\\Exceptions\\Invalid' . ucfirst($property) . 'Exception',
            $model['exception'],
        ];
        $exceptionClass = Stancer\Exceptions\InvalidArgumentException::class;

        foreach ($exceptionList as $except) {
            if ($except && class_exists($except)) {
                $exceptionClass = $except;
            }
        }

        if (is_a($model['type'], BackedEnum::class, true) && gettype($value) === 'string') {
            $value = $model['type']::from($value);
        }

        $type = gettype($value);
        $length = $value;

        $mismatchType = $type !== $model['type'];

        if (is_object($value)) {
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

        if (is_string($value)) {
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
            $readable = str_replace(['_id', '_'], [' ID', ' '], Stancer\Helper::camelCaseToSnakeCase($property));

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

        return $value;
    }
}
