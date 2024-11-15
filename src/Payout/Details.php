<?php
declare(strict_types=1);

namespace Stancer\Payout;

use Override;
use ReturnTypeWillChange;
use Stancer;

/**
 * Payout details.
 *
 * @method \Generator<Stancer\Dispute> disputes(array<mixed> $terms) List every disputes in the payout.
 * @method ?\DateTimeImmutable getCreated() Get creation date.
 * @method ?\Stancer\Payout\Details\Inner getDisputes() Get disputes details.
 * @method ?\Stancer\Payout\Details\Inner getPayments() Get payments details.
 * @method ?\Stancer\Payout\Details\Inner getRefunds() Get refunds details.
 * @method ?\DateTimeImmutable get_created() Get creation date.
 * @method ?\DateTimeImmutable get_creation_date() Get creation date.
 * @method ?\Stancer\Payout\Details\Inner get_disputes() Get disputes details.
 * @method string get_endpoint() Get API endpoint.
 * @method string get_entity_name() Get entity name.
 * @method ?string get_id() Get object ID.
 * @method ?\Stancer\Payout\Details\Inner get_payments() Get payments details.
 * @method ?\Stancer\Payout\Details\Inner get_refunds() Get refunds details.
 * @method string get_uri() Get entity resource location.
 * @method \Generator<Stancer\Dispute> listDisputes(array<mixed> $terms) List every disputes in the payout.
 * @method \Generator<Stancer\Payment> listPayments(array<mixed> $terms) List every payments in the payout.
 * @method \Generator<Stancer\Refund> listRefunds(array<mixed> $terms) List every refunds in the payout.
 * @method \Generator<Stancer\Payment> payments(array<mixed> $terms) List every payments in the payout.
 * @method \Generator<Stancer\Refund> refunds(array<mixed> $terms) List every refunds in the payout.
 *
 * @phpstan-method \Generator<Stancer\Dispute> listDisputes(SearchFilters $terms) List every disputes in the payout.
 * @phpstan-method \Generator<Stancer\Payment> listPayments(SearchFilters $terms) List every payments in the payout.
 * @phpstan-method \Generator<Stancer\Refund> listRefunds(SearchFilters $terms) List every refunds in the payout.
 * @property-read ?\DateTimeImmutable $created Creation date.
 * @property-read ?\DateTimeImmutable $creationDate Creation date.
 * @property-read ?\DateTimeImmutable $creation_date Creation date.
 * @property-read ?\Stancer\Payout\Details\Inner $disputes Disputes details.
 * @property-read string $endpoint API endpoint.
 * @property-read string $entityName Entity name.
 * @property-read string $entity_name Entity name.
 * @property-read ?string $id Object ID.
 * @property-read ?\Stancer\Payout\Details\Inner $payments Payments details.
 * @property-read ?\Stancer\Payout\Details\Inner $refunds Refunds details.
 * @property-read string $uri Entity resource location.
 */
#[Stancer\Core\Documentation\AddMethod(
    'disputes',
    ['array $terms'],
    '\Generator<Stancer\Dispute>',
    description: 'List every disputes in the payout.',
)]
#[Stancer\Core\Documentation\AddMethod(
    'listDisputes',
    ['array $terms'],
    '\Generator<Stancer\Dispute>',
    description: 'List every disputes in the payout.',
)]
#[Stancer\Core\Documentation\AddMethod(
    'listDisputes',
    ['SearchFilters $terms'],
    '\Generator<Stancer\Dispute>',
    description: 'List every disputes in the payout.',
    stan: true,
)]
#[Stancer\Core\Documentation\AddMethod(
    'payments',
    ['array $terms'],
    '\Generator<Stancer\Payment>',
    description: 'List every payments in the payout.',
)]
#[Stancer\Core\Documentation\AddMethod(
    'listPayments',
    ['array $terms'],
    '\Generator<Stancer\Payment>',
    description: 'List every payments in the payout.',
)]
#[Stancer\Core\Documentation\AddMethod(
    'listPayments',
    ['SearchFilters $terms'],
    '\Generator<Stancer\Payment>',
    description: 'List every payments in the payout.',
    stan: true,
)]
#[Stancer\Core\Documentation\AddMethod(
    'refunds',
    ['array $terms'],
    '\Generator<Stancer\Refund>',
    description: 'List every refunds in the payout.',
)]
#[Stancer\Core\Documentation\AddMethod(
    'listRefunds',
    ['array $terms'],
    '\Generator<Stancer\Refund>',
    description: 'List every refunds in the payout.',
)]
#[Stancer\Core\Documentation\AddMethod(
    'listRefunds',
    ['SearchFilters $terms'],
    '\Generator<Stancer\Refund>',
    description: 'List every refunds in the payout.',
    stan: true,
)]
class Details extends Stancer\Core\AbstractObject
{
    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected array $dataModel = [
        'disputes' => [
            'desc' => 'Disputes details',
            'restricted' => true,
            'type' => Stancer\Payout\Details\Inner::class,
        ],
        'payments' => [
            'desc' => 'Payments details',
            'restricted' => true,
            'type' => Stancer\Payout\Details\Inner::class,
        ],
        'refunds' => [
            'desc' => 'Refunds details',
            'restricted' => true,
            'type' => Stancer\Payout\Details\Inner::class,
        ],
    ];

    /**
     * Handle getter and setter for every properties.
     *
     * Overrided to handle empty parameters list and `list` methods.
     *
     * @param string $method Method called.
     * @param mixed[] $arguments Arguments used during the call.
     * @return mixed
     * @throws Stancer\Exceptions\BadMethodCallException When an unhandled method is called.
     */
    #[Override]
    public function __call(string $method, array $arguments): mixed
    {
        $name = Stancer\Helper::snakeCaseToCamelCase($method);
        $action = substr($name, 0, 4);

        if ($action === 'list') {
            $name = lcfirst(substr($name, 4));
        }

        if (array_key_exists($name, $this->dataModel)) {
            if (!$arguments) {
                $arguments = [
                    [
                        'limit' => 100,
                        'start' => 0,
                    ],
                ];
            }

            // @phpstan-ignore-next-line `dataModelGetter` will return an instance of `Stancer\Payout\Details\Inner`
            return call_user_func_array($this->dataModelGetter($name), $arguments);
        }

        return parent::__call($method, $arguments);
    }
}
