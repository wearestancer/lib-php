<?php

declare(strict_types=1);

namespace Stancer;

use Generator;
use Stancer;

/**
 * Representation of an intent.
 *
 * @method static add_methods_allowed($method) Add an allowed method.
 * @method static array<mixed> filter_list_params(array<mixed> $terms) Filter for list method.
 * @method ?\Stancer\ThreeDomainsSecure\Status get3DS() Get ask for an authenticated payment.
 * @method ?integer getAmount() Get intent amount.
 * @method ?\Stancer\Address getBillingAddress() Get billing address.
 * @method ?boolean getCapture() Get capture immediately the payment.
 * @method ?\Stancer\Card getCard() Get card object.
 * @method ?\DateTimeImmutable getCreated() Get creation date.
 * @method ?\Stancer\Currency getCurrency() Get processed currency.
 * @method ?\Stancer\Customer getCustomer() Get customer object.
 * @method ?string getDescription() Get intent description.
 * @method ?mixed getMetadata() Get arbitrary metadata.
 * @method Stancer\Payment\MethodsAllowed[] getMethodsAllowed() Get list of payment methods allowed for this intent.
 * @method ?string getOrderId() Get order identifier.
 * @method ?\Stancer\Payment getPayment() Get finalized payment.
 * @method ?string getPaymentPageUrl() Get payment page URL.
 * @method ?string getReturnUrl() Get URL to redirect back your customer after processing the payment.
 * @method ?\Stancer\Sepa getSepa() Get SEPA object.
 * @method ?\Stancer\Address getShippingAddress() Get shipping Address.
 * @method ?\Stancer\PaymentIntent\Status getStatus() Get status of the intent.
 * @method ?\Stancer\ThreeDomainsSecure\Status getThreeDS() Get ask for an authenticated payment.
 * @method ?\Stancer\ThreeDomainsSecure\Status getThreeds() Get ask for an authenticated payment.
 * @method ?string getUrl() Get payment page URL.
 * @method ?\Stancer\ThreeDomainsSecure\Status get_3ds() Get ask for an authenticated payment.
 * @method ?integer get_amount() Get intent amount.
 * @method ?\Stancer\Address get_billing_address() Get billing address.
 * @method ?boolean get_capture() Get capture immediately the payment.
 * @method ?\Stancer\Card get_card() Get card object.
 * @method ?\DateTimeImmutable get_created() Get creation date.
 * @method ?\DateTimeImmutable get_created_at() Get creation date.
 * @method ?\DateTimeImmutable get_creation_date() Get creation date.
 * @method ?\Stancer\Currency get_currency() Get processed currency.
 * @method ?\Stancer\Customer get_customer() Get customer object.
 * @method ?string get_description() Get intent description.
 * @method string get_endpoint() Get API endpoint.
 * @method string get_entity_name() Return the entity name
 * @method ?string get_id() Get object ID.
 * @method ?mixed get_metadata() Get arbitrary metadata.
 * @method Stancer\Payment\MethodsAllowed[] get_methods_allowed() Get list of payment methods allowed for this intent.
 * @method ?string get_order_id() Get order identifier.
 * @method ?\Stancer\Payment get_payment() Get finalized payment.
 * @method ?string get_payment_page_url() Get payment page URL.
 * @method ?string get_return_url() Get URL to redirect back your customer after processing the payment.
 * @method ?\Stancer\Sepa get_sepa() Get SEPA object.
 * @method ?\Stancer\Address get_shipping_address() Get shipping Address.
 * @method ?\Stancer\PaymentIntent\Status get_status() Get status of the intent.
 * @method ?\Stancer\ThreeDomainsSecure\Status get_three_ds() Get ask for an authenticated payment.
 * @method ?\Stancer\ThreeDomainsSecure\Status get_threeds() Get ask for an authenticated payment.
 * @method string get_uri() Get entity resource location.
 * @method ?string get_url() Get payment page URL.
 * @method \Generator list_payments(array<mixed> $terms) List payment associated to the payment intent.
 * @method $this set3DS(\Stancer\ThreeDomainsSecure\Status $3DS) Set ask for an authenticated payment.
 * @method $this setBillingAddress(\Stancer\Address $billingAddress) Set billing address.
 * @method $this setCapture(boolean $capture) Set capture immediately the payment.
 * @method $this setCard(\Stancer\Card $card) Set card object.
 * @method $this setCustomer(\Stancer\Customer $customer) Set customer object.
 * @method $this setDescription(string $description) Set intent description.
 * @method $this setOrderId(string $orderId) Set order identifier.
 * @method $this setSepa(\Stancer\Sepa $sepa) Set SEPA object.
 * @method $this setShippingAddress(\Stancer\Address $shippingAddress) Set shipping Address.
 * @method $this setThreeDS(\Stancer\ThreeDomainsSecure\Status $threeDS) Set ask for an authenticated payment.
 * @method $this setThreeds(\Stancer\ThreeDomainsSecure\Status $threeds) Set ask for an authenticated payment.
 * @method $this set_3ds(\Stancer\ThreeDomainsSecure\Status $3ds) Set ask for an authenticated payment.
 * @method $this set_amount(integer $amount) Set intent amount.
 * @method $this set_billing_address(\Stancer\Address $billing_address) Set billing address.
 * @method $this set_capture(boolean $capture) Set capture immediately the payment.
 * @method $this set_card(\Stancer\Card $card) Set card object.
 * @method $this set_currency(\Stancer\Currency $currency) Set processed currency.
 * @method $this set_customer(\Stancer\Customer $customer) Set customer object.
 * @method $this set_description(string $description) Set intent description.
 * @method $this set_metadata(mixed $metadata) Set arbitrary metadata.
 * @method $this set_methods_allowed(Stancer\Payment\MethodsAllowed[] $methods_allowed) Set list of payment
 *   methods allowed for this intent.
 * @method $this set_order_id(string $order_id) Set order identifier.
 * @method $this set_return_url(string $return_url) Set URL to redirect back your customer after processing the payment.
 * @method $this set_sepa(\Stancer\Sepa $sepa) Set SEPA object.
 * @method $this set_shipping_address(\Stancer\Address $shipping_address) Set shipping Address.
 * @method $this set_three_ds(\Stancer\ThreeDomainsSecure\Status $three_ds) Set ask for an authenticated payment.
 * @method $this set_threeds(\Stancer\ThreeDomainsSecure\Status $threeds) Set ask for an authenticated payment.
 *
 * @property ?\Stancer\ThreeDomainsSecure\Status $3DS Ask for an authenticated payment.
 * @property ?\Stancer\ThreeDomainsSecure\Status $3ds Ask for an authenticated payment.
 * @property ?integer $amount Intent amount.
 * @property ?\Stancer\Address $billingAddress Billing address.
 * @property ?\Stancer\Address $billing_address Billing address.
 * @property ?boolean $capture Capture immediately the payment.
 * @property ?\Stancer\Card $card Card object.
 * @property ?\Stancer\Currency $currency Processed currency.
 * @property ?\Stancer\Customer $customer Customer object.
 * @property ?string $description Intent description.
 * @property ?mixed $metadata Arbitrary metadata.
 * @property Stancer\Payment\MethodsAllowed[] $methodsAllowed List of payment methods allowed for this intent.
 * @property Stancer\Payment\MethodsAllowed[] $methods_allowed List of payment methods allowed for this intent.
 * @property ?string $orderId Order identifier.
 * @property ?string $order_id Order identifier.
 * @property ?string $returnUrl URL to redirect back your customer after processing the payment.
 * @property ?string $return_url URL to redirect back your customer after processing the payment.
 * @property ?\Stancer\Sepa $sepa SEPA object.
 * @property ?\Stancer\Address $shippingAddress Shipping Address.
 * @property ?\Stancer\Address $shipping_address Shipping Address.
 * @property ?\Stancer\ThreeDomainsSecure\Status $threeDS Ask for an authenticated payment.
 * @property ?\Stancer\ThreeDomainsSecure\Status $three_ds Ask for an authenticated payment.
 * @property ?\Stancer\ThreeDomainsSecure\Status $threeds Ask for an authenticated payment.
 *
 * @property-read ?\DateTimeImmutable $created Creation date.
 * @property-read ?\DateTimeImmutable $createdAt Creation date.
 * @property-read ?\DateTimeImmutable $created_at Creation date.
 * @property-read ?\DateTimeImmutable $creationDate Creation date.
 * @property-read ?\DateTimeImmutable $creation_date Creation date.
 * @property-read string $endpoint API endpoint.
 * @property-read ?string $id Object ID.
 * @property-read ?\Stancer\Payment $payment Finalized payment.
 * @property-read ?string $paymentPageUrl Payment page URL.
 * @property-read ?string $payment_page_url Payment page URL.
 * @property-read ?\Stancer\PaymentIntent\Status $status Status of the intent.
 * @property-read string $uri Entity resource location.
 * @property-read ?string $url Payment page URL.
 */
