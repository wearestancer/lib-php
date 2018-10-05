<?php
declare(strict_types=1);

namespace ild78\Api;

use DateTime;
use GuzzleHttp;
use ild78\Exceptions;

/**
 * Manage common code between API object
 *
 * @throws ild78\Exceptions\BadMethodCallException when calling unknonw method
 */
abstract class Object
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
        $action = substr($method, 0, 3);
        $property = lcfirst(substr($method, 3));

        if ($action === 'get' && property_exists($this, $property)) {
            return $this->$property;
        }

        throw new Exceptions\BadMethodCallException(sprintf('Method "%s" unknonw', $method));
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
     * Return creation date
     *
     * @return DateTime|null
     */
    public function getCreationDate()
    {
        return $this->created;
    }

    public function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            $property = $key;

            if (strpos($key, '_') !== false) {
                $property = preg_replace_callback('`_\w`', function ($matches) {
                    return trim(strtoupper($matches[0]), '_');
                }, $key);
            }

            if ($property === 'created') {
                $this->created = new DateTime('@' . $value);
            } elseif (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

        return $this;
    }
}
