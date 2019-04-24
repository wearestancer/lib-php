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
 * @method ild78\\Card getCard()
 * @method string getCountry()
 * @method string getCurrency()
 * @method string|null getDescription()
 * @method integer|null getId_customer()
 * @method string getMethod()
 * @method integer getOrder_id()
 * @method string getResponse()
 * @method ild78\\Sepa getSepa()
 * @method string getStatus()
 */
class Payment extends Api\AbstractObject
{
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
            'required' => true,
            'type' => self::STRING,
        ],
        'description' => [
            'size' => [
                'min' => 3,
                'max' => 64,
            ],
            'type' => self::STRING,
        ],
        'customer' => [
            'type' => ild78\Customer::class,
        ],
        'method' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
        'orderId' => [
            'size' => [
                'min' => 1,
                'max' => 24,
            ],
            'type' => self::STRING,
        ],
        'responseCode' => [
            'restricted' => true,
            'size' => [
                'fixed' => 2,
            ],
            'type' => self::STRING,
        ],
        'status' => [
            'restricted' => true,
            'type' => self::STRING,
        ],
        'sepa' => [
            'type' => ild78\Sepa::class,
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
    public static function charge(array $options) : self
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

        return $obj->hydrate($options)->$method($means->hydrate($data))->save();
    }

    /**
     * Delete the current object in the API
     *
     * @see self::refund()
     * @return void No return possible
     * @throws ild78\Exceptions\BadMethodCallException On every call, this method is not allowed in this context.
     */
    public function delete() : ild78\Api\AbstractObject
    {
        $message = 'You are not allowed to delete a payment, you need to refund it instead.';

        throw new ild78\Exceptions\BadMethodCallException($message);
    }

    /**
     * Get a readable message of response code
     *
     * @return string
     */
    public function getResponseMessage() : string
    {
        $messages = [
            '00' => 'OK',
            '05' => 'Do not honor',
            '41' => 'Lost card',
            '42' => 'Stolen card',
            '51' => 'Insufficient funds',
        ];

        $code = $this->getResponseCode();

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
    public function isNotSuccess() : bool
    {
        return !$this->isSuccess();
    }

    /**
     * Indicates if payment is a success or not
     *
     * @return boolean
     */
    public function isSuccess() : bool
    {
        return $this->getResponseCode() === '00';
    }

    /**
     * List payment
     *
     * `$terms` must be an associative array with one of the following key : `created`, `limit`, `order_id` or `start`.
     *
     * `created` must be an unix timestamp or a DateTime object which will filter payments equal
     * to or greater than this value.
     *
     * `limit` must be an integer between 1 and 100 and will limit the number of objects to be returned.
     *
     * `order_id` will be treated as a string, will filter payments corresponding to the `order_id` you specified
     * in your initial payment request.
     *
     * `start` must be an integer, will be used as a pagination cursor, starts at 0.
     *
     * @param array $terms Search terms. May have `created`, `limit`, `order_id` or `start` key.
     * @return Generator
     * @throws ild78\Exceptions\InvalidSearchFilter When `$terms` is invalid.
     * @throws ild78\Exceptions\InvalidSearchCreationFilter When `created` is invalid.
     * @throws ild78\Exceptions\InvalidSearchOrderIdFilter When `order_id` is invalid.
     * @throws ild78\Exceptions\InvalidSearchLimit When `limit` is invalid.
     * @throws ild78\Exceptions\InvalidSearchStart When `start` is invalid.
     */
    public static function list(array $terms) : Generator
    {
        $allowed = array_flip(['created', 'limit', 'order_id', 'start']);
        $diff = array_intersect_key($terms, $allowed);
        $params = [];

        if (!$diff) {
            throw new ild78\Exceptions\InvalidSearchFilter();
        }

        if (array_key_exists('created', $terms)) {
            $created = $terms['created'];

            if ($terms['created'] instanceof DateTime) {
                $created = (int) $terms['created']->format('U');
            }

            $params['created'] = $created;

            $type = gettype($created);

            if (!$created || $type !== 'integer') {
                $message = 'Created must be a position integer or a DateTime object.';

                throw new ild78\Exceptions\InvalidSearchCreationFilter($message);
            }

            if ($created > time()) {
                $message = 'Created must be in the past.';

                throw new ild78\Exceptions\InvalidSearchCreationFilter($message);
            }
        }

        if (array_key_exists('limit', $terms)) {
            $params['limit'] = $terms['limit'];
            $type = gettype($terms['limit']);

            if ($type !== 'integer' || $terms['limit'] < 1 || $terms['limit'] > 100) {
                throw new ild78\Exceptions\InvalidSearchLimit();
            }
        }

        if (array_key_exists('order_id', $terms)) {
            $params['order_id'] = $terms['order_id'];
            $type = gettype($terms['order_id']);

            if (!$terms['order_id'] || $type !== 'string') {
                throw new ild78\Exceptions\InvalidSearchOrderIdFilter();
            }
        }

        $params['start'] = 0;

        if (array_key_exists('start', $terms)) {
            $params['start'] = $terms['start'];
            $type = gettype($terms['start']);

            if ($type !== 'integer' || $terms['start'] < 0) {
                throw new ild78\Exceptions\InvalidSearchStart();
            }
        }

        $obj = new static(); // Mandatory for requests.
        $request = new Api\Request();

        $gen = function () use ($obj, $request, $params) {
            $more = true;
            $start = 0;

            do {
                $params['start'] += $start;

                $tmp = $request->get($obj, $params);

                if (!$tmp) {
                    $more = false;
                } else {
                    $results = json_decode($tmp, true);
                    $more = $results['range']['has_more'];
                    $start += $results['range']['limit'];

                    foreach ($results['payments'] as $data) {
                        $payment = new static($data['id']);

                        yield $payment->hydrate($data);
                    }
                }
            } while ($more);
        };

        return $gen();
    }

    /**
     * Quick way to make a simple payment
     *
     * @param integer $amount Amount.
     * @param string $currency Currency.
     * @param ild78\Interfaces\PaymentMeansInterface $means Payment means.
     * @return self
     */
    public function pay(int $amount, string $currency, ild78\Interfaces\PaymentMeansInterface $means) : self
    {
        if ($means instanceof Card) {
            $this->setCard($means);
        }

        if ($means instanceof Sepa) {
            $this->setSepa($means);
        }

        return $this->setAmount($amount)->setCurrency($currency)->save();
    }

    /**
     * Save the current object.
     *
     * @uses Request::post()
     * @return self
     * @throws ild78\Exceptions\MissingPaymentMethodException When trying to pay something without any
     *   credit card or SEPA account.
     */
    public function save() : ild78\Api\AbstractObject
    {
        $card = $this->getCard();
        $sepa = $this->getSepa();

        if (!$card && !$sepa) {
            $message = 'You must provide a valid credit card or SEPA account to make a payment.';

            throw new ild78\Exceptions\MissingPaymentMethodException($message);
        }

        parent::save();

        $params = [
            $this->getAmount() / 100,
            $this->getCurrency(),
        ];

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

        Api\Config::getGlobal()->getLogger()->info($message);

        return $this;
    }

    /**
     * Update amount
     *
     * @param integer $amount New amount.
     * @return self
     * @throws ild78\Exceptions\InvalidAmountException When the amount is invalid.
     */
    public function setAmount(int $amount) : self
    {
        try {
            return parent::setAmount($amount);
        } catch (ild78\Exceptions\InvalidArgumentException $excep) {
            throw new ild78\Exceptions\InvalidAmountException($excep->getMessage(), $excep->getCode(), $excep);
        }
    }

    /**
     * Set the currency.
     *
     * @param string $currency The currency, must one in the following : EUR, USD, GBP.
     * @return self
     * @throws ild78\Exceptions\InvalidCurrencyException When currency is not EUR, USD or GBP.
     */
    public function setCurrency(string $currency) : self
    {
        $cur = strtolower($currency);

        $valid = [
            'eur',
            'usd',
            'gbp',
        ];

        if (!in_array($cur, $valid, true)) {
            $params = [
                $currency,
                strtoupper(implode(', ', $valid)),
            ];
            $message = vsprintf('"%s" is not a valid currency, please use one of the following : %s', $params);

            throw new ild78\Exceptions\InvalidCurrencyException($message);
        }

        $this->dataModel['currency']['value'] = $cur;

        return $this;
    }

    /**
     * Update description
     *
     * @param string $description New description.
     * @return self
     * @throws ild78\Exceptions\InvalidDescriptionException When the description is invalid.
     */
    public function setDescription(string $description) : self
    {
        try {
            return parent::setDescription($description);
        } catch (ild78\Exceptions\InvalidArgumentException $excep) {
            throw new ild78\Exceptions\InvalidDescriptionException($excep->getMessage(), $excep->getCode(), $excep);
        }
    }

    /**
     * Update order ID
     *
     * @param string $orderId New order ID.
     * @return self
     * @throws ild78\Exceptions\InvalidOrderIdException When the order ID is invalid.
     */
    public function setOrderId(string $orderId) : self
    {
        try {
            return parent::setOrderId($orderId);
        } catch (ild78\Exceptions\InvalidArgumentException $excep) {
            throw new ild78\Exceptions\InvalidOrderIdException($excep->getMessage(), $excep->getCode(), $excep);
        }
    }
}