#[Stancer\Core\Documentation\PropertyAlias('3DS', 'threeds')]
#[Stancer\Core\Documentation\PropertyAlias('threeDS', 'threeds')]
#[Stancer\Core\Documentation\PropertyAlias('paymentPageUrl', 'url')]
class PaymentIntent extends Stancer\Core\AbstractObject
{
    use Stancer\Traits\SearchTrait;
    use Stancer\Traits\TransactionTrait;

    #[Stancer\WillChange\PHP8_3\TypedClassConstants]
    final public const ENDPOINT = 'payment_intents';

    final public const API_VERSION = Stancer\Enum\ApiVersion::VERSION_2;

    /**
     * @phpstan-var array<string, DataModel>
     */
    protected array $dataModel = [
        'amount' => [
            'desc' => 'Intent amount',
            'required' => true,
            'type' => self::INTEGER,
        ],
        'billingAddress' => [
            'desc' => 'Billing address',
            'onlyId' => true,
            'type' => Stancer\Address::class,
        ],
        'capture' => [
            'desc' => 'Capture immediately the payment',
            'type' => self::BOOLEAN,
        ],
        'card' => [
            'changed' => [
                [
                    'sinceVersion' => Stancer\Enum\ApiVersion::VERSION_2,
                    'onlyID' => true,
                ],
            ],
            'desc' => 'Card object',
            'type' => Stancer\Card::class,
        ],
        'currency' => [
            'desc' => 'Processed currency',
            'required' => true,
            'type' => Stancer\Currency::class,
        ],
        'customer' => [
            'changed' => [
                [
                    'sinceVersion' => Stancer\Enum\ApiVersion::VERSION_2,
                    'onlyID' => true,
                ],
            ],
            'desc' => 'Customer object',
            'type' => Stancer\Customer::class,
        ],
        'description' => [
            'desc' => 'Intent description',
            'size' => [
                'min' => 3,
                'max' => 64,
            ],
            'type' => self::STRING,
        ],
        'metadata' => [
            'desc' => 'Arbitrary metadata',
            'type' => self::MIXED,
        ],
        'methodsAllowed' => [
            'desc' => 'List of payment methods allowed for this intent',
            'list' => true,
            'type' => Stancer\Payment\MethodsAllowed::class,
        ],
        'orderId' => [
            'desc' => 'Order identifier',
            'size' => [
                'min' => 1,
                'max' => 36,
            ],
            'type' => self::STRING,
        ],
        'payment' => [
            'desc' => 'Finalized payment',
            'restricted' => true,
            'type' => Stancer\Payment::class,
        ],
        'returnUrl' => [
            'desc' => 'URL to redirect back your customer after processing the payment',
            'size' => [
                'min' => 1,
                'max' => 65536,
            ],
            'type' => self::STRING,
        ],
        'sepa' => [
            'changed' => [
                [
                    'sinceVersion' => Stancer\Enum\ApiVersion::VERSION_2,
                    'onlyID' => true,
                ],
            ],
            'desc' => 'SEPA object',
            'type' => Stancer\Sepa::class,
        ],
        'shippingAddress' => [
            'desc' => 'Shipping Address',
            'onlyId' => true,
            'type' => Stancer\Address::class,
        ],
        'status' => [
            'desc' => 'Status of the intent',
            'restricted' => true,
            'type' => Stancer\PaymentIntent\Status::class,
        ],
        'threeds' => [
            'desc' => 'Ask for an authenticated payment',
            'type' => Stancer\ThreeDomainsSecure\Status::class,
        ],
        'url' => [
            'desc' => 'Payment page URL',
            'restricted' => true,
            'type' => self::STRING,
        ],
    ];

