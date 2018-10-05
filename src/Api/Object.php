<?php
declare(strict_types=1);

namespace ild78\Api;

use DateTime;
use GuzzleHttp;
use ild78\Exceptions;
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
     * @param string|null $id Object id
     * @return self
     * @throws ild78\Exceptions\TooManyRedirectsException on too many redirection case (HTTP 310)
     * @throws ild78\Exceptions\NotAuthorizedException on credential problem (HTTP 401)
     * @throws ild78\Exceptions\NotFoundException if an `id` is provided but it seems unknonw (HTTP 404)
     * @throws ild78\Exceptions\ClientException on HTTP 4** errors
     * @throws ild78\Exceptions\ServerException on HTTP 5** errors
     * @throws ild78\Exceptions\Exception on every over exception send by GuzzleHttp
     */
    public function __construct(string $id = null)
    {
        if ($id) {
            $request = new Request;
            $response = $request->get($this, $id);
            $body = json_decode($response, true);
            $this->hydrate($body);
        }
    }

    public function __call($method, $arguments)
    {
        $message = sprintf('Method "%s" unknown', $method);
        $action = substr($method, 0, 3);
        $property = lcfirst(substr($method, 3));

        if ($action === 'get' && property_exists($this, $property)) {
            return $this->$property;
        }

        $forbiddenChanges = [
            'created',
            'endpoint',
            'id',
        ];

        if ($action === 'set' && property_exists($this, $property)) {
            if (!in_array($property, $forbiddenChanges, true)) {
                $this->$property = $arguments[0];

                return $this;
            }

            $tmp = $property;

            if ($property === 'created') {
                $tmp = 'creation date';
            }

            $message = sprintf('You are not allowed to modify the %s.', $tmp);
        }

        throw new Exceptions\BadMethodCallException($message);
    }

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
     * Return object ID
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function hydrate(array $data) : self
    {
        foreach ($data as $key => $value) {
            $property = $key;
            $method = 'hydrate' . ucfirst($property);

            if (strpos($key, '_') !== false) {
                $property = preg_replace_callback('`_\w`', function ($matches) {
                    return trim(strtoupper($matches[0]), '_');
                }, $key);
            }

            if ($property === 'created') {
                $this->created = new DateTime('@' . $value);
            } elseif (method_exists($this, $method)) {
                $this->$method($value);
            } elseif (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

        return $this;
    }

    public function jsonSerialize() : array
    {
        return $this->toArray();
    }

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

    public function toJson() : string
    {
        return json_encode($this);
    }

    public function toString() : string
    {
        return $this->toJson();
    }
}
