<?php
declare(strict_types=1);

// phpcs:disable Generic.NamingConventions.ConstructorName.OldStyle

namespace ild78\Core;

use GuzzleHttp;
use Exception;
use ild78;

/**
 * Handle request on API
 */
class Request
{
    /**
     * Simple proxy for a DELETE request
     *
     * @see self::request() For full documentation.
     * @param ild78\Core\AbstractObject $object Object.
     * @return string
     */
    public function delete(AbstractObject $object): string
    {
        $verb = new ild78\Http\Verb\Delete();

        return $this->request($verb, $object);
    }

    /**
     * Simple proxy for a GET request
     *
     * @see self::request() For full documentation.
     * @param ild78\Core\AbstractObject $object Object.
     * @param array $params Query parameters.
     * @return string
     */
    public function get(AbstractObject $object, array $params = []): string
    {
        $options = [];

        if ($params) {
            $options['query'] = $params;
        }

        $verb = new ild78\Http\Verb\Get();

        return $this->request($verb, $object, $options);
    }

    /**
     * Simple proxy for a PATCH request
     *
     * @see self::request() For full documentation.
     * @param ild78\Core\AbstractObject $object Object.
     * @return string
     */
    public function patch(AbstractObject $object): string
    {
        $options = ['body' => json_encode($object)];
        $verb = new ild78\Http\Verb\Patch();

        return $this->request($verb, $object, $options);
    }

    /**
     * Simple proxy for a POST request
     *
     * @see self::request() For full documentation.
     * @param ild78\Core\AbstractObject $object Object.
     * @return string
     */
    public function post(AbstractObject $object): string
    {
        $options = ['body' => json_encode($object)];
        $verb = new ild78\Http\Verb\Post();

        return $this->request($verb, $object, $options);
    }

    /**
     * Simple proxy for a PUT request
     *
     * @see self::request() For full documentation.
     * @param ild78\Core\AbstractObject $object Object.
     * @return string
     */
    public function put(AbstractObject $object): string
    {
        $options = ['body' => json_encode($object)];
        $verb = new ild78\Http\Verb\Put();

        return $this->request($verb, $object, $options);
    }

    /**
     * Alias for patch method
     *
     * @see self::patch() The patch method.
     * @param ild78\Core\AbstractObject $object Object.
     * @return string
     */
    public function update(AbstractObject $object): string
    {
        return $this->patch($object);
    }

    // phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber
    // Prevent PHPCS warning due to `thrown new $class`.

    /**
     * Make a call to API
     *
     * @uses ild78\Config
     * @param ild78\Http\Verb\AbstractVerb $verb HTTP verb for the call.
     * @param ild78\Core\AbstractObject $object Object.
     * @param array $options Guzzle options.
     * @return string
     * @throws ild78\Exceptions\InvalidArgumentException When calling with unsupported verb.
     * @throws ild78\Exceptions\TooManyRedirectsException On too many redirection case (HTTP 310).
     * @throws ild78\Exceptions\NotAuthorizedException On credential problem (HTTP 401).
     * @throws ild78\Exceptions\NotFoundException If an `id` is provided but it seems unknown (HTTP 404).
     * @throws ild78\Exceptions\ClientException On HTTP 4** errors.
     * @throws ild78\Exceptions\ServerException On HTTP 5** errors.
     * @throws ild78\Exceptions\Exception On every over exception.
     */
    public function request(ild78\Http\Verb\AbstractVerb $verb, AbstractObject $object, array $options = []): string
    {
        $config = ild78\Config::getGlobal();
        $client = $config->getHttpClient();
        $logger = $config->getLogger();

        if ($verb->isNotAllowed()) {
            $message = sprintf('HTTP verb "%s" unsupported', (string) $verb);
            $logger->error($message);

            throw new ild78\Exceptions\InvalidArgumentException($message);
        }

        if (!array_key_exists('headers', $options)) {
            $options['headers'] = [];
        }

        $options['headers']['Authorization'] = $config->getBasicAuthHeader();
        $options['headers']['Content-Type'] = 'application/json';
        $options['headers']['User-Agent'] = $config->getDefaultUserAgent();

        $options['timeout'] = $config->getTimeout();

        $logMethod = null;
        $logMessage = null;
        $excepClass = null;
        $excepParams = [];
        $response = null;

        try {
            $location = $object->getUri();

            if (array_key_exists('query', $options)) {
                $location .= '?' . http_build_query($options['query']);
            }

            $logger->debug(sprintf('API call : %s %s', (string) $verb, $location));
            $response = $client->request((string) $verb, $location, array_diff_key($options, ['query' => 1]));

        // Bypass for internal exceptions.
        } catch (ild78\Exceptions\Exception $exception) {
            $params = [
                'exception' => $exception,
                'request' => $client->getLastRequest(),
                'response' => $client->getLastResponse(),
            ];

            $call = new ild78\Core\Request\Call($params);
            $config->addCall($call);

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
                    $logMethod = 'notice';
                    $logMessage = sprintf('HTTP 401 - Invalid credential : %s', $config->getSecretKey());
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

            $body = json_decode((string) $response->getBody(), true);

            if (
                is_array($body)
                && array_key_exists('error', $body)
                && is_array($body['error'])
                && array_key_exists('message', $body['error'])
            ) {
                $excepParams['message'] = $body['error']['message'];

                if (is_array($body['error']['message'])) {
                    $excepParams['message'] = current($body['error']['message']);
                    $id = '';

                    if (array_key_exists('id', $body['error']['message'])) {
                        $id = $body['error']['message']['id'];
                        $excepParams['message'] = $body['error']['message']['id'];
                    }

                    if (array_key_exists('error', $body['error']['message'])) {
                        $excepParams['message'] = $body['error']['message']['error'];

                        if ($id) {
                            $excepParams['message'] .= ' (' . $id . ')';
                        }
                    }
                }
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

        $exception = null;

        if ($excepClass) {
            $exception = $excepClass::create($excepParams);
        }

        $params = [];

        if ($config->getDebug()) {
            if ($client instanceof ild78\Http\Client) {
                $params = [
                    'exception' => $exception,
                    'request' => $client->getLastRequest(),
                    'response' => $client->getLastResponse(),
                ];
            } else {
                $body = array_key_exists('body', $options) ? $options['body'] : null;
                $request = new GuzzleHttp\Psr7\Request((string) $verb, $location, $options['headers'], $body);

                if (!$response && $exception instanceof ild78\Exceptions\HttpException) {
                    $response = $exception->getResponse();
                }

                $params = [
                    'exception' => $exception,
                    'request' => $request,
                    'response' => $response,
                ];
            }

            $call = new ild78\Core\Request\Call($params);
            $config->addCall($call);
        }

        if ($exception) {
            throw $exception;
        }

        return (string) $response->getBody();
    }
    // phpcs:enable
}
