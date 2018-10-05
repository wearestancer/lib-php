<?php
declare(strict_types=1);

namespace ild78\Api;

use GuzzleHttp;
use ild78\Exceptions;

/**
 * Handle request on API
 */
class Request
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';

    /**
     * Simple proxy for a GET request
     *
     * @see self::request
     * @param ild78\Api\Object $id Object id
     * @param string|null $location
     * @return string
     */
    public function get(Object $object, string $location = null) : string
    {
        return $this->request(static::GET, $object, $location);
    }

    /**
     * Simple proxy for a POST request
     *
     * @see self::request
     * @param ild78\Api\Object $id Object id
     * @param string|null $location
     * @return string
     */
    public function post(Object $object, string $location = null) : string
    {
        return $this->request(static::POST, $object, $location);
    }

    /**
     * Simple proxy for a PUT request
     *
     * @see self::request
     * @param ild78\Api\Object $id Object id
     * @param string|null $location
     * @return string
     */
    public function put(Object $object, string $location = null) : string
    {
        return $this->request(static::PUT, $object, $location);
    }

    /**
     * Make a call to API
     *
     * @uses ild78\Api\Config
     * @param string $method HTTP verb for the call. Use one of class constant.
     * @param ild78\Api\Object $id Object id
     * @param string|null $location
     * @return string
     * @throws ild78\Exceptions\InvalidArgumentException when calling with unsupported method
     * @throws ild78\Exceptions\TooManyRedirectsException on too many redirection case (HTTP 310)
     * @throws ild78\Exceptions\NotAuthorizedException on credential problem (HTTP 401)
     * @throws ild78\Exceptions\NotFoundException if an `id` is provided but it seems unknonw (HTTP 404)
     * @throws ild78\Exceptions\ClientException on HTTP 4** errors
     * @throws ild78\Exceptions\ServerException on HTTP 5** errors
     * @throws ild78\Exceptions\Exception on every over exception send by GuzzleHttp
     */
    public function request(string $method, Object $object, string $location = null) : string
    {
        $allowedMethods = [
            static::GET,
            static::POST,
            static::PUT,
        ];

        if (!in_array(strtoupper($method), $allowedMethods, true)) {
            throw new Exceptions\InvalidArgumentException(sprintf('Method "%s" unsupported', $method));
        }


        $config = Config::getGlobal();
        $client = $config->getHttpClient();

        $options = [
            'headers' => [
                'Authorization' => 'Basic ' . $config->getKey(),
            ],
        ];

        $endpoint = $object->getEndpoint();

        if ($location) {
            $endpoint .= '/' . $location;
        }

        try {
            $response = $client->request(strtoupper($method), $endpoint);
        } catch (GuzzleHttp\Exception\ServerException $exception) { // HTTP 5**
            $message = 'Servor error, please leave a minute to repair it and try again';
            throw new Exceptions\ServerException($message, 0, $exception);
        } catch (GuzzleHttp\Exception\TooManyRedirectsException $exception) {
            throw new Exceptions\TooManyRedirectsException('Too many redirection', 0, $exception);
        } catch (GuzzleHttp\Exception\ClientException $exception) { // HTTP 4**
            $response = $exception->getResponse();
            $class = Exceptions\ClientException::class;
            $message = vsprintf('%d - %s', [
                $response->getStatusCode(),
                $response->getReasonPhrase(),
            ]);

            switch ($response->getStatusCode()) {
                case 400:
                    $class = Exceptions\BadRequestException::class;
                    break;

                case 401:
                    $body = json_decode((string) $response->getBody());
                    $class = Exceptions\NotAuthorizedException::class;
                    $message = $body->error->message;
                    break;

                case 404:
                    $class = Exceptions\NotFoundException::class;
                    $message = sprintf('Ressource "%s" unknonw for %s', $location, get_class($object));
                    break;
            }

            throw new $class($message, 0, $exception);
        } catch (Exception $exception) {
            throw new Exceptions\Exception('Unknown error, may be a network error', 0, $exception);
        }

        return (string) $response->getBody();
    }
}
