<?php
declare(strict_types=1);

namespace Stancer\Payment;

use DateTimeImmutable;
use Exception;
use Generator;
use Override;
use Stancer;
use Stancer\Stub\Payment;
use ValueError;

/**
 * Representation of an intent.
 *
 * @method static add_methods_allowed($method) Add an allowed method.
 * @method static array filter_list_params(array $terms) Filter for list method.
 * @method ?\Stancer\ThreeDomainsSecure\Status get3DS() Get ask for an authenticated payment.
 * @method ?integer getAmount() Get intent amount.
 * @method ?boolean getCapture() Get capture immediately the payment.
 * @method ?\DateTimeImmutable getCreated() Get creation date.
 * @method ?\Stancer\Currency getCurrency() Get processed currency.
 * @method ?string getDescription() Get intent description.
 * @method ?mixed getMetadata() Get arbitrary metadata.
 * @method Stancer\Payment\MethodsAllowed[] getMethodsAllowed() Get list of payment methods allowed for this intent.
 * @method ?string getOrderId() Get order identifier.
 * @method ?\Stancer\Payment getPayment() Get finalized payment.
 * @method ?string getReturnUrl() Get URL to redirect back your customer after processing the payment.
 * @method ?\Stancer\Sepa getSepa() Get SEPA object.
 * @method ?\Stancer\Payment\Intent\Status getStatus() Get status of the intent.
 * @method ?\Stancer\ThreeDomainsSecure\Status getThreeDS() Get ask for an authenticated payment.
 * @method ?\Stancer\ThreeDomainsSecure\Status getThreeds() Get ask for an authenticated payment.
 * @method ?string getUrl() Get payment page URL.
 * @method ?\Stancer\ThreeDomainsSecure\Status get_3ds() Get ask for an authenticated payment.
 * @method ?integer get_amount() Get intent amount.
 * @method ?boolean get_capture() Get capture immediately the payment.
 * @method ?\Stancer\Card get_card() Get card object.
 * @method ?\DateTimeImmutable get_created() Get creation date.
 * @method ?\DateTimeImmutable get_created_at() Get creation date.
 * @method ?\DateTimeImmutable get_creation_date() Get creation date.
 * @method ?\Stancer\Currency get_currency() Get processed currency.
 * @method ?\Stancer\Customer get_customer() Get customer object.
 * @method ?string get_description() Get intent description.
 * @method string get_endpoint() Get API endpoint.
 * @method string get_entity_name()
 * @method ?string get_id() Get object ID.
 * @method ?mixed get_metadata() Get arbitrary metadata.
 * @method Stancer\Payment\MethodsAllowed[] get_methods_allowed() Get list of payment methods allowed for this intent.
 * @method ?string get_order_id() Get order identifier.
 * @method ?\Stancer\Payment get_payment() Get finalized payment.
 * @method ?string get_return_url() Get URL to redirect back your customer after processing the payment.
 * @method ?\Stancer\Sepa get_sepa() Get SEPA object.
 * @method ?\Stancer\Payment\Intent\Status get_status() Get status of the intent.
 * @method ?\Stancer\ThreeDomainsSecure\Status get_three_ds() Get ask for an authenticated payment.
 * @method ?\Stancer\ThreeDomainsSecure\Status get_threeds() Get ask for an authenticated payment.
 * @method string get_uri() Return resource location.
 * @method ?string get_url() Get payment page URL.
 * @method Generator list_payments(array $terms) List payment associated to the payment intent.
 * @method $this set3DS(\Stancer\ThreeDomainsSecure\Status $3DS) Set ask for an authenticated payment.
 * @method $this setCapture(boolean $capture) Set capture immediately the payment.
 * @method $this setDescription(string $description) Set intent description.
 * @method $this setOrderId(string $orderId) Set order identifier.
 * @method $this setSepa(\Stancer\Sepa $sepa) Set SEPA object.
 * @method $this setThreeDS(\Stancer\ThreeDomainsSecure\Status $threeDS) Set ask for an authenticated payment.
 * @method $this setThreeds(\Stancer\ThreeDomainsSecure\Status $threeds) Set ask for an authenticated payment.
 * @method $this set_3ds(\Stancer\ThreeDomainsSecure\Status $3ds) Set ask for an authenticated payment.
 * @method $this set_amount(integer $amount) Set intent amount.
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
 * @method $this set_three_ds(\Stancer\ThreeDomainsSecure\Status $three_ds) Set ask for an authenticated payment.
 * @method $this set_threeds(\Stancer\ThreeDomainsSecure\Status $threeds) Set ask for an authenticated payment.
 *
 * @property ?\Stancer\ThreeDomainsSecure\Status $3DS Ask for an authenticated payment.
 * @property ?\Stancer\ThreeDomainsSecure\Status $3ds Ask for an authenticated payment.
 * @property ?integer $amount Intent amount.
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
 * @property-read ?\Stancer\Payment\Intent\Status $status Status of the intent.
 * @property-read ?string $url Payment page URL.
 */
