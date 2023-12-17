<?php
declare(strict_types=1);

namespace Stancer;

use Stancer;

/**
 * Representation of a dispute.
 *
 * @method integer getAmount() Get the disputed amount.
 * @method ?\DateTimeImmutable getCreated() Get creation date.
 * @method string getCurrency() Get the currency of the disputed amount.
 * @method ?string getOrderId() Get the order_id you specified in your inital payment request.
 * @method \Stancer\Payment getPayment() Get the related payment's identifier.
 * @method string getResponse() Get the response code.
 * @method integer get_amount() Get the disputed amount.
 * @method ?\DateTimeImmutable get_created() Get creation date.
 * @method ?\DateTimeImmutable get_creation_date() Get creation date.
 * @method string get_currency() Get the currency of the disputed amount.
 * @method string get_endpoint() Get API endpoint.
 * @method string get_entity_name() Get entity name.
 * @method ?string get_id() Get object ID.
 * @method ?string get_order_id() Get the order_id you specified in your inital payment request.
 * @method \Stancer\Payment get_payment() Get the related payment's identifier.
 * @method string get_response() Get the response code.
 * @method string get_uri() Get entity resource location.
 *
 * @property-read integer $amount The disputed amount.
 * @property-read ?\DateTimeImmutable $created Creation date.
 * @property-read ?\DateTimeImmutable $creationDate Creation date.
 * @property-read ?\DateTimeImmutable $creation_date Creation date.
 * @property-read string $currency The currency of the disputed amount.
 * @property-read string $endpoint API endpoint.
 * @property-read string $entityName Entity name.
 * @property-read string $entity_name Entity name.
 * @property-read ?string $id Object ID.
 * @property-read ?string $orderId The order_id you specified in your inital payment request.
 * @property-read ?string $order_id The order_id you specified in your inital payment request.
 * @property-read \Stancer\Payment $payment The related payment's identifier.
 * @property-read string $response The response code.
 * @property-read string $uri Entity resource location.
 */
class Dispute extends Stancer\Core\AbstractObject
{
    use Stancer\Traits\SearchTrait;

    #[Stancer\WillChange\PHP8_3\TypedClassConstants]
    final public const ENDPOINT = 'disputes';

    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected array $dataModel = [
        'amount' => [
            'desc' => 'The disputed amount',
            'nullable' => false,
            'restricted' => true,
            'type' => self::INTEGER,
        ],
        'currency' => [
            'desc' => 'The currency of the disputed amount',
            'coerce' => Stancer\Core\Type\Helper::TO_LOWER,
            'nullable' => false,
            'restricted' => true,
            'type' => self::STRING,
        ],
        'orderId' => [
            'desc' => 'The order_id you specified in your inital payment request',
            'restricted' => true,
            'size' => [
                'min' => 1,
                'max' => 36,
            ],
            'type' => self::STRING,
        ],
        'payment' => [
            'desc' => 'The related payment\'s identifier',
            'nullable' => false,
            'restricted' => true,
            'type' => Stancer\Payment::class,
        ],
        'response' => [
            'desc' => 'The response code',
            'nullable' => false,
            'restricted' => true,
            'type' => self::STRING,
        ],
    ];
}
