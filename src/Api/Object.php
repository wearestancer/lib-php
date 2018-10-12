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
 * @throws ild78\Exceptions\BadMethodCallException when calling unknonw method
 */
abstract class Object implements JsonSerializable
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

    /**
     * Create or get an API object
     *
     * @param string|null $id Object id.
     * @return self
     * @throws ild78\Exceptions\TooManyRedirectsException On too many redirection case (HTTP 310).
     * @throws ild78\Exceptions\NotAuthorizedException On credential problem (HTTP 401).
     * @throws ild78\Exceptions\NotFoundException If an `id` is provided but it seems unknonw (HTTP 404).
     * @throws ild78\Exceptions\ClientException On HTTP 4** errors.
     * @throws ild78\Exceptions\ServerException On HTTP 5** errors.
     * @throws ild78\Exceptions\Exception On every over exception send by GuzzleHttp.
     */
    public function __construct(string $id = null)
    {
        $defaults = [
            'size' => [
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
     * @param string $method Method called.
     * @param array $arguments Arguments used during the call.
     * @return mixed
     * @throws ild78\Exceptions\BadMethodCallException When an unhandled method is called.
     * @throws ild78\Exceptions\InvalidArgumentException When the value do not match expected pattern (in setters).
     */
    public function __call(string $method, array $arguments)
    {
        $class = ild78\Exceptions\BadMethodCallException::class;
        $message = sprintf('Method "%s" unknown', $method);
        $action = substr($method, 0, 3);
        $property = lcfirst(substr($method, 3));

        if (property_exists($this, $property)) {
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
        }

        throw new $class($message);
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

        return $this->dataModel[$property]['value'];
    }

    /**
     * Set a value in data model.
     *
     * This was initialy in `self::__call()` method, I removed it for simplicity.
     *
     * @param string $property Property to set.
     * @param mixed $value Value to set.
     * @return self
     * @throws ild78\Exceptions\InvalidArgumentException When asking an unknown property.
     * @throws ild78\Exceptions\InvalidArgumentException When the value do not match expected pattern.
     */
    public function dataModelSetter(string $property, $value) : self
    {
        if (!array_key_exists($property, $this->dataModel)) {
            throw new ild78\Exceptions\InvalidArgumentException(sprintf('Unknown property "%s"', $property));
        }

        $model = $this->dataModel[$property];
        $type = gettype($value);
        $length = $value;

        if ($model['restricted']) {
            $message = sprintf('You are not allowed to modify "%s".', $property);

            throw new ild78\Exceptions\InvalidArgumentException($message);
        }

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

        if (array_key_exists('fixed', $model['size']) && $model['size']['fixed'] !== $length) {
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

        $this->dataModel[$property]['value'] = $value;

        return $this;
    }

    /**
     * Return creation date
     *
     * @return DateTime|null
     */
    public function getCreationDate() : DateTime
    {
        return $this->created;
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
     * Return ressource location
     *
     * @return string
     */
    public function getUri() : string
    {
        return Config::getGlobal()->getUri() . $this->getEndpoint();
    }

    /**
     * Hydrate the current object.
     *
     * @param array $data Data for hydratation.
     * @return self
     */
    public function hydrate(array $data) : self
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

                if (!in_array($this->dataModel[$property]['type'], $types, true)) {
                    $id = null;

                    if (is_string($value)) {
                        $id = $value;
                    }

                    if (!$this->dataModel[$property]['value'] || $id) {
                        $class = $this->dataModel[$property]['type'];
                        $this->dataModel[$property]['value'] = new $class($id);
                    }

                    if (is_array($value)) {
                        $this->dataModel[$property]['value']->hydrate($value);
                    }
                } else {
                    $this->dataModel[$property]['value'] = $value;
                }
            }
        }

        return $this;
    }

    /**
     * Return a array representation of the current object for a convertion as JSON.
     *
     * @uses self::toArray()
     * @return string
     */
    public function jsonSerialize() : array
    {
        return $this->toArray();
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
        if ($this->id) {
            $request = new Request();
            $response = $request->get($this, $this->id);
            $body = json_decode($response, true);
            $this->hydrate($body);

            $this->updated = true;
        }

        return $this;
    }

    /**
     * Save the current object.
     *
     * @uses Request::post()
     * @return self
     */
    public function save() : self
    {
        $request = new Request();
        $response = $request->post($this);
        $body = json_decode($response, true);
        $this->hydrate($body);

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
                'value' => $this->id,
            ],
            'created' => [
                'value' => $this->created,
            ],
        ];
        $data = array_merge($data, $this->dataModel);

        foreach ($data as $property => $infos) {
            $value = $infos['value'];

            if ($value && $property !== 'endpoint') {
                $json[$property] = $value;

                if ($value instanceof DateTime) {
                    $json[$property] = (int) $value->format('U');
                }
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
}
