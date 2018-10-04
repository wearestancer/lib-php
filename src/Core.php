<?php
declare(strict_types=1);

namespace ild78;

use DateTime;
use GuzzleHttp;
use ild78\Exceptions;

/**
 * Manage common code between API object
 */
abstract class Core
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
            $api = Api::getInstance();
            $client = $api->getHttpClient();

            $options = [
                'headers' => [
                    'Authorization' => 'Basic ' . $api->getKey(),
                ],
            ];

            try {
                $response = $client->request('GET', $this->getEndpoint() . '/' . $id);
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
                        $message = sprintf('%s "%s" unknonw', get_class($this), $id);
                        break;
                }

                throw new $class($message, 0, $exception);
            } catch (Exception $exception) {
                throw new Exceptions\Exception('Unknown error, may be a network error', 0, $exception);
            }

            $body = json_decode((string) $response->getBody(), true);

            foreach ($body as $key => $value) {
                if ($key === 'created') {
                    $this->created = new DateTime('@' . $value);
                } elseif (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
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
}
