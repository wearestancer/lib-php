<?php
declare(strict_types=1);

namespace Stancer;

use DateTimeImmutable;
use Override;
use Stancer;

/**
 * Representation of a payout.
 *
 * @method integer getAmount() Get the total credit transfer amount you will receive.
 * @method ?\DateTimeImmutable getCreated() Get creation date.
 * @method string getCurrency() Get processed currency.
 * @method ?\DateTimeImmutable getDateBank() Get the date you will receive the credit transfer.
 * @method \DateTimeImmutable getDatePaym() Get the date the payment transactions were made.
 * @method \Stancer\Payout\Details getDetails() Get payout details.
 * @method integer getFees() Get the fees you paid for processing the payments.
 * @method ?string getStatementDescription() Get the statement description, will be used on the transfer.
 * @method \Stancer\Payout\Status getStatus() Get payout status.
 * @method integer get_amount() Get the total credit transfer amount you will receive.
 * @method ?\DateTimeImmutable get_created() Get creation date.
 * @method ?\DateTimeImmutable get_creation_date() Get creation date.
 * @method string get_currency() Get processed currency.
 * @method ?\DateTimeImmutable get_date_bank() Get the date you will receive the credit transfer.
 * @method \DateTimeImmutable get_date_paym() Get the date the payment transactions were made.
 * @method \DateTimeImmutable get_date_payment() Get the date the payment transactions were made.
 * @method \Stancer\Payout\Details get_details() Get payout details.
 * @method string get_endpoint() Get API endpoint.
 * @method string get_entity_name() Get entity name.
 * @method integer get_fees() Get the fees you paid for processing the payments.
 * @method ?string get_id() Get object ID.
 * @method ?string get_statement_description() Get the statement description, will be used on the transfer.
 * @method \Stancer\Payout\Status get_status() Get payout status.
 * @method string get_uri() Get entity resource location.
 *
 * @property-read integer $amount The total credit transfer amount you will receive.
 * @property-read ?\DateTimeImmutable $created Creation date.
 * @property-read ?\DateTimeImmutable $creationDate Creation date.
 * @property-read ?\DateTimeImmutable $creation_date Creation date.
 * @property-read string $currency Processed currency.
 * @property-read ?\DateTimeImmutable $dateBank The date you will receive the credit transfer.
 * @property-read \DateTimeImmutable $datePaym The date the payment transactions were made.
 * @property-read \DateTimeImmutable $datePayment The date the payment transactions were made.
 * @property-read ?\DateTimeImmutable $date_bank The date you will receive the credit transfer.
 * @property-read \DateTimeImmutable $date_paym The date the payment transactions were made.
 * @property-read \DateTimeImmutable $date_payment The date the payment transactions were made.
 * @property-read \Stancer\Payout\Details $details Payout details.
 * @property-read string $endpoint API endpoint.
 * @property-read string $entityName Entity name.
 * @property-read string $entity_name Entity name.
 * @property-read integer $fees The fees you paid for processing the payments.
 * @property-read ?string $id Object ID.
 * @property-read ?string $statementDescription The statement description, will be used on the transfer.
 * @property-read ?string $statement_description The statement description, will be used on the transfer.
 * @property-read \Stancer\Payout\Status $status Payout status.
 * @property-read string $uri Entity resource location.
 */
class Payout extends Stancer\Core\AbstractObject
{
    use Stancer\Traits\SearchTrait;

    #[Stancer\WillChange\PHP8_3\TypedClassConstants]
    final public const ENDPOINT = 'payouts';

    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected array $dataModel = [
        'amount' => [
            'desc' => 'The total credit transfer amount you will receive',
            'nullable' => false,
            'restricted' => true,
            'type' => self::INTEGER,
        ],
        'currency' => [
            'desc' => 'Processed currency',
            'nullable' => false,
            'restricted' => true,
            'type' => self::STRING,
        ],
        'dateBank' => [
            'desc' => 'The date you will receive the credit transfer',
            'format' => Stancer\Core\Type\Helper::DATE_ONLY,
            'restricted' => true,
            'type' => DateTimeImmutable::class,
        ],
        'datePaym' => [
            'desc' => 'The date the payment transactions were made',
            'format' => Stancer\Core\Type\Helper::DATE_ONLY,
            'nullable' => false,
            'restricted' => true,
            'type' => DateTimeImmutable::class,
        ],
        'datePayment' => [
            'desc' => 'The date the payment transactions were made',
            'nullable' => false,
            'restricted' => true,
            'type' => DateTimeImmutable::class,
        ],
        'details' => [
            'desc' => 'Payout details',
            'nullable' => false,
            'restricted' => true,
            'type' => Stancer\Payout\Details::class,
        ],
        'fees' => [
            'desc' => 'The fees you paid for processing the payments',
            'nullable' => false,
            'restricted' => true,
            'type' => self::INTEGER,
        ],
        'statementDescription' => [
            'desc' => 'The statement description, will be used on the transfer',
            'restricted' => true,
            'type' => self::STRING,
        ],
        'status' => [
            'desc' => 'Payout status',
            'nullable' => false,
            'restricted' => true,
            'type' => Stancer\Payout\Status::class,
        ],
    ];

    /**
     * Return the date the payment transactions were made.
     *
     * Alias for `datePaym`.
     *
     * @return DateTimeImmutable
     */
    public function getDatePayment(): DateTimeImmutable
    {
        return $this->datePaym;
    }

    /**
     * Hydrate the current object.
     *
     * Overrided to handle details.
     *
     * @param array<string, mixed> $data Data for hydration.
     * @return $this
     *
     * @phpstan-param PayoutResponse $data
     */
    #[Override]
    public function hydrate(array $data): static
    {
        $data['details'] = [];
        $items = [
            'disputes',
            'payments',
            'refunds',
        ];

        foreach ($items as $item) {
            if (array_key_exists($item, $data)) {
                $data['details'][$item] = $data[$item];
                $data['details'][$item]['currency'] = $data['currency'];
                $data['details'][$item]['parent'] = $this;
                $data['details'][$item]['type'] = $item;
            }
        }

        return parent::hydrate($data);
    }
}
