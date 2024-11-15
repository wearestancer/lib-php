<?php
declare(strict_types=1);

namespace Stancer;

use DateTime;
use DateTimeImmutable;
use Override;
use Stancer;
use ValueError;

/**
 * Representation of a payment.
 *
 * @method static add_methods_allowed($method) Add an allowed method.
 * @method static array<mixed> filter_list_params(array<mixed> $terms) Filter for list method.
 * @method ?integer getAmount() Get transaction amount.
 * @method ?\Stancer\Auth getAuth() Get auth object, must be set for 3-D Secure card payments.
 * @method ?boolean getCapture() Get capture immediately the payment.
 * @method ?\Stancer\Card getCard() Get card object.
 * @method ?string getCountry()
 * @method ?\DateTimeImmutable getCreated() Get creation date.
 * @method ?\Stancer\Currency getCurrency() Get processed currency.
 * @method ?\Stancer\Customer getCustomer() Get customer object.
 * @method ?\DateTimeImmutable getDateBank() Get delivery date of the funds traded by the bank.
 * @method ?string getDescription() Get payment description.
 * @method ?\Stancer\Device getDevice() Get customer's device object.
 * @method ?string getMethod() Get payment method used.
 * @method Stancer\Payment\MethodsAllowed[] getMethodsAllowed() Get list of payment methods allowed for this payment.
 * @method ?string getOrderId() Get order identifier.
 * @method Stancer\Refund[] getRefunds() Get array of refund objects.
 * @method ?string getResponse() Get response of the bank processing.
 * @method ?string getResponseAuthor()
 * @method ?string getReturnUrl() Get URL to redirect back your customer after processing the payment.
 * @method ?\Stancer\Sepa getSepa() Get SEPA object.
 * @method ?\Stancer\Payment\Status getStatus() Get status of the payment.
 * @method ?string getUniqueId() Get unicity key.
 * @method ?integer get_amount() Get transaction amount.
 * @method ?\Stancer\Auth get_auth() Get auth object, must be set for 3-D Secure card payments.
 * @method ?boolean get_capture() Get capture immediately the payment.
 * @method ?\Stancer\Card get_card() Get card object.
 * @method ?string get_country()
 * @method ?\DateTimeImmutable get_created() Get creation date.
 * @method ?\DateTimeImmutable get_creation_date() Get creation date.
 * @method ?\Stancer\Currency get_currency() Get processed currency.
 * @method ?\Stancer\Customer get_customer() Get customer object.
 * @method ?\DateTimeImmutable get_date_bank() Get delivery date of the funds traded by the bank.
 * @method ?string get_description() Get payment description.
 * @method ?\Stancer\Device get_device() Get customer's device object.
 * @method string get_endpoint() Get API endpoint.
 * @method string get_entity_name() Get entity name.
 * @method ?string get_id() Get object ID.
 * @method ?string get_method() Get payment method used.
 * @method Stancer\Payment\MethodsAllowed[] get_methods_allowed() Get list of payment methods allowed for this payment.
 * @method ?string get_order_id() Get order identifier.
 * @method string get_payment_page_url(array<mixed> $params = [], boolean $force = false) Return the URL for Stancer
 *   payment page.
 * @method int get_refundable_amount() Return the refundable amount.
 * @method int get_refunded_amount() Return the already refunded amount.
 * @method Stancer\Refund[] get_refunds() Get array of refund objects.
 * @method ?string get_response() Get response of the bank processing.
 * @method ?string get_response_author()
 * @method ?string get_return_url() Get URL to redirect back your customer after processing the payment.
 * @method ?\Stancer\Sepa get_sepa() Get SEPA object.
 * @method ?\Stancer\Payment\Status get_status() Get status of the payment.
 * @method ?string get_unique_id() Get unicity key.
 * @method string get_uri() Get entity resource location.
 * @method boolean is_error() Indicates if payment is an error.
 * @method boolean is_not_error() Indicates if payment is not an error.
 * @method boolean is_not_success() Indicates if payment is not a success.
 * @method boolean is_success() Indicates if payment is a success.
 * @method static \Generator<static> list(SearchFilters $terms)
 * @method $this setCapture(boolean $capture) Set capture immediately the payment.
 * @method $this setCountry(string $country)
 * @method $this setCustomer(\Stancer\Customer $customer) Set customer object.
 * @method $this setDescription(string $description) Set payment description.
 * @method $this setDevice(\Stancer\Device $device) Set customer's device object.
 * @method $this setOrderId(string $orderId) Set order identifier.
 * @method $this setUniqueId(string $uniqueId) Set unicity key.
 * @method $this set_amount(integer $amount) Set transaction amount.
 * @method $this set_auth(\Stancer\Auth|boolean|string $auth) Set auth object, must be set for 3-D Secure card payments.
 * @method $this set_capture(boolean $capture) Set capture immediately the payment.
 * @method $this set_card(\Stancer\Card $card) Set card object.
 * @method $this set_country(string $country)
 * @method $this set_currency(\Stancer\Currency $currency) Set processed currency.
 * @method $this set_customer(\Stancer\Customer $customer) Set customer object.
 * @method $this set_description(string $description) Set payment description.
 * @method $this set_device(\Stancer\Device $device) Set customer's device object.
 * @method $this set_methods_allowed(Stancer\Payment\MethodsAllowed[] $methods_allowed) Set list of payment
 *   methods allowed for this payment.
 * @method $this set_order_id(string $order_id) Set order identifier.
 * @method $this set_return_url(string $return_url) Set URL to redirect back your customer after processing the payment.
 * @method $this set_sepa(\Stancer\Sepa $sepa) Set SEPA object.
 * @method $this set_status(\Stancer\Payment\Status $status) Set status of the payment.
 * @method $this set_unique_id(string $unique_id) Set unicity key.
 *
 * @phpstan-method $this addRefunds(Stancer\Refund $refund)
 *
 * @property ?integer $amount Transaction amount.
 * @property \Stancer\Auth|boolean|string|null $auth Auth object, must be set for 3-D Secure card payments.
 * @property ?boolean $capture Capture immediately the payment.
 * @property ?\Stancer\Card $card Card object.
 * @property ?string $country
 * @property ?\Stancer\Currency $currency Processed currency.
 * @property ?\Stancer\Customer $customer Customer object.
 * @property ?string $description Payment description.
 * @property ?\Stancer\Device $device Customer's device object.
 * @property Stancer\Payment\MethodsAllowed[] $methodsAllowed List of payment methods allowed for this payment.
 * @property Stancer\Payment\MethodsAllowed[] $methods_allowed List of payment methods allowed for this payment.
 * @property ?string $orderId Order identifier.
 * @property ?string $order_id Order identifier.
 * @property ?string $returnUrl URL to redirect back your customer after processing the payment.
 * @property ?string $return_url URL to redirect back your customer after processing the payment.
 * @property ?\Stancer\Sepa $sepa SEPA object.
 * @property ?\Stancer\Payment\Status $status Status of the payment.
 * @property ?string $uniqueId Unicity key.
 * @property ?string $unique_id Unicity key.
 *
 * @property-read ?\DateTimeImmutable $created Creation date.
 * @property-read ?\DateTimeImmutable $creationDate Creation date.
 * @property-read ?\DateTimeImmutable $creation_date Creation date.
 * @property-read ?\DateTimeImmutable $dateBank Delivery date of the funds traded by the bank.
 * @property-read ?\DateTimeImmutable $date_bank Delivery date of the funds traded by the bank.
 * @property-read string $endpoint API endpoint.
 * @property-read string $entityName Entity name.
 * @property-read string $entity_name Entity name.
 * @property-read ?string $id Object ID.
 * @property-read boolean $isError Alias for `Stancer\Payment::isError()`.
 * @property-read boolean $isNotError Alias for `Stancer\Payment::isNotError()`.
 * @property-read boolean $isNotSuccess Alias for `Stancer\Payment::isNotSuccess()`.
 * @property-read boolean $isSuccess Alias for `Stancer\Payment::isSuccess()`.
 * @property-read boolean $is_error Alias for `Stancer\Payment::isError()`.
 * @property-read boolean $is_not_error Alias for `Stancer\Payment::isNotError()`.
 * @property-read boolean $is_not_success Alias for `Stancer\Payment::isNotSuccess()`.
 * @property-read boolean $is_success Alias for `Stancer\Payment::isSuccess()`.
 * @property-read ?string $method Payment method used.
 * @property-read Stancer\Refund[] $refunds Array of refund objects.
 * @property-read ?string $response Response of the bank processing.
 * @property-read ?string $responseAuthor
 * @property-read ?string $response_author
 * @property-read string $uri Entity resource location.
 */
