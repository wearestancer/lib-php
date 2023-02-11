<?php
declare(strict_types=1);

namespace Stancer;

use DateTime;
use DateTimeImmutable;
use Stancer;

/**
 * Representation of a payout.
 *
 * @method integer getAmount()
 * @method string getCurrency()
 * @method DateTimeImmutable getCreated()
 * @method DateTimeImmutable getCreationDate()
 * @method DateTimeImmutable getDateBank()
 * @method DateTimeImmutable getDatePaym()
 * @method DateTimeImmutable getDatePayment()
 * @method integer getFees()
 * @method string getStatus()
 * @method string getStatementDescription()
 *
 * @method static Generator<static> list(SearchFilters $terms)
 *
 * @property-read integer $amount
 * @property-read DateTimeImmutable $creationDate
 * @property-read DateTimeImmutable $created
 * @property-read string $currency
 * @property-read DateTimeImmutable $dateBank
 * @property-read DateTimeImmutable $datePaym
 * @property-read DateTimeImmutable $datePayment
 * @property-read integer $fees
 * @property-read string $status
 * @property-read string $statementDescription
 */
class Payout extends Stancer\Core\AbstractObject
{
    use Stancer\Traits\SearchTrait;

    /** @var string */
    protected $endpoint = 'payouts';

    /** @var array<string, string> */
    protected $aliases = [
        'datePayment' => 'getDatePaym',
        'getDatePayment' => 'getDatePaym',
    ];

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
        'dateBank' => [
            'format' => Stancer\Core\Type\Helper::DATE_ONLY,
            'restricted' => true,
            'type' => DateTimeImmutable::class,
        ],
        'datePaym' => [
            'format' => Stancer\Core\Type\Helper::DATE_ONLY,
            'restricted' => true,
            'type' => DateTimeImmutable::class,
        ],
        'fees' => [
            'restricted' => true,
            'type' => self::INTEGER,
        ],
        'status' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
        'statementDescription' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
    ];
}
