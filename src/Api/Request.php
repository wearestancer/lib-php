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
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';

    /**
     * Simple proxy for a GET request
     *
     * @see self::request For full documentation.
     * @param ild78\Api\Object $object Object.
     * @param string|null $location Optionnal ressource identifier.
     * @return string
     */
    public function get(Object $object, string $location = null) : string
    {
        return $this->request(static::GET, $object, $location);
    }

    /**
     * Simple proxy for a POST request
     *
     * @see self::request For full documentation.
     * @param ild78\Api\Object $object Object.
     * @param string|null $location Optionnal ressource identifier.
     * @return string
     */
    public function post(Object $object, string $location = null) : string
    {
        return $this->request(static::POST, $object, $location, ['body' => json_encode($object)]);
    }

    /**
     * Simple proxy for a PUT request
     *
     * @see self::request For full documentation.
     * @param ild78\Api\Object $object Object.
     * @param string|null $location Optionnal ressource identifier.
     * @return string
     */
    public function put(Object $object, string $location = null) : string
    {
        return $this->request(static::PUT, $object, $location);
    }

    // phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber
    // Prevent PHPCS warning due to `thrown new $class`.

    /**
     * Make a call to API
     *
     * @uses ild78\Api\Config
     * @param string $method HTTP verb for the call. Use one of class constant.
     * @param ild78\Api\Object $object Object.
     * @param string|null $location Optionnal ressource identifier.
     * @param array $options Guzzle options.
     * @return string
     * @throws ild78\Exceptions\InvalidArgumentException When calling with unsupported method.
     * @throws ild78\Exceptions\TooManyRedirectsException On too many redirection case (HTTP 310).
     * @throws ild78\Exceptions\NotAuthorizedException On credential problem (HTTP 401).
     * @throws ild78\Exceptions\NotFoundException If an `id` is provided but it seems unknonw (HTTP 404).
     * @throws ild78\Exceptions\ClientException On HTTP 4** errors.
     * @throws ild78\Exceptions\ServerException On HTTP 5** errors.
     * @throws ild78\Exceptions\Exception On every over exception send by GuzzleHttp.
     */
    public function request(string $method, Object $object, string $location = null, array $options = []) : string
    {
        $config = Config::getGlobal();
        $client = $config->getHttpClient();
        $logger = $config->getLogger();

        $allowedMethods = [
            static::GET,
            static::POST,
            static::PUT,
        ];

        if (!in_array(strtoupper($method), $allowedMethods, true)) {
            $logger->error(sprintf('Unknown HTTP verb "%s"', $method));

            throw new ild78\Exceptions\InvalidArgumentException(sprintf('Method "%s" unsupported', $method));
        }

        if (!array_key_exists('headers', $options)) {
            $options['headers'] = [];
        }

        $options['headers']['Authorization'] = 'Basic ' . $config->getKey();

        $endpoint = $object->getEndpoint();

        if ($location) {
            $endpoint .= '/' . $location;
        }

        try {
            $logger->info(sprintf('API call : %s %s', strtoupper($method), $config->getUri() . $endpoint));
            $response = $client->request(strtoupper($method), $endpoint, $options);

        // HTTP 5**.
        } catch (GuzzleHttp\Exception\ServerException $exception) {
            $logger->critical('HTTP 500 - Internal Server Error');

            $message = 'Servor error, please leave a minute to repair it and try again';
            throw new ild78\Exceptions\ServerException($message, 0, $exception);

        // Too many redirection.
        } catch (GuzzleHttp\Exception\TooManyRedirectsException $exception) {
            $logger->critical('HTTP 310 - Too many redirection');

            throw new ild78\Exceptions\TooManyRedirectsException('Too many redirection', 0, $exception);

        // HTTP 4**.
        } catch (GuzzleHttp\Exception\ClientException $exception) {
            $response = $exception->getResponse();
            $class = ild78\Exceptions\ClientException::class;

            $params = [
                $response->getStatusCode(),
                $response->getReasonPhrase(),
            ];
            $message = vsprintf('%d - %s', $params);

            switch ($response->getStatusCode()) {
                case 400:
                    $class = ild78\Exceptions\BadRequestException::class;

                    $logger->critical('HTTP 400 - Bad request');
                    break;

                case 401:
                    $body = json_decode((string) $response->getBody());
                    $class = ild78\Exceptions\NotAuthorizedException::class;
                    $message = $body->error->message;

                    $logger->notice(sprintf('HTTP 401 - Invalid credential : %s', $config->getKey()));
                    break;

                case 404:
                    $class = ild78\Exceptions\NotFoundException::class;
                    $message = sprintf('Ressource "%s" unknown for %s', $location, get_class($object));

                    $logger->error(sprintf('HTTP 404 - Not found : %s', $message));
                    break;

                case 405:
                    $logger->critical('HTTP ' . $message);
                    break;

                default:
                    $logger->error('HTTP ' . $message);
                    break;
            }

            throw new $class($message, 0, $exception);

        // Others exceptions ...
        } catch (Exception $exception) {
            $logger->error(sprintf('Unknown error : %s', $exception->getMessage()));

            throw new ild78\Exceptions\Exception('Unknown error, may be a network error', 0, $exception);
        }

        return (string) $response->getBody();
    }
    // phpcs:enable
}