#[Stancer\Core\Documentation\AddMethod('addRefunds', ['Stancer\Refund $refund'], '$this', stan: true)]
#[Stancer\Core\Documentation\AddMethod('list', ['SearchFilters $terms'], 'static Generator<static>')]
#[Stancer\Core\Documentation\AddProperty(
    'auth',
    property: [
        'type' => [
            Stancer\Auth::class,
            'bool',
            'string',
        ],
    ],
    setter: [
        'type' => [
            Stancer\Auth::class,
            'bool',
            'string',
        ],
    ],
)]
#[Stancer\Core\Documentation\AddProperty('refunds', restricted: true)]
class Payment extends Stancer\Core\AbstractObject
{
    use Stancer\Traits\AmountTrait;
    use Stancer\Traits\SearchTrait;

    #[Stancer\WillChange\PHP8_3\TypedClassConstants]
    final public const ENDPOINT = 'checkout';

    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected array $dataModel = [
        'amount' => [
            'desc' => 'Transaction amount',
            'required' => true,
            'type' => self::INTEGER,
        ],
        'auth' => [
            'desc' => 'Auth object, must be set for 3-D Secure card payments',
            'type' => Stancer\Auth::class,
        ],
        'capture' => [
            'desc' => 'Capture immediately the payment',
            'type' => self::BOOLEAN,
        ],
        'card' => [
            'desc' => 'Card object',
            'type' => Stancer\Card::class,
        ],
        'country' => [
            'type' => self::STRING,
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
        'dateBank' => [
            'desc' => 'Delivery date of the funds traded by the bank',
            'restricted' => true,
            'type' => DateTimeImmutable::class,
        ],
        'description' => [
            'desc' => 'Payment description',
            'size' => [
                'min' => 3,
                'max' => 64,
            ],
            'type' => self::STRING,
        ],
        'device' => [
            'desc' => 'Customer\'s device object',
            'type' => Stancer\Device::class,
        ],
        'method' => [
            'desc' => 'Payment method used',
            'restricted' => true,
            'type' => self::STRING,
        ],
        'methodsAllowed' => [
            'desc' => 'List of payment methods allowed for this payment',
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
        'refunds' => [
            'desc' => 'Array of refund objects',
            'exportable' => false,
            'list' => true,
            'type' => Stancer\Refund::class,
        ],
        'response' => [
            'desc' => 'Response of the bank processing',
            'restricted' => true,
            'size' => [
                'min' => 2,
                'max' => 4,
            ],
            'type' => self::STRING,
        ],
        'responseAuthor' => [
            'restricted' => true,
            'size' => [
                'fixed' => 6,
            ],
            'type' => self::STRING,
        ],
        'returnUrl' => [
            'desc' => 'URL to redirect back your customer after processing the payment',
            'size' => [
                'min' => 1,
                'max' => 2048,
            ],
            'type' => self::STRING,
        ],
        'sepa' => [
            'desc' => 'SEPA object',
            'type' => Stancer\Sepa::class,
        ],
        'status' => [
            'desc' => 'Status of the payment',
            'type' => Stancer\Payment\Status::class,
        ],
        'uniqueId' => [
            'desc' => 'Unicity key',
            'size' => [
                'min' => 1,
                'max' => 36,
            ],
            'type' => self::STRING,
        ],
    ];

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
     * Charge a card or a bank account.
     *
     * This method is Stripe compatible.
     *
     * @param array $options Charge options.
     * @return $this
     *
     * @phpstan-param PaymentChargeOptions $options
     */
    public static function charge(array $options): static
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated', E_USER_DEPRECATED);

        $obj = new static();
        $source = $options['source'];
        $id = null;
        $class = Card::class;
        $method = 'setCard';
        $data = [];

        if (is_array($source)) {
            if (array_key_exists('id', $source)) {
                $id = $source['id'];
            }

            $data = $source;
        } else {
            $id = $source;
        }

        if ($id && strpos($id, 'sepa_') === 0) {
            $class = Sepa::class;
            $method = 'setSepa';
        }

        /** @phpstan-var PaymentChargeOptions $data */
        if (array_key_exists('account_holder_name', $data)) {
            $data['name'] = $data['account_holder_name'];
        }

        /** @phpstan-var PaymentChargeOptions $data */
        if (array_key_exists('account_number', $data)) {
            $data['iban'] = $data['account_number'];
            $class = Sepa::class;
            $method = 'setSepa';
        }

        $means = new $class($id);

        return $obj->hydrate($options)->$method($means->hydrate($data))->send();
    }

    /**
     * Delete the current object in the API.
     *
     * @see self::refund()
     * @return void No return possible
     * @throws Stancer\Exceptions\BadMethodCallException On every call, this method is not allowed in this context.
     *
     * @phpstan-return $this
     */
    #[Override]
    public function delete(): static
    {
        $message = 'You are not allowed to delete a payment, you need to refund it instead.';

        throw new Stancer\Exceptions\BadMethodCallException($message);
    }

    /**
     * Filter for list method.
     *
     * `$terms` must be an associative array with one of the following key : `order_id`, `unique_id`.
     *
     * `order_id` and `unique_id` will be treated as a string and will filter payments corresponding to the data
     * you specified in your initial payment request.
     *
     * @param array $terms Search terms. May have `order_id` or `unique_id` key.
     * @return array
     * @throws Stancer\Exceptions\InvalidSearchOrderIdFilterException When `order_id` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchUniqueIdFilterException When `unique_id` is invalid.
     *
     * @phpstan-param array{order_id?: string, unique_id?: string} $terms
     * @phpstan-return array{order_id?: string, unique_id?: string}
     */
    public static function filterListParams(array $terms): array
    {
        $params = [];

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

        if (array_key_exists('unique_id', $terms)) {
            $params['unique_id'] = $terms['unique_id'];
            $type = gettype($terms['unique_id']);

            if ($type !== 'string') {
                throw new Stancer\Exceptions\InvalidSearchUniqueIdFilterException('Unique ID must be a string.');
            }

            if (strlen($terms['unique_id']) > 36 || !$terms['unique_id']) {
                $message = 'A valid unique ID must be between 1 and 36 characters.';

                throw new Stancer\Exceptions\InvalidSearchUniqueIdFilterException($message);
            }
        }

        return $params;
    }

    // phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber

    /**
     * Return the URL for Stancer payment page.
     *
     * Maybe used as an iframe or a redirection page if you needed it.
     *
     * @param array $params Parameters to add to the URL.
     * @param boolean $force Get the payment page url even without return URL.
     * @return string
     * @throws Stancer\Exceptions\MissingApiKeyException When no public key was given in configuration.
     * @throws Stancer\Exceptions\MissingReturnUrlException When no return URL was given to payment data.
     * @throws Stancer\Exceptions\MissingPaymentIdException When no payment has no ID.
     *
     * @phpstan-param array{lang?: string} $params Parameters to add to the URL.
     */
    public function getPaymentPageUrl(array $params = [], bool $force = false): string
    {
        $config = Stancer\Config::getGlobal();

        $data = [
            str_replace('api', 'payment', $config->getHost()),
            $config->getPublicKey(),
        ];

        if (!$this->getReturnUrl() && !$force) {
            $message = 'You must provide a return URL before asking for the payment page.';

            throw new Stancer\Exceptions\MissingReturnUrlException($message);
        }

        if (!$this->getId()) {
            $message = 'A payment ID is mandatory to obtain a payment page URL. Maybe you forgot to send the payment.';

            throw new Stancer\Exceptions\MissingPaymentIdException($message);
        }

        $data[] = $this->getId();

        $params = array_intersect_key($params, ['lang' => 1]);
        $query = http_build_query($params);

        if ($query) {
            $query = '?' . $query;
        }

        return vsprintf('https://%s/%s/%s', $data) . $query;
    }

    // phpcs:enable

    /**
     * Return the refundable amount.
     *
     * @return integer
     */
    public function getRefundableAmount(): int
    {
        return ($this->getAmount() ?? 0) - $this->getRefundedAmount();
    }

    /**
     * Return the already refunded amount.
     *
     * @return integer
     */
    public function getRefundedAmount(): int
    {
        $getAmounts = function ($refund) {
            return $refund->getAmount();
        };

        $refunds = $this->getRefunds();
        $refunded = array_map($getAmounts, $refunds);

        return intval(array_sum($refunded));
    }

    /**
     * Indicates if payment is an error.
     *
     * @return boolean
     */
    public function isError(): bool
    {
        if (is_null($this->getStatus())) {
            return false;
        }

        if ($this->getCapture() === false && $this->getStatus() === Stancer\Payment\Status::AUTHORIZED) {
            return false;
        }

        $allowed = [
            Stancer\Payment\Status::CAPTURE_SENT,
            Stancer\Payment\Status::CAPTURED,
            Stancer\Payment\Status::TO_CAPTURE,
        ];

        return !in_array($this->getStatus(), $allowed, true);
    }

    /**
     * Indicates if payment is not an error.
     *
     * @return boolean
     */
    public function isNotError(): bool
    {
        return !$this->isError();
    }

    /**
     * Indicates if payment is not a success.
     *
     * @return boolean
     */
    public function isNotSuccess(): bool
    {
        return !$this->isSuccess();
    }

    /**
     * Indicates if payment is a success.
     *
     * @return boolean
     */
    public function isSuccess(): bool
    {
        if ($this->getCapture() === false && $this->getStatus() === Stancer\Payment\Status::AUTHORIZED) {
            return true;
        }

        $allowed = [
            Stancer\Payment\Status::CAPTURE_SENT,
            Stancer\Payment\Status::CAPTURED,
            Stancer\Payment\Status::TO_CAPTURE,
        ];

        return in_array($this->getStatus(), $allowed, true);
    }

    /**
     * Quick way to make a simple payment.
     *
     * @param integer $amount Amount.
     * @param string $currency Currency.
     * @param Stancer\Interfaces\PaymentMeansInterface $means Payment means.
     * @return $this
     */
    public function pay(int $amount, string $currency, Stancer\Interfaces\PaymentMeansInterface $means): static
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated', E_USER_DEPRECATED);

        if ($means instanceof Card) {
            $this->setCard($means);
        }

        if ($means instanceof Sepa) {
            $this->setSepa($means);
        }

        return $this->setAmount($amount)->setCurrency($currency)->send();
    }

