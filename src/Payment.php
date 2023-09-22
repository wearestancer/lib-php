<?php
declare(strict_types=1);

namespace Stancer;

use DateTime;
use DateTimeImmutable;
use Stancer;

/**
 * Representation of a payment.
 *
 * @method integer|null getAmount()
 * @method Stancer\Auth|null getAuth()
 * @method boolean getCapture()
 * @method Stancer\Card|null getCard()
 * @method string|null getCountry()
 * @method string getCurrency()
 * @method Stancer\Customer|null getCustomer()
 * @method DateTimeImmutable|null getCreated()
 * @method DateTimeImmutable|null getCreationDate()
 * @method DateTimeImmutable|null getDateBank()
 * @method string|null getDescription()
 * @method Stancer\Device|null getDevice()
 * @method string|null getMethod()
 * @method string[]|null getMethodsAllowed()
 * @method string|null getOrderId()
 * @method Stancer\Refund[] getRefunds()
 * @method string|null getResponse()
 * @method string|null getReturnUrl()
 * @method Stancer\Sepa|null getSepa()
 * @method string|null getStatus()
 * @method string|null getUniqueId()
 *
 * @method static Generator<static> list(SearchFilters $terms)
 *
 * @method $this setAmount(integer $amount)
 * @method $this setCapture(boolean $capture)
 * @method $this setCountry(string $country)
 * @method $this setCustomer(Stancer\Customer $customer)
 * @method $this setDescription(string $description)
 * @method $this setDevice(Stancer\Device $device)
 * @method $this setMethodsAllowed(string[] $methods)
 * @method $this setOrderId(string $orderId)
 * @method $this setStatus(string $status)
 * @method $this setUniqueId(string $uniqueId)
 *
 * @property integer|null $amount
 * @property Stancer\Auth|null $auth
 * @property boolean $capture
 * @property Stancer\Card|null $card
 * @property string $currency
 * @property Stancer\Customer|null $customer
 * @property string|null $description
 * @property Stancer\Device|null $device
 * @property string[]|null $methodsAllowed
 * @property string|null $orderId
 * @property string|null $returnUrl
 * @property Stancer\Sepa|null $sepa
 * @property string|null $status
 * @property string|null $uniqueId
 *
 * @property-read string|null $country
 * @property-read DateTimeImmutable|null $creationDate
 * @property-read DateTimeImmutable|null $created
 * @property-read DateTimeImmutable|null $dateBank
 * @property-read string|null $method
 * @property-read Stancer\Refund[] $refunds
 * @property-read string $response
 *
 * @phpstan-method $this addRefunds(Stancer\Refund $refund)
 */
class Payment extends Stancer\Core\AbstractObject
{
    use Stancer\Traits\AmountTrait;
    use Stancer\Traits\SearchTrait;