#[Stancer\Core\Documentation\PropertyAlias('3DS', 'threeds')]
#[Stancer\Core\Documentation\PropertyAlias('threeDS', 'threeds')]
class Intent extends Stancer\Core\AbstractObject
{
    use Stancer\Traits\SearchTrait;

    #[Stancer\WillChange\PHP8_3\TypedClassConstants]
    final public const ENDPOINT = 'payment_intents';

    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected array $dataModel = [
        'amount' => [
            'desc' => 'Intent amount',
            'required' => true,
            'type' => self::INTEGER,
        ],
        'capture' => [
            'desc' => 'Capture immediately the payment',
            'type' => self::BOOLEAN,
        ],
        'card' => [
            'desc' => 'Card object',
            'type' => Stancer\Card::class,
        ],
        'currency' => [
            'desc' => 'Processed currency',
            'required' => true,
            'type' => Stancer\Currency::class,
        ],
        'customer' => [
            'desc' => 'Customer object',
            'type' => Stancer\Customer::class,
        ],
        'createdAt' => [
            'desc' => 'Date of creation',
            'restricted' => true,
            'type' => DateTimeImmutable::class,
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
            'desc' => 'SEPA object',
            'type' => Stancer\Sepa::class,
        ],
        'status' => [
            'desc' => 'Status of the intent',
            'restricted' => true,
            'type' => Stancer\Payment\Intent\Status::class,
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
     * @param string $method Method called.
     * @param mixed[] $arguments Arguments used during the call.
     * @return mixed
     * @throws Stancer\Exceptions\BadMethodCallException When an unhandled method is called.
     */
    #[Override]
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
                return $this->listPayments($arguments[0]);

            case 'created':
                return parent::getCreatedAt();

            default:
                return parent::__call($method, $arguments);
        }
    }

    /**
     * Add an allowed method.
     *
     * @param Stancer\Payment\MethodsAllowed|string $method New method.
     * @return $this
     * @throws Stancer\Exceptions\InvalidArgumentException When currency is EUR and trying to set "sepa" method.
     */
    public function addMethodsAllowed(Stancer\Payment\MethodsAllowed|string $method): static
    {
        $currency = $this->getCurrency();

        if ($currency && $method && $method === 'sepa' && $currency !== Stancer\Currency::EUR) {
            $message = sprintf('You can not use "%s" method with "%s" currency.', $method, $currency->value);

            throw new Stancer\Exceptions\InvalidArgumentException($message);
        }

        // @phpstan-ignore-next-line The method is not defined in parent object so it will trigger __call ...
        return parent::addMethodsAllowed($method);
        // ... and that's that we want
    }