    /**
     * Refund a payment, or part of it.
     *
     * @param integer|null $amount Amount to refund, if not present all paid amount will be refund.
     * @return $this
     * @throws Stancer\Exceptions\InvalidAmountException When trying to refund more than paid.
     * @throws Stancer\Exceptions\InvalidAmountException When the amount is invalid.
     * @throws Stancer\Exceptions\MissingPaymentIdException When the payment has no ID.
     */
    public function refund(?int $amount = null): static
    {
        if (!$this->getId()) {
            throw new Stancer\Exceptions\MissingPaymentIdException();
        }

        $refund = new Refund();
        $refund->hydrate(['payment' => $this]);

        if ($amount) {
            $currency = $this->getCurrency();
            $params = [
                $amount / 100,
                $currency ? strtoupper($currency->value) : '',
                ($this->getAmount() ?? 0) / 100,
                $this->getRefundedAmount() / 100,
            ];
            $message = '';

            if ($amount < 50) {
                $message = 'Amount must be greater than or equal to 50.';
            } elseif ($amount > $this->getAmount()) {
                $message = 'You are trying to refund (%1$.02f %2$s) more than paid';
                $message .= ' (%3$.02f %2$s).';
            } elseif ($amount > $this->getRefundableAmount()) {
                $message = 'You are trying to refund (%1$.02f %2$s) more than paid';
                $message .= ' (%3$.02f %2$s with %4$.02f %2$s already refunded).';
            }

            if ($message) {
                throw new Stancer\Exceptions\InvalidAmountException(vsprintf($message, $params));
            }

            $refund->hydrate(['amount' => $amount]);
        }

        $modified = $this->modified;

        $this->addRefunds($refund->send());
        $this->modified = $modified;

        $params = [
            $refund->getAmount() / 100,
            strtoupper($refund->getCurrency()),
            $this->getId(),
        ];
        $message = vsprintf('Refund of %.02f %s on payment "%s"', $params);

        Stancer\Config::getGlobal()->getLogger()->info($message);

        if ($refund->getStatus() !== Stancer\Refund\Status::TO_REFUND) {
            $this->populated = false;
            $this->populate();

            foreach ($this->getRefunds() as $ref) {
                $ref->hydrate(['payment' => $this])->modified = [];
            }
        }

        return $this;
    }

