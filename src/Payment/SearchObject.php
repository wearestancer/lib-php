<?php
declare(strict_types=1);

namespace Stancer\Payment;

use Stancer\Core\AbstractObject;
use Stancer\Traits\SearchTrait;

/**
 * An Object with a custom url, to search Data in a ressource.
 * E.g: A list of payments in a payment intent.
 *
 * @method ?\DateTimeImmutable getCreated() Get creation date.
 * @method ?\DateTimeImmutable get_created() Get creation date.
 * @method ?\DateTimeImmutable get_created_at() Get creation date.
 * @method ?\DateTimeImmutable get_creation_date() Get creation date.
 * @method string get_endpoint()
 * @method string get_entity_name() Get entity name.
 * @method ?string get_id() Get object ID.
 * @method string get_uri()
 *
 * @property-read ?\DateTimeImmutable $created Creation date.
 * @property-read ?\DateTimeImmutable $createdAt Creation date.
 * @property-read ?\DateTimeImmutable $created_at Creation date.
 * @property-read ?\DateTimeImmutable $creationDate Creation date.
 * @property-read ?\DateTimeImmutable $creation_date Creation date.
 * @property-read string $entityName Entity name.
 * @property-read string $entity_name Entity name.
 * @property-read ?string $id Object ID.
 */
class SearchObject extends AbstractObject
{
    use SearchTrait;

    /**
     * Contruct the object.
     *
     * @param string $id The id of the Outer Object.
     * @param string $innerPath The inner path.
     */
    public function __construct(string $id, protected string $innerPath)
    {
        $this->innerPath = $innerPath;
        parent::__construct($id);
    }

    /**
     * Get the uri of our search route.
     *
     * @return string
     */
    public function getUri(): string
    {
        return parent::getUri() . '/' . $this->innerPath;
    }

    /**
     * Get the endpoint of the "outer" object.
     * TODO: Don't hardcode it.
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return 'payment_intents';
    }
}