    /**
     * Handle getter and setter for every properties.
     *
     * @uses self::dataModelAdder() When method starts with `add`.
     * @uses self::dataModelGetter() When method starts with `get`.
     * @uses self::dataModelSetter() When method starts with `set`.
     *
     * @param string $method Method called.
     * @param mixed[] $arguments Arguments used during the call.
     *
     * @throws Stancer\Exceptions\BadMethodCallException When an unhandled method is called.
     */
    #[\Override]
    public function __call(string $method, array $arguments): mixed
    {
        $lower = strtolower($method);

        switch ($lower) {
            case 'get3ds':
            case 'get_3ds':
            case 'getthreeds':
            case 'get_three_ds':
                return parent::__call('getthreeds', $arguments);

            case 'set3ds':
            case 'set_3ds':
            case 'setthreeds':
            case 'set_three_ds':
                return parent::__call('setthreeds', $arguments);

            case 'list_payments':
            case 'payments':
                if (is_array($arguments[0])) {
                    return $this->listPayments($arguments[0]);
                }

                return $this->listPayments([]);

            case 'created':
                return parent::getCreatedAt();

            case 'geturl':
            case 'get_url':
            case 'getpaymentpageurl':
            case 'get_payment_page_url':
                return parent::__call('geturl', $arguments);

            default:
                return parent::__call($method, $arguments);
        }
    }