    /**
     * Send the current object.
     *
     * @uses Request::post()
     * @return $this
     * @throws Stancer\Exceptions\InvalidAmountException When no amount was given.
     * @throws Stancer\Exceptions\InvalidCurrencyException When no currency was given.
     * @throws Stancer\Exceptions\InvalidIpAddressException When no device was already given, authenticated payment
     *   was asked and an error occur during device creation.
     * @throws Stancer\Exceptions\InvalidPortException When no device was already given, authenticated payment
     *   was asked and an error occur during device creation.
     * @throws Stancer\Exceptions\InvalidExpirationException When card's expiration is invalid.
     */
    #[Override]
    public function send(): static
    {
        if ($this->getId() && $this->isModified()) {
            return parent::send();
        }

        $amount = $this->getAmount();
        $currency = $this->getCurrency();

        if (is_null($amount)) {
            throw new Stancer\Exceptions\InvalidAmountException();
        }

        if (!$currency) {
            throw new Stancer\Exceptions\InvalidCurrencyException();
        }

        if ($amount === 0 && $this->getCapture() !== false) {
            throw new Stancer\Exceptions\InvalidAmountException();
        }

        $auth = $this->getAuth();
        $card = $this->getCard();
        $sepa = $this->getSepa();

        if (!is_object($this->getDevice())) {
            // phpcs:disable Squiz.PHP.DisallowBooleanStatement.Found
            $mandatoryDevice = is_object($auth) && $auth->getReturnUrl();
            // phpcs:enable

            try {
                $device = new Stancer\Device();
                $this->setDevice($device->hydrateFromEnvironment());
            } catch (Stancer\Exceptions\InvalidIpAddressException $exception) {
                if ($mandatoryDevice) {
                    throw $exception;
                }
            } catch (Stancer\Exceptions\InvalidPortException $exception) {
                if ($mandatoryDevice) {
                    throw $exception;
                }
            }
        }

        if ($card && !$card->getId()) {
            $expiration = $card->getExpirationDate();
            $now = new DateTime();

            if ($expiration < $now) {
                throw new Stancer\Exceptions\InvalidExpirationException('Card expiration is invalid.');
            }
        }

        parent::send();

        $params = [
            // @phpstan-ignore-next-line amount is defined or an exception should be thrown before
            $this->getAmount() / 100,
            // @phpstan-ignore-next-line same for currency
            $this->getCurrency()->value,
        ];
        $message = vsprintf('Payment of %.02f %s without payment method', $params);

        if ($card) {
            $params[] = $card->getBrand();
            $params[] = $card->getLast4();
            $message = vsprintf('Payment of %.02f %s with %s "%s"', $params);
        }

        if ($sepa) {
            $params[] = $sepa->getLast4();
            $params[] = $sepa->getBic();
            $message = vsprintf('Payment of %.02f %s with IBAN "%s" / BIC "%s"', $params);
        }

        Stancer\Config::getGlobal()->getLogger()->info($message);

        return $this;
    }

