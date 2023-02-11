<?php
declare(strict_types=1);

namespace Stancer\Payout\Details;

use Generator;
use Stancer;

/**
 * Payout details.
 *
 * @method integer getAmount()
 * @method string getCurrency()
 *
 * @property-read integer $amount
 * @property-read string $currency
 *
 * @phpstan-property-read Stancer\Payout $parent
 * @phpstan-property-read string $type
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
    protected $dataModel = [
        'amount' => [
            'restricted' => true,
            'type' => self::INTEGER,
        ],
        'currency' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
        'parent' => [
            'exportable' => false,
            'restricted' => true,
            'type' => Stancer\Payout::class,
        ],
        'type' => [
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
    public function getUri(): string
    {
        return $this->parent->getUri() . '/' . $this->type;
    }
}
