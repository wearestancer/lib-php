<?php
declare(strict_types=1);

namespace Stancer\Core\Request;

use Stancer;
use Psr;

/**
 * Register API call.
 *
 * @method ?\DateTimeImmutable getCreated() Get creation date.
 * @method ?\Stancer\Exceptions\Exception getException() Get the exception catched during processing.
 * @method ?\Psr\Http\Message\RequestInterface getRequest() Get called request.
 * @method ?\Psr\Http\Message\ResponseInterface getResponse() Get the response received.
 * @method ?\DateTimeImmutable get_created() Get creation date.
 * @method ?\DateTimeImmutable get_creation_date() Get creation date.
 * @method string get_endpoint() Get API endpoint.
 * @method string get_entity_name() Get entity name.
 * @method ?\Stancer\Exceptions\Exception get_exception() Get the exception catched during processing.
 * @method ?string get_id() Get object ID.
 * @method ?\Psr\Http\Message\RequestInterface get_request() Get called request.
 * @method ?\Psr\Http\Message\ResponseInterface get_response() Get the response received.
 * @method string get_uri() Get entity resource location.
 *
 * @property-read ?\DateTimeImmutable $created Creation date.
 * @property-read ?\DateTimeImmutable $creationDate Creation date.
 * @property-read ?\DateTimeImmutable $creation_date Creation date.
 * @property-read string $endpoint API endpoint.
 * @property-read string $entityName Entity name.
 * @property-read string $entity_name Entity name.
 * @property-read ?\Stancer\Exceptions\Exception $exception The exception catched during processing.
 * @property-read ?string $id Object ID.
 * @property-read ?\Psr\Http\Message\RequestInterface $request Called request.
 * @property-read ?\Psr\Http\Message\ResponseInterface $response The response received.
 * @property-read string $uri Entity resource location.
 */
class Call extends Stancer\Core\AbstractObject
{
    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected array $dataModel = [
        'exception' => [
            'desc' => 'The exception catched during processing',
            'restricted' => true,
            'type' => Stancer\Exceptions\Exception::class,
        ],
        'request' => [
            'desc' => 'Called request',
            'restricted' => true,
            'type' => Psr\Http\Message\RequestInterface::class,
        ],
        'response' => [
            'desc' => 'The response received',
            'restricted' => true,
            'type' => Psr\Http\Message\ResponseInterface::class,
        ],
    ];
}
