<?php
declare(strict_types=1);

namespace ild78;

use DateTime;
use Generator;
use ild78;

/**
 * Representation of a payment
 *
 * @method integer getAmount()
 * @method ild78\\Auth getAuth()
 * @method boolean getCapture()
 * @method ild78\\Card getCard()
 * @method string|null getCountry()
 * @method string getCurrency()
 * @method ild78\\Customer getCustomer()
 * @method string getDateBank()
 * @method string|null getDescription()
 * @method ild78\\Device getDevice()
 * @method string|null getMethod()
 * @method string|null getOrderId()
 * @method ild78\\Refund[] getRefunds()
 * @method string getResponse()
 * @method string|null getReturnUrl()
 * @method ild78\\Sepa getSepa()
 * @method string|null getStatus()
 * @method string|null getUniqueId()
 *
 * @method Generator list(array $terms)
 *
 * @method self setAmount(integer $amount)
 * @method self setCapture(boolean $capture)
 * @method self setCountry(string $country)
 * @method self setCustomer(ild78\\Customer $customer)
 * @method self setDescription(string $description)
 * @method self setDevice(ild78\\Device $device)
 * @method self setOrderId(string $orderId)
 * @method self setStatus(string $status)
 * @method self setUniqueId(string $uniqueId)
 *
 * @property integer $amount
 * @property ild78\\Auth $auth
 * @property boolean $capture
 * @property ild78\\Card $card
 * @property string|null $country
 * @property DateTime|null $created
 * @property string $currency
 * @property ild78\\Customer $customer
 * @property string $dateBank
 * @property string|null $description
 * @property ild78\\Device $device
 * @property string|null $method
 * @property string|null $orderId
 * @property ild78\\Refund[] $refunds
 * @property string $response
 * @property string|null $returnUrl
 * @property ild78\\Sepa $sepa
 * @property string|null $status
 * @property string|null $uniqueId
 */
class Payment extends ild78\Core\AbstractObject
{
    use ild78\Traits\AmountTrait;
    use ild78\Traits\SearchTrait;

    /** @var string */
    protected $endpoint = 'checkout';

