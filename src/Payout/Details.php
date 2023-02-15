<?php
declare(strict_types=1);

namespace Stancer\Payout;

use Stancer;

/**
 * Payout details.
 *
 * @method \Generator<Stancer\Dispute> disputes(array $terms) List every dispute in the payout.
 * @method Stancer\Payout\Details\Inner getDisputes() Get disputes details.
 * @method Stancer\Payout\Details\Inner getPayments() Get payments details.
 * @method Stancer\Payout\Details\Inner getRefunds() Get refunds details.
 * @method \Generator<Stancer\Dispute> listDisputes(array $terms) List every dispute in the payout.
 * @method \Generator<Stancer\Payment> listPayments(array $terms) List every payment in the payout.
 * @method \Generator<Stancer\Refund> listRefunds(array $terms) List every refund in the payout.
 * @method \Generator<Stancer\Payment> payments(array $terms) List every payment in the payout.
 * @method \Generator<Stancer\Refund> refunds(array $terms) List every refund in the payout.
 *
 * @property-read Stancer\Payout\Details\Inner $disputes Get disputes details.
 * @property-read Stancer\Payout\Details\Inner $payments Get payments details.
 * @property-read Stancer\Payout\Details\Inner $refunds Get refunds details.
 *
 * @phpstan-method \Generator<Stancer\Dispute> listDisputes(SearchFilters $terms)
 * @phpstan-method \Generator<Stancer\Payment> listPayments(SearchFilters $terms)
 * @phpstan-method \Generator<Stancer\Refund> listRefunds(SearchFilters $terms)
 */
class Details extends Stancer\Core\AbstractObject
{
    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected $dataModel = [
        'disputes' => [
            'restricted' => true,
            'type' => Stancer\Payout\Details\Inner::class,
        ],
        'payments' => [
            'restricted' => true,
            'type' => Stancer\Payout\Details\Inner::class,
        ],
        'refunds' => [
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
    public function __call(string $method, array $arguments)
    {
        $name = $this->snakeCaseToCamelCase($method);
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
