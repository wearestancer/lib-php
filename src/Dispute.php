<?php
declare(strict_types=1);

namespace Stancer;

use Stancer;

/**
 * Representation of a dispute.
 *
 * @method integer getAmount()
 * @method string getCurrency()
 * @method string getOrderId()
 * @method Stancer\Payment getPayment()
 * @method string getResponse()
 *
 * @method static Generator<static> list(SearchFilters $terms)
 *
 * @property integer $amount
 * @property DateTimeImmutable|null $created
 * @property string $currency
 * @property string $orderId
 * @property Stancer\Payment $payment
 * @property string $response
 */
class Dispute extends Stancer\Core\AbstractObject
{
    use Stancer\Traits\AmountTrait;
    use Stancer\Traits\SearchTrait;

    /** @var string */
    protected $endpoint = 'disputes';

    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected $dataModel = [
        'orderId' => [
            'restricted' => true,
            'size' => [
                'min' => 1,
                'max' => 36,
            ],
            'type' => self::STRING,
        ],
        'payment' => [
            'restricted' => true,
            'type' => Stancer\Payment::class,
        ],
        'response' => [
            'restricted' => true,
            'size' => [
                'fixed' => 2,
            ],
            'type' => self::STRING,
        ],
    ];
}