    /** @var array */
    protected $dataModel = [
        'amount' => [
            'required' => true,
            'size' => [
                'min' => 50,
            ],
            'type' => self::INTEGER,
        ],
        'auth' => [
            'type' => ild78\Auth::class,
        ],
        'capture' => [
            'type' => self::BOOLEAN,
        ],
        'card' => [
            'type' => ild78\Card::class,
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
            'coerce' => 'strtolower',
            'required' => true,
            'type' => self::STRING,
        ],
        'customer' => [
            'type' => ild78\Customer::class,
        ],
        'dateBank' => [
            'restricted' => true,
            'type' => DateTime::class,
        ],
        'description' => [
            'size' => [
                'min' => 3,
                'max' => 64,
            ],
            'type' => self::STRING,
        ],
        'device' => [
            'type' => ild78\Device::class,
        ],
        'method' => [
            'restricted' => true,
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
            'type' => ild78\Refund::class,
        ],
        'response' => [
            'restricted' => true,
            'size' => [
                'fixed' => 2,
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
            'type' => ild78\Sepa::class,
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
     * Charge a card or a bank account.
     *
     * This method is Stripe compatible.
     *
     * @param array $options Charge options.
     * @return self
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

        if (array_key_exists('account_holder_name', $data)) {
            $data['name'] = $data['account_holder_name'];
        }

        if (array_key_exists('account_number', $data)) {
            $data['iban'] = $data['account_number'];
            $class = Sepa::class;
            $method = 'setSepa';
        }

        $means = new $class($id);

        return $obj->hydrate($options)->$method($means->hydrate($data))->send();
    }

    /**
     * Delete the current object in the API
     *
     * @see self::refund()
     * @return void No return possible
     * @throws ild78\Exceptions\BadMethodCallException On every call, this method is not allowed in this context.
     */
    public function delete(): ild78\Core\AbstractObject
    {
        $message = 'You are not allowed to delete a payment, you need to refund it instead.';

        throw new ild78\Exceptions\BadMethodCallException($message);
    }

    /**
     * Filter for list method
     *
     * `$terms` must be an associative array with one of the following key : `order_id`, `unique_id`.
     *
     * `order_id` and `unique_id` will be treated as a string and will filter payments corresponding to the data
     * you specified in your initial payment request.
     *
     * @param array $terms Search terms. May have `order_id` or `unique_id` key.
     * @return array
     * @throws ild78\Exceptions\InvalidSearchOrderIdFilterException When `order_id` is invalid.
     * @throws ild78\Exceptions\InvalidSearchUniqueIdFilterException When `unique_id` is invalid.
     */
    public static function filterListParams(array $terms): array
    {
        $params = [];

        if (array_key_exists('order_id', $terms)) {
            $params['order_id'] = $terms['order_id'];
            $type = gettype($terms['order_id']);

            if ($type !== 'string') {
                throw new ild78\Exceptions\InvalidSearchOrderIdFilterException('Order ID must be a string.');
            }

            if (strlen($terms['order_id']) > 36 || !$terms['order_id']) {
                $message = 'A valid order ID must be between 1 and 36 characters.';

                throw new ild78\Exceptions\InvalidSearchOrderIdFilterException($message);
            }
        }

        if (array_key_exists('unique_id', $terms)) {
            $params['unique_id'] = $terms['unique_id'];
            $type = gettype($terms['unique_id']);

            if ($type !== 'string') {
                throw new ild78\Exceptions\InvalidSearchUniqueIdFilterException('Unique ID must be a string.');
            }

            if (strlen($terms['unique_id']) > 36 || !$terms['unique_id']) {
                $message = 'A valid unique ID must be between 1 and 36 characters.';

                throw new ild78\Exceptions\InvalidSearchUniqueIdFilterException($message);
            }
        }

        return $params;
    }

    // phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber

    /**
     * Return the URL for Iliad payment page.
     *
     * Maybe used as an iframe or a redirection page if you needed it.
     *
     * @param array $params Parameters to add to the URL.
     * @return string
     * @throws ild78\Exceptions\MissingApiKeyException When no public key was given in configuration.
     * @throws ild78\Exceptions\MissingReturnUrlException When no return URL was given to payment data.
     * @throws ild78\Exceptions\MissingPaymentIdException When no payment has no ID.
     */
    public function getPaymentPageUrl(array $params = []): string
    {
        $config = ild78\Config::getGlobal();

        $data = [
            str_replace('api', 'payment', $config->getHost()),
            $config->getPublicKey(),
        ];

        if (!$this->getReturnUrl()) {
            $message = 'You must provide a return URL before asking for the payment page.';

            throw new ild78\Exceptions\MissingReturnUrlException($message);
        }

        if (!$this->getId()) {
            $message = 'A payment ID is mandatory to obtain a payment page URL. Maybe you forgot to send the payment.';

            throw new ild78\Exceptions\MissingPaymentIdException($message);
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
     * Return the refundable amount
     *
     * @return integer
     */
    public function getRefundableAmount(): int
    {
        return $this->getAmount() - $this->getRefundedAmount();
    }

    /**
     * Return the already refunded amount
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

        return array_sum($refunded);
    }

    /**
     * Get a readable message of response code
     *
     * @return string
     */
    public function getResponseMessage(): string
    {
        $messages = [
            '00' => 'OK',
            '05' => 'Do not honor',
            '41' => 'Lost card',
            '42' => 'Stolen card',
            '51' => 'Insufficient funds',
        ];

        $code = $this->getResponse();

        if (array_key_exists($code, $messages)) {
            return $messages[$code];
        }

        return 'Unknown';
    }

    /**
     * Indicates if payment is not a success
     *
     * @return boolean
     */
    public function isNotSuccess(): bool
    {
        if (is_null($this->getResponse())) {
            return false;
        }

        return !$this->isSuccess();
    }

    /**
     * Indicates if payment is a success or not
     *
     * @return boolean
     */
    public function isSuccess(): bool
    {
        return $this->getResponse() === '00';
    }

    /**
     * Quick way to make a simple payment
     *
     * @param integer $amount Amount.
     * @param string $currency Currency.
     * @param ild78\Interfaces\PaymentMeansInterface $means Payment means.
     * @return self
     */
    public function pay(int $amount, string $currency, ild78\Interfaces\PaymentMeansInterface $means): self
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
     * @return self
     * @throws ild78\Exceptions\InvalidAmountException When trying to refund more than paid.
     * @throws ild78\Exceptions\InvalidAmountException When the amount is invalid.
     * @throws ild78\Exceptions\MissingPaymentIdException When the payment has no ID.
     */
    public function refund(int $amount = null): self
    {
        if (!$this->getId()) {
            throw new ild78\Exceptions\MissingPaymentIdException();
        }

        $refund = new Refund();
        $refund->setPayment($this);

        if ($amount) {
            $params = [
                $amount / 100,
                strtoupper($this->getCurrency()),
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
                throw new ild78\Exceptions\InvalidAmountException(vsprintf($message, $params));
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

        ild78\Config::getGlobal()->getLogger()->info($message);

        if ($refund->getStatus() !== ild78\Refund\Status::TO_REFUND) {
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
     * @return self
     * @throws ild78\Exceptions\InvalidAmountException When no amount was given.
     * @throws ild78\Exceptions\InvalidCurrencyException When no currency was given.
     * @throws ild78\Exceptions\InvalidIpAddressException When no device was already given, authenticated payment
     *   was asked and an error occur during device creation.
     * @throws ild78\Exceptions\InvalidPortException When no device was already given, authenticated payment
     *   was asked and an error occur during device creation.
     * @throws ild78\Exceptions\InvalidExpirationException When card's expiration is invalid.
     */
    public function send(): ild78\Core\AbstractObject
    {
        if ($this->getId() && $this->isModified()) {
            return parent::send();
        }

        if (!$this->getAmount()) {
            throw new ild78\Exceptions\InvalidAmountException();
        }

        if (!$this->getCurrency()) {
            throw new ild78\Exceptions\InvalidCurrencyException();
        }

        $auth = $this->getAuth();
        $card = $this->getCard();
        $sepa = $this->getSepa();

        if (!is_object($this->getDevice())) {
            // phpcs:disable Squiz.PHP.DisallowBooleanStatement.Found
            $mandatoryDevice = is_object($auth) && $auth->getReturnUrl();
            // phpcs:enable

            try {
                $device = new ild78\Device();
                $this->setDevice($device->hydrateFromEnvironment());
            } catch (ild78\Exceptions\InvalidIpAddressException $exception) {
                if ($mandatoryDevice) {
                    throw $exception;
                }
            } catch (ild78\Exceptions\InvalidPortException $exception) {
                if ($mandatoryDevice) {
                    throw $exception;
                }
            }
        }

        if ($card && !$card->getId()) {
            $expiration = $card->getExpirationDate();
            $now = new DateTime();

            if ($expiration < $now) {
                throw new ild78\Exceptions\InvalidExpirationException('Card expiration is invalid.');
            }
        }

        parent::send();

        $params = [
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

        ild78\Config::getGlobal()->getLogger()->info($message);

        return $this;
    }

    /**
     * Set an authenticated payment
     *
     * You supposed to give an `ild78\Auth` object to start an authenticated payment.
     * To simplify your workflow, we allow you to pass directly the return URL used in authenticated payment.
     *
     * If you are using our payment page, you can simpliy pass a boolean to acitvate an authenticated payment,
     * we will manage everything else for you.
     *
     * @param ild78\Auth|string|boolean $auth Authentication data.
     * @return self
     */
    public function setAuth($auth): self
    {
        if ($auth === false) {
            return $this;
        }

        $obj = $auth;

        if (is_string($auth)) {
            $obj = new ild78\Auth(['returnUrl' => $auth]);
        }

        if (is_bool($auth)) {
            $obj = new ild78\Auth();
        }

        return parent::setAuth($obj);
    }

    /**
     * Set a card.
     *
     * @param ild78\Card $card New card instance.
     * @return self
     */
    public function setCard(Card $card): self
    {
        parent::setCard($card);
        $this->dataModel['method']['value'] = 'card';

        return $this;
    }

    /**
     * Update return URL
     *
     * @param string $url New HTTPS URL.
     * @return self
     * @throws ild78\Exceptions\InvalidUrlException When URL is not an HTTPS URL.
     */
    public function setReturnUrl(string $url): self
    {
        if (strpos($url, 'https://') !== 0) {
            throw new ild78\Exceptions\InvalidUrlException('You must provide an HTTPS URL.');
        }

        return parent::setReturnUrl($url);
    }

    /**
     * Set a sepa account.
     *
     * @param ild78\Sepa $sepa New sepa instance.
     * @return self
     */
    public function setSepa(Sepa $sepa): self
    {
        parent::setSepa($sepa);
        $this->dataModel['method']['value'] = 'sepa';

        return $this;
    }
}