    /**
     * Set an authenticated payment.
     *
     * You supposed to give an `Stancer\Auth` object to start an authenticated payment.
     * To simplify your workflow, we allow you to pass directly the return URL used in authenticated payment.
     *
     * If you are using our payment page, you can simpliy pass a boolean to acitvate an authenticated payment,
     * we will manage everything else for you.
     *
     * @param Stancer\Auth|string|boolean $auth Authentication data.
     * @return $this
     */
    public function setAuth(Stancer\Auth|string|bool $auth): static
    {
        if ($auth === false) {
            return $this;
        }

        $obj = $auth;

        if (is_string($auth)) {
            $obj = new Stancer\Auth(['returnUrl' => $auth]);
        }

        if (is_bool($auth)) {
            $obj = new Stancer\Auth();
        }

        return parent::setAuth($obj);
    }

    /**
     * Set a card.
     *
     * @param Stancer\Card $card New card instance.
     * @return $this
     */
    public function setCard(Card $card): static
    {
        parent::setCard($card);
        $this->dataModel['method']['value'] = 'card';

        return $this;
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
            $message = vsprintf('"%s" is not a valid currency, please use one of the following : %s', $params);

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
                $message = vsprintf('"%s" is not a valid method, please use one of the following : %s', $params);

                throw new Stancer\Exceptions\InvalidArgumentException($message, previous: $exception);
            }
        }

        $currency = $this->getCurrency();
        $method = $this->getMethod();

        if (
            !$method
            && $currency
            && in_array(Stancer\Payment\MethodsAllowed::SEPA, $new, true)
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
    public function setReturnUrl(string $url): static
    {
        if (strpos($url, 'https://') !== 0) {
            throw new Stancer\Exceptions\InvalidUrlException('You must provide an HTTPS URL.');
        }

        return parent::setReturnUrl($url);
    }

    /**
     * Ask for a new status.
     *
     * @param Stancer\Payment\Status|string $status New status value.
     * @return $this
     * @throws Stancer\Exceptions\BadMethodCallException If the value is passed by string
     *      and it does not match a valid status.
     */
    public function setStatus(Stancer\Payment\Status|string $status): static
    {
        try {
            if (is_string($status)) {
                $new = Stancer\Payment\Status::from(strtolower($status));
            } else {
                $new = $status;
            }
        } catch (ValueError $exception) {
            $message = 'You only can set `AUTHORIZE`, to ask for an authorization, or `CAPTURE`, to ask for a capture.';

            throw new Stancer\Exceptions\BadMethodCallException($message, previous: $exception);
        }

        return parent::setStatus($new);
    }

    /**
     * Set a sepa account.
     *
     * @param Stancer\Sepa $sepa New sepa instance.
     * @return $this
     */
    public function setSepa(Sepa $sepa): static
    {
        parent::setSepa($sepa);
        $this->dataModel['method']['value'] = 'sepa';

        return $this;
    }
}
