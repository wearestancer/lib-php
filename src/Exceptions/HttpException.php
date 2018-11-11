<?php
declare(strict_types=1);

namespace ild78\Exceptions;

use GuzzleHttp\Exception\RequestException;
use ild78\Interfaces\ExceptionInterface;
use Throwable;
use Psr;

/**
 * Base exception class for all ild78 HTTP based exceptions.
 */
class HttpException extends Exception implements ExceptionInterface
{
    /** @var Psr\Http\Message\RequestInterface|null */
    protected $request;

    /** @var Psr\Http\Message\ResponseInterface|null */
    protected $response;

    /**
     * Construct the exception
     *
     * @param string $message The Exception message to throw.
     * @param integer $code The Exception code.
     * @param Throwable $previous The previous exception used for the exception chaining.
     */
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if ($previous instanceof RequestException) {
            $this->request = $previous->getRequest();
            $this->response = $previous->getResponse();
        }
    }

    /**
     * Create an instance from an array
     *
     * @param array $params Parameters, keys must correspond to exception properties.
     * @return self
     */
    public static function create(array $params = []) : Exception
    {
        $obj = parent::create($params);

        $keys = [
            'request',
            'response',
        ];

        foreach ($keys as $key) {
            if (array_key_exists($key, $params) && $params[$key]) {
                $obj->$key = $params[$key];
            }
        }

        return $obj;
    }

    /**
     * Return default message for that kind of exception
     *
     * @return string
     */
    public static function getDefaultMessage() : string
    {
        return 'HTTP error';
    }

    /**
     * Get the request that caused the exception
     *
     * @return Psr\Http\Message\RequestInterface|null
     */
    public function getRequest() : ?Psr\Http\Message\RequestInterface
    {
        return $this->request;
    }

    /**
     * Get the associated response
     *
     * @return Psr\Http\Message\ResponseInterface|null
     */
    public function getResponse() : ?Psr\Http\Message\ResponseInterface
    {
        return $this->response;
    }
}
