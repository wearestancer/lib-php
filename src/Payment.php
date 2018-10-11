<?php
declare(strict_types=1);

namespace ild78;

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
class Payment extends Api\Object
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
        'sepa' => [
            'type' => ild78\Sepa::class,
        ],
    ];

    /**
     * Save the current object.
     *
     * @uses Request::post()
     * @return self
     */
    public function save() : Api\Object
    {
        parent::save();

        $params = [
            $this->getAmount() / 100,
            $this->getCurrency(),
        ];

        $card = $this->getCard();
        $sepa = $this->getSepa();

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
     * Set the currency.
     *
     * @param string $currency The currency, must one in the following : EUR, USD, GBP.
     * @return self
     * @throws ild78\Exceptions\InvalidArgumentException When currency is not EUR, USD or GBP.
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

            throw new ild78\Exceptions\InvalidArgumentException($message);
        }

        $this->dataModel['currency']['value'] = $cur;

        return $this;
    }
}