    /**
     * Capture a Payment Intent
     *
     * TODO: Finish The capture.
     *
     * @return static
     */
    public function capture()
    {
        $this->send();
        return $this;
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
     * @return array
     * @throws Stancer\Exceptions\InvalidSearchOrderIdFilterException When `order_id` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchCardFilterException When `card` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchSepaFilterException When `sepa` is invalid.
     *
     * @phpstan-param array{card?: string, order_id?: string, sepa?: string} $terms
     * @phpstan-return array{card?: string, order_id?: string, sepa?: string}
     */
    public static function filterListParams(array $terms): array
    {
        $params = [];

        if (array_key_exists('card', $terms)) {
            $params['card'] = $terms['card'];
            $type = gettype($terms['card']);

            if ($type !== 'string') {
                throw new Stancer\Exceptions\InvalidSearchCardFilterException('Card must be a string.');
            }

            if (strlen($terms['card']) !== 29) {
                $message = 'A valid Card reference must have 29 characters.';

                throw new Stancer\Exceptions\InvalidSearchCardFilterException($message);
            }
        }

        if (array_key_exists('order_id', $terms)) {
            $params['order_id'] = $terms['order_id'];
            $type = gettype($terms['order_id']);

            if ($type !== 'string') {
                throw new Stancer\Exceptions\InvalidSearchOrderIdFilterException('Order ID must be a string.');
            }

            if (strlen($terms['order_id']) > 36 || !$terms['order_id']) {
                $message = 'A valid order ID must be between 1 and 36 characters.';

                throw new Stancer\Exceptions\InvalidSearchOrderIdFilterException($message);
            }
        }

        if (array_key_exists('sepa', $terms)) {
            $params['sepa'] = $terms['sepa'];
            $type = gettype($terms['sepa']);

            if ($type !== 'string') {
                throw new Stancer\Exceptions\InvalidSearchSepaFilterException('SEPA must be a string.');
            }

            if (strlen($terms['sepa']) !== 29) {
                $message = 'A valid SEPA reference must have 29 characters.';

                throw new Stancer\Exceptions\InvalidSearchSepaFilterException($message);
            }
        }
        return $params;
    }

    /**
     * Return a card from an ID.
     *
     * @return Stancer\Card|null
     */
    public function getCard(): ?Stancer\Card
    {
        $card = parent::getCard();
        if ($card === null) {
            return null;
        }
        return new Stancer\Card($card);
    }

    /**
     * Return a customer from an ID.
     *
     * @return Stancer\Customer|null
     */
    public function getCustomer(): ?Stancer\Customer
    {
        $customer = parent::getCustomer();
        if ($customer === null) {
            return null;
        }
        return new Stancer\Customer($customer);
    }

    /**
     * Return creation date.
     *
     * @return DateTimeImmutable|null
     */
    #[Stancer\Core\Documentation\FormatProperty(
        description: 'Creation date',
        restricted: true,
        type: DateTimeImmutable::class,
    )]
    public function getCreationDate(): ?DateTimeImmutable
    {
        return parent::getCreatedAt();
    }

    /**
     * Return the entity name
     * This could lead to bugs as, for all other entities, their names are the Class name.
     *
     * @return string
     */
    #[\Override]
    public function getEntityName(): string
    {
        return 'payment_intent';
    }

    /**
     * Return resource location.
     *
     * @return string
     */
    #[\Override]
    public function getUri(): string
    {
        return parent::getUri();
    }

    /**
     * List payment associated to the payment intent.
     *
     * @param mixed|array<mixed> $terms Research parameters.
     * @return Generator A generator that yelds the objects listed.
     * @throws Stancer\Exceptions\InvalidSearchFilterException Invalid parameter to listPayments.
     */
    public function listPayments(mixed $terms): Generator
    {
        if (!is_array($terms)) {
            throw new Stancer\Exceptions\InvalidSearchFilterException();
        }
        return $this->search(Payment::class, 'payments', $terms, 'payments');
    }

    /**
     * Set the intent amount.
     *
     * @param integer $amount New amount.
     *
     * @return self
     *
     * @throws Stancer\Exceptions\InvalidAmountException If the amount is less than 50.
     */
    public function setAmount(int $amount): self
    {
        if ($amount && $amount < 50) {
            throw new Stancer\Exceptions\InvalidAmountException();
        }

        return parent::setAmount($amount);
    }

    /**
     * Set A card, by id or by object.
     *
     * @param Stancer\Card|string $card A card Object or it's ID.
     * @return $this
     * @throws Stancer\Exceptions\InvalidUniqueIdException When the card is invalid.
     */
    public function setCard(Stancer\Card|string $card): static
    {
        try {
            if (is_string($card)) {
                $new = $card;
            } else {
                $new = $card->getId() ?? $card->send()->getId();
            }
        } catch (ValueError $exception) {
            throw new Stancer\Exceptions\InvalidUniqueIdException();
        }
        return parent::setCard($new);
    }

    /**
     * Set the currency.
     *
     * @param Stancer\Currency|string $currency The currency.
     * @return $this
     * @throws Stancer\Exceptions\InvalidCurrencyException When currency is EUR and "sepa" is already allowed.
     * @throws Stancer\Exceptions\InvalidCurrencyException When the currency is invalid.
     */
    public function setCurrency(Stancer\Currency|string $currency): static
    {
        try {
            if (is_string($currency)) {
                $new = Stancer\Currency::from(strtolower($currency));
            } else {
                $new = $currency;
            }
        } catch (ValueError $exception) {
            $params = [
                $currency,
                implode(', ', array_map(fn(Stancer\Currency $case): string => $case->value, Stancer\Currency::cases())),
            ];
            $message = vsprintf('"%s" is not a valid currency, please use one of the following: %s', $params);

            throw new Stancer\Exceptions\InvalidCurrencyException($message, previous: $exception);
        }

        $methods = $this->getMethodsAllowed();

        if (in_array(Stancer\Payment\MethodsAllowed::SEPA, $methods, true) && $new !== Stancer\Currency::EUR) {
            $message = sprintf('You can not use "%s" currency with "%s" method.', $new->value, 'sepa');

            throw new Stancer\Exceptions\InvalidCurrencyException($message);
        }

        return parent::setCurrency($new);
    }

    /**
     * Set a customer by id or by object.
     *
     * @param Stancer\Customer|string $customer A customer object or it's ID.
     * @return $this
     * @throws Stancer\Exceptions\InvalidUniqueIdException When the customer is invalid.
     */
    public function setCustomer(Stancer\Customer|string $customer): static
    {
        try {
            if (is_string($customer)) {
                $new = $customer;
            } else {
                $new = $customer->getId() ?? $customer->send()->getId();
            }
        } catch (ValueError $exception) {
            throw new Stancer\Exceptions\InvalidUniqueIdException();
        }
        return parent::setCustomer($new);
    }

    /**
     * Set metadata.
     *
     * @param mixed $data Arbitrary data.
     * @return $this
     * @throws Stancer\Exceptions\InvalidMetadataException When data is neither a JSON serializable or stringable object.
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
     * @return $this
     * @throws Stancer\Exceptions\InvalidArgumentException When currency is EUR and trying to set "sepa" method.
     * @throws Stancer\Exceptions\InvalidArgumentException When the method is invalid.
     */
    public function setMethodsAllowed(array $methods): static
    {
        $new = [];
        $cast = fn(Stancer\Payment\MethodsAllowed $case): string => $case->value;

        foreach ($methods as $method) {
            try {
                if (is_string($method)) {
                    $new[] = Stancer\Payment\MethodsAllowed::from(strtolower($method));
                } else {
                    $new[] = $method;
                }
            } catch (ValueError $exception) {
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
            Stancer\Payment\Intent\Status::REQUIRE_PAYMENT_METHOD,
            Stancer\Payment\Intent\Status::UNPAID,
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

    /**
     * Update return URL.
     *
     * @param string $url New HTTPS URL.
     * @return $this
     * @throws Stancer\Exceptions\InvalidUrlException When URL is not an HTTPS URL.
     */
    public function setReturnUrl(string $url): self
    {
        if (strpos($url, 'https://') !== 0) {
            throw new Stancer\Exceptions\InvalidUrlException('You must provide an HTTPS URL.');
        }

        return parent::setReturnUrl($url);
    }
}