    /**
     * Filter for list method.
     *
     * `$terms` must be an associative array with one of the following key : `order_id`, `card`, `sepa`.
     *
     * `order_id`, `card` and `sepa` will be treated as a string and will filter payments corresponding to the data
     * you specified in your initial payment request.
     *
     * @param array $terms Search terms. May have `order_id`, `card` or `sepa` key.
     *
     * @phpstan-param array{card?: string, order_id?: string, sepa?: string} $terms
     *
     * @phpstan-return array{card?: string, order_id?: string, sepa?: string}
     *
     * @throws Stancer\Exceptions\InvalidSearchOrderIdFilterException When `order_id` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchCardFilterException When `card` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchSepaFilterException When `sepa` is invalid.
     */
    public static function filterListParams(array $terms): array
    {
        $params = [];

        if (array_key_exists('card', $terms)) {
            $params['card'] = $terms['card'];
            if ($params['card'] instanceof Stancer\Card) {
                $params['card'] = $params['card']->id;
            } elseif (!is_string($params['card'])) {
                throw new Stancer\Exceptions\InvalidSearchCardFilterException('Card must be a card object or a string.');
            }

            if (strlen($params['card']) !== 29) {
                throw new Stancer\Exceptions\InvalidSearchCardFilterException(
                    'A valid Card reference must have 29 characters.'
                );
            }
        }

        if (array_key_exists('order_id', $terms)) {
            $params['order_id'] = $terms['order_id'];

            if (!is_string($terms['order_id'])) {
                throw new Stancer\Exceptions\InvalidSearchOrderIdFilterException('Order ID must be a string.');
            }

            if (strlen($terms['order_id']) > 36 || !$terms['order_id']) {
                throw new Stancer\Exceptions\InvalidSearchOrderIdFilterException(
                    'A valid order ID must be between 1 and 36 characters.'
                );
            }
        }

        if (array_key_exists('sepa', $terms)) {
            $params['sepa'] = $terms['sepa'];

            if ($params['sepa'] instanceof Stancer\Sepa) {
                $params['sepa'] = $params['sepa']->id;
            }
            if (!is_string($params['sepa'])) {
                throw new Stancer\Exceptions\InvalidSearchSepaFilterException('SEPA must be a string.');
            }

            if (strlen($params['sepa']) !== 29) {
                throw new Stancer\Exceptions\InvalidSearchSepaFilterException(
                    'A valid SEPA reference must have 29 characters.'
                );
            }
        }

        return $params;
    }

