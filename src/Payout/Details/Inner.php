<?php
declare(strict_types=1);

namespace Stancer\Payout\Details;

use Generator;
use Stancer;

/**
 * Payout details.
 *
 * @method integer getAmount()
 * @method ?\DateTimeImmutable getCreated() Get creation date.
 * @method string getCurrency()
 * @method \Stancer\Payout getParent()
 * @method 'disputes'|'payments'|'refunds' getType()
 * @method integer get_amount()
 * @method ?\DateTimeImmutable get_created() Get creation date.
 * @method ?\DateTimeImmutable get_creation_date() Get creation date.
 * @method string get_currency()
 * @method string get_endpoint() Get API endpoint.
 * @method string get_entity_name() Get entity name.
 * @method ?string get_id() Get object ID.
 * @method \Stancer\Payout get_parent()
 * @method 'disputes'|'payments'|'refunds' get_type()
 * @method string get_uri() Get current resource location.
 *
 * @property-read integer $amount
 * @property-read ?\DateTimeImmutable $created Creation date.
 * @property-read ?\DateTimeImmutable $creationDate Creation date.
 * @property-read ?\DateTimeImmutable $creation_date Creation date.
 * @property-read string $currency
 * @property-read string $endpoint API endpoint.
 * @property-read string $entityName Entity name.
 * @property-read string $entity_name Entity name.
 * @property-read ?string $id Object ID.
 * @property-read \Stancer\Payout $parent
 * @property-read 'disputes'|'payments'|'refunds' $type
 * @property-read string $uri Current resource location.
 */
class Inner extends Stancer\Core\AbstractObject
{
    use Stancer\Traits\SearchTrait {
        list as protected;
    }

    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected array $dataModel = [
        'amount' => [
            'nullable' => false,
            'restricted' => true,
            'type' => self::INTEGER,
        ],
        'currency' => [
            'nullable' => false,
            'restricted' => true,
            'type' => self::STRING,
        ],
        'parent' => [
            'nullable' => false,
            'exportable' => false,
            'restricted' => true,
            'type' => Stancer\Payout::class,
        ],
        'type' => [
            'allowedValues' => [
                'disputes',
                'payments',
                'refunds',
            ],
            'nullable' => false,
            'exportable' => false,
            'restricted' => true,
            'type' => self::STRING,
        ],
    ];

    /**
     * List elements.
     *
     * `$terms` must be an associative array with one of the following key : `created`, `limit` or `start`.
     *
     * `created` must be an unix timestamp or a DateTime object which will filter payments equal
     * to or greater than this value.
     *
     * `limit` must be an integer between 1 and 100 and will limit the number of objects to be returned.
     *
     * `start` must be an integer, will be used as a pagination cursor, starts at 0.
     *
     * @param array $terms Search terms. May have `created`, `limit` or `start` key.
     * @return Generator<Stancer\Core\AbstractObject>
     * @throws Stancer\Exceptions\InvalidSearchFilterException When `$terms` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchCreationFilterException When `created` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchCreationFilterException When `created` is a DatePeriod without end.
     * @throws Stancer\Exceptions\InvalidSearchCreationUntilFilterException When `created_until` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchLimitException When `limit` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchStartException When `start` is invalid.
     *
     * @phpstan-param SearchFilters $terms
     */
    public function __invoke(array $terms): Generator
    {
        $map = [
            'disputes' => Stancer\Dispute::class,
            'payments' => Stancer\Payment::class,
            'refunds' => Stancer\Refund::class,
        ];

        return $this->search($map[$this->type], $this->type, $terms);
    }

    /**
     * Return resource location.
     *
     * @return string
     */
    #[Stancer\Core\Documentation\FormatProperty(
        description: 'Current resource location',
        nullable: false,
        restricted: true,
        type: self::STRING,
    )]
    public function getUri(): string
    {
        return $this->parent->getUri() . '/' . $this->type;
    }
}
