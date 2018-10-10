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

    /** @var integer */
    protected $amount;

    /** @var ild78\\Card */
    protected $card;

    /** @var string */
    protected $country;

    /** @var string */
    protected $currency;

    /** @var string|null */
    protected $description;

    /** @var string */
    protected $idCustomer;

    /** @var string */
    protected $method;

    /** @var string */
    protected $orderId;

    /** @var string */
    protected $response;

    /** @var ild78\\Sepa */
    protected $sepa;

    /** @var string */
    protected $status;

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
     * Set a transaction amount.
     *
     * The amount need to be in cents, aka 10€ => 1000 or $123.45 => 12345, and must be greater than or equal to 50.
     *
     * @param integer $amount The amount (in cents).
     * @return self
     * @throws ild78\Exceptions\InvalidArgumentException When the amount is less than 50.
     */
    public function setAmount(int $amount) : self
    {
        if ($amount < 50) {
            throw new ild78\Exceptions\InvalidArgumentException('Amount must be greater than or equal to 50');
        }

        $this->amount = $amount;

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

        $this->currency = $cur;

        return $this;
    }
}
