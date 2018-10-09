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
    /** @var string */
    protected $endpoint = '';

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
        if ($id) {
            $request = new Request();
            $response = $request->get($this, $id);
            $body = json_decode($response, true);
            $this->hydrate($body);
        }
    }

    /**
     * Handle getter and setter for every properties.
     *
     * @param string $method Method called.
     * @param array $arguments Arguments used during the call.
     * @return mixed
     * @throws ild78\Exceptions\BadMethodCallException When an unhandled method is called.
     */
    public function __call(string $method, array $arguments)
    {
        $message = sprintf('Method "%s" unknown', $method);
        $action = substr($method, 0, 3);
        $property = lcfirst(substr($method, 3));

        if ($action === 'get' && property_exists($this, $property)) {
            return $this->$property;
        }

        if ($action === 'set' && property_exists($this, $property)) {
            if (!in_array($property, $this->getForbiddenProperties(), true)) {
                $this->$property = $arguments[0];

                return $this;
            }

            $tmp = $property;

            if ($property === 'created') {
                $tmp = 'creation date';
            }

            $message = sprintf('You are not allowed to modify the %s.', $tmp);
        }

        throw new ild78\Exceptions\BadMethodCallException($message);
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
     * Return an array of properties not allowed to change with a setter
     *
     * @see self::__call()
     * @return array
     */
    public function getForbiddenProperties() : array
    {
        return [
            'created',
            'endpoint',
            'id',
        ];
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
            $class = 'ild78\\' . ucfirst($property);

            if (strpos($key, '_') !== false) {
                $replace = function ($matches) {
                    return trim(strtoupper($matches[0]), '_');
                };

                $property = preg_replace_callback('`_\w`', $replace, $key);
            }

            if ($property === 'created') {
                $this->created = new DateTime('@' . $value);
            } elseif (property_exists($this, $property)) {
                if (class_exists($class)) {
                    if (!$this->$property) {
                        $this->$property = new $class();
                    }

                    $this->$property->hydrate($value);
                } else {
                    $this->$property = $value;
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

        foreach (get_object_vars($this) as $property => $value) {
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
