<?php
declare(strict_types=1);

// phpcs:disable Generic.NamingConventions.ConstructorName.OldStyle

namespace ild78\Api;

use GuzzleHttp;
use Exception;
use ild78;

/**
 * Handle request on API
 */
class Request
{
    /**
     * Simple proxy for a GET request
     *
     * @see self::request() For full documentation.
     * @param ild78\Api\Object $object Object.
     * @return string
     */
    public function get(Object $object) : string
    {
        return $this->request(ild78\Interfaces\HttpClientInterface::GET, $object);
    }

    /**
     * Simple proxy for a PATCH request
     *
     * @see self::request() For full documentation.
     * @param ild78\Api\Object $object Object.
     * @return string
     */
    public function patch(Object $object) : string
    {
        $options = ['body' => json_encode($object)];

        return $this->request(ild78\Interfaces\HttpClientInterface::PATCH, $object, $options);
    }

    /**
     * Simple proxy for a POST request
     *
     * @see self::request() For full documentation.
     * @param ild78\Api\Object $object Object.
     * @return string
     */
    public function post(Object $object) : string
    {
        $options = ['body' => json_encode($object)];

        return $this->request(ild78\Interfaces\HttpClientInterface::POST, $object, $options);
    }

    /**
     * Simple proxy for a PUT request
     *
     * @see self::request() For full documentation.
     * @param ild78\Api\Object $object Object.
     * @return string
     */
    public function put(Object $object) : string
    {
        return $this->request(ild78\Interfaces\HttpClientInterface::PUT, $object);
    }

    /**
     * Alias for patch method
     *
     * @see self::patch() The patch method.
     * @param ild78\Api\Object $object Object.
     * @return string
     */
    public function update(Object $object) : string
    {
        return $this->patch($object);
    }

    // phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber
    // Prevent PHPCS warning due to `thrown new $class`.

    /**
     * Make a call to API
     *
     * @uses ild78\Api\Config
     * @param string $method HTTP verb for the call. Use one of class constant.
     * @param ild78\Api\Object $object Object.
     * @param array $options Guzzle options.
     * @return string
     * @throws ild78\Exceptions\InvalidArgumentException When calling with unsupported method.
     * @throws ild78\Exceptions\TooManyRedirectsException On too many redirection case (HTTP 310).
     * @throws ild78\Exceptions\NotAuthorizedException On credential problem (HTTP 401).
     * @throws ild78\Exceptions\NotFoundException If an `id` is provided but it seems unknonw (HTTP 404).
     * @throws ild78\Exceptions\ClientException On HTTP 4** errors.
     * @throws ild78\Exceptions\ServerException On HTTP 5** errors.
     * @throws ild78\Exceptions\Exception On every over exception.
     */
    public function request(string $method, Object $object, array $options = []) : string
    {
        $config = Config::getGlobal();
        $client = $config->getHttpClient();
        $logger = $config->getLogger();

        $allowedMethods = [
            ild78\Interfaces\HttpClientInterface::GET,
            ild78\Interfaces\HttpClientInterface::PATCH,
            ild78\Interfaces\HttpClientInterface::POST,
            ild78\Interfaces\HttpClientInterface::PUT,
        ];

        if (!in_array(strtoupper($method), $allowedMethods, true)) {
            $logger->error(sprintf('Unknown HTTP verb "%s"', $method));

            throw new ild78\Exceptions\InvalidArgumentException(sprintf('Method "%s" unsupported', $method));
        }

        if (!array_key_exists('headers', $options)) {
            $options['headers'] = [];
        }

        $options['headers']['Authorization'] = $config->getBasicAuthHeader();
        $options['headers']['Content-Type'] = 'application/json';

        $options['timeout'] = $config->getTimeout();

        $logMethod = null;
        $logMessage = null;
        $excepClass = null;
        $excepParams = [];

        try {
            $logger->debug(sprintf('API call : %s %s', strtoupper($method), $object->getUri()));
            $response = $client->request(strtoupper($method), $object->getUri(), $options);

        // Bypass for internal exceptions.
        } catch (ild78\Exceptions\Exception $exception) {
            throw $exception;

        // HTTP 5**.
        } catch (GuzzleHttp\Exception\ServerException $exception) {
            $logMethod = 'critical';
            $logMessage = 'HTTP 500 - Internal Server Error';
            $excepClass = ild78\Exceptions\InternalServerErrorException::class;
            $excepParams['previous'] = $exception;

        // Too many redirection.
        } catch (GuzzleHttp\Exception\TooManyRedirectsException $exception) {
            $logMethod = 'critical';
            $excepClass = ild78\Exceptions\TooManyRedirectsException::class;
            $excepParams['previous'] = $exception;

        // HTTP 4**.
        } catch (GuzzleHttp\Exception\ClientException $exception) {
            $logMethod = 'error';

            $response = $exception->getResponse();

            $excepClass = ild78\Exceptions\ClientException::class;
            $excepParams['previous'] = $exception;
            $excepParams['status'] = $response->getStatusCode();

            $params = [
                $response->getStatusCode(),
                $response->getReasonPhrase(),
            ];
            $excepParams['message'] = vsprintf('HTTP %d - %s', $params);

            switch ($response->getStatusCode()) {
                case 400:
                    $logMethod = 'critical';
                    break;

                case 401:
                    $body = json_decode((string) $response->getBody());
                    $excepParams['message'] = $body->error->message;

                    $logMethod = 'notice';
                    $logMessage = sprintf('HTTP 401 - Invalid credential : %s', $config->getKey());
                    break;

                case 404:
                    $tmp = get_class($object);
                    $parts = explode('\\', $tmp);
                    $resource = end($parts);

                    $excepParams['message'] = sprintf('Ressource "%s" unknown for %s', $object->getId(), $resource);

                    $logMethod = 'error';
                    $logMessage = sprintf('HTTP 404 - Not found : %s', $excepParams['message']);
                    break;

                case 405:
                    $logMethod = 'critical';
                    break;

                default:
                    $logMethod = 'error';
                    break;
            }

        // Others exceptions ...
        } catch (Exception $exception) {
            $logMethod = 'error';
            $logMessage = sprintf('Unknown error : %s', $exception->getMessage());

            $excepClass = ild78\Exceptions\Exception::class;
            $excepParams['previous'] = $exception;
            $excepParams['message'] = 'Unknown error, may be a network error';
        }

        if ($logMethod) {
            if (!$logMessage) {
                $logMessage = $excepParams['message'] ?? $excepClass::getDefaultMessage();
            }

            $logger->$logMethod($logMessage);
        }

        if ($excepClass) {
            throw $excepClass::create($excepParams);
        }

        return (string) $response->getBody();
    }
    // phpcs:enable
}