    /** @var string */
    protected $endpoint = 'checkout';

    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected $dataModel = [
        'amount' => [
            'required' => true,
            'type' => self::INTEGER,
        ],
        'auth' => [
            'type' => Stancer\Auth::class,
        ],
        'capture' => [
            'type' => self::BOOLEAN,
        ],
        'card' => [
            'type' => Stancer\Card::class,
        ],
        'country' => [
            'type' => self::STRING,
        ],
        'currency' => [
            'allowedValues' => [
                'aud',
                'cad',
                'chf',
                'dkk',
                'eur',
                'gbp',
                'jpy',
                'nok',
                'pln',
                'sek',
                'usd',
            ],
            'coerce' => Stancer\Core\Type\Helper::TO_LOWER,
            'required' => true,
            'type' => self::STRING,
        ],
        'customer' => [
            'type' => Stancer\Customer::class,
        ],
        'dateBank' => [
            'restricted' => true,
            'type' => DateTimeImmutable::class,
        ],
        'description' => [
            'size' => [
                'min' => 3,
                'max' => 64,
            ],
            'type' => self::STRING,
        ],
        'device' => [
            'type' => Stancer\Device::class,
        ],
        'method' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
        'methodsAllowed' => [
            'allowedValues' => [
                'card',
                'sepa',
            ],
            'list' => true,
            'type' => self::STRING,
        ],
        'orderId' => [
            'size' => [
                'min' => 1,
                'max' => 36,
            ],
            'type' => self::STRING,
        ],
        'refunds' => [
            'exportable' => false,
            'list' => true,
            'type' => Stancer\Refund::class,
        ],
        'response' => [
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
            'size' => [
                'min' => 1,
                'max' => 2048,
            ],
            'type' => self::STRING,
        ],
        'sepa' => [
            'type' => Stancer\Sepa::class,
        ],
        'status' => [
            'type' => self::STRING,
        ],
        'uniqueId' => [
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
     * @param string $method New method.
     * @return self
     * @throws Stancer\Exceptions\InvalidArgumentException When currency is EUR and trying to set "sepa" method.
     */
    public function addMethodsAllowed(string $method): self
    {
        $currency = $this->getCurrency();

        if ($currency && $method && $method === 'sepa' && $currency !== 'eur') {
            $message = sprintf('You can not use "%s" method with "%s" currency.', $method, $currency);

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
     * @return self
     *
     * @phpstan-param PaymentChargeOptions $options
     */
    public static function charge(array $options): self
    {
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
    public function delete(): Stancer\Core\AbstractObject
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
     * @return string
     * @throws Stancer\Exceptions\MissingApiKeyException When no public key was given in configuration.
     * @throws Stancer\Exceptions\MissingReturnUrlException When no return URL was given to payment data.
     * @throws Stancer\Exceptions\MissingPaymentIdException When no payment has no ID.
     *
     * @phpstan-param array{lang?: string} $params Parameters to add to the URL.
     */
    public function getPaymentPageUrl(array $params = []): string
    {
        $config = Stancer\Config::getGlobal();

        $data = [
            str_replace('api', 'payment', $config->getHost()),
            $config->getPublicKey(),
        ];

        if (!$this->getReturnUrl()) {
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
     * @return self
     */
    public function pay(int $amount, string $currency, Stancer\Interfaces\PaymentMeansInterface $means): self
    {
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
    public function refund(int $amount = null): self
    {
        if (!$this->getId()) {
            throw new Stancer\Exceptions\MissingPaymentIdException();
        }

        $refund = new Refund();
        $refund->setPayment($this);

        if ($amount) {
            $params = [
                $amount / 100,
                strtoupper($this->getCurrency()),
                // @phpstan-ignore-next-line Current object has an ID so it had been sent to the API and has an amount
                $this->getAmount() / 100,
                $this->getRefundedAmount() / 100,
            ];
            $message = '';

            if ($amount > $this->getRefundableAmount()) {
                $message = 'You are trying to refund (%1$.02f %2$s) more than paid';
                $message .= ' (%3$.02f %2$s with %4$.02f %2$s already refunded).';
            }

            if ($amount > $this->getAmount()) {
                $message = 'You are trying to refund (%1$.02f %2$s) more than paid';
                $message .= ' (%3$.02f %2$s).';
            }

            if ($message) {
                throw new Stancer\Exceptions\InvalidAmountException(vsprintf($message, $params));
            }

            $refund->setAmount($amount);
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
                $ref->setPayment($this);
                $ref->modified = [];
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
    public function send(): Stancer\Core\AbstractObject
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
            $this->getCurrency(),
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
    public function setAuth($auth): self
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
    public function setCard(Card $card): self
    {
        parent::setCard($card);
        $this->dataModel['method']['value'] = 'card';

        return $this;
    }

    /**
     * Set the currency.
     *
     * @param string $currency The currency.
     * @return self
     * @throws Stancer\Exceptions\InvalidCurrencyException When currency is EUR and "sepa" is already allowed.
     */
    public function setCurrency(string $currency): self
    {
        $method = $this->getMethod();
        $methods = $this->getMethodsAllowed();

        if (!$method && $currency && $methods && in_array('sepa', $methods, true) && strtolower($currency) !== 'eur') {
            $message = sprintf('You can not use "%s" currency with "%s" method.', strtolower($currency), 'sepa');

            throw new Stancer\Exceptions\InvalidCurrencyException($message);
        }

        return parent::setCurrency($currency);
    }

    /**
     * Set allowed methods.
     *
     * @param string[] $methods New methods.
     * @return self
     * @throws Stancer\Exceptions\InvalidArgumentException When currency is EUR and trying to set "sepa" method.
     */
    public function setMethodsAllowed(array $methods): self
    {
        $currency = $this->getCurrency();
        $method = $this->getMethod();

        if (!$method && $currency && $methods && in_array('sepa', $methods, true) && $currency !== 'eur') {
            $message = sprintf('You can not use "%s" method with "%s" currency.', 'sepa', $currency);

            throw new Stancer\Exceptions\InvalidArgumentException($message);
        }

        return parent::setMethodsAllowed($methods);
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

    /**
     * Set a sepa account.
     *
     * @param Stancer\Sepa $sepa New sepa instance.
     * @return $this
     */
    public function setSepa(Sepa $sepa): self
    {
        parent::setSepa($sepa);
        $this->dataModel['method']['value'] = 'sepa';

        return $this;
    }
}