    /**
     * Return the entity name
     * This could lead to bugs as, for all other entities, their names are the Class name.
     */
    #[\Override]
    public function getEntityName(): string
    {
        return 'payment_intent';
    }

    /**
     * List payment associated to the payment intent.
     *
     * @param array<mixed,mixed> $terms
     *
     * @return \Generator A generator that yelds the objects listed.
     * @throws Stancer\Exceptions\InvalidSearchFilterException Invalid parameter to listPayments.
     */
    public function listPayments(array $terms): \Generator
    {
        return $this->search(Payment::class, 'payments', $terms, 'payments');
    }

    /**
     * Set the intent amount.
     *
     * @param integer $amount New amount.
     *
     * @throws Stancer\Exceptions\InvalidAmountException If the amount is less than 50.
     */
    public function setAmount(int $amount): self
    {
        if ($amount < 50) {
            throw new Stancer\Exceptions\InvalidAmountException();
        }

        return parent::setAmount($amount);
    }

    /**
     * Set metadata.
     *
     * @param mixed $data Arbitrary data.
     *
     * @return $this
     * @throws Stancer\Exceptions\InvalidMetadataException If data is neither a JSON serializable nor stringable object.
     */
    public function setMetadata(mixed $data): static
    {
        if (is_object($data) && !($data instanceof \JsonSerializable || $data instanceof \Stringable)) {
            $message = 'Objects are not allowed if not JSON serializable or stringable.';

            throw new Stancer\Exceptions\InvalidMetadataException($message);
        }

        return parent::setMetadata($data);
    }

    /**
     * Set allowed methods.
     *
     * @param non-empty-array<Stancer\Currency|string> $methods New methods.
     *
     * @return $this
     * @throws Stancer\Exceptions\InvalidArgumentException When currency is EUR and trying to set "sepa" method.
     * @throws Stancer\Exceptions\InvalidArgumentException When the method is invalid.
     */
    public function setMethodsAllowed(array $methods): static
    {
        $new = [];
        $cast = fn (Stancer\Payment\MethodsAllowed $case): string => $case->value;

        foreach ($methods as $method) {
            try {
                if (is_string($method)) {
                    $new[] = Stancer\Payment\MethodsAllowed::from(strtolower($method));
                } else {
                    $new[] = $method;
                }
            } catch (\ValueError $exception) {
                $params = [
                    $method,
                    implode(', ', array_map($cast, Stancer\Payment\MethodsAllowed::cases())),
                ];
                $message = vsprintf('"%s" is not a valid method, please use one of the following: %s', $params);

                throw new Stancer\Exceptions\InvalidArgumentException($message, previous: $exception);
            }
        }

        $currency = $this->getCurrency();
        $status = [
            null,
            Stancer\PaymentIntent\Status::REQUIRE_PAYMENT_METHOD,
            Stancer\PaymentIntent\Status::UNPAID,
        ];

        if (
            in_array($this->status, $status, true)
            && in_array(Stancer\Payment\MethodsAllowed::SEPA, $new, true)
            && $currency
            && $currency !== Stancer\Currency::EUR
        ) {
            $message = sprintf('You can not use "%s" method with "%s" currency.', 'sepa', $currency->value);

            throw new Stancer\Exceptions\InvalidArgumentException($message);
        }

        return parent::setMethodsAllowed($new);
    }
}
