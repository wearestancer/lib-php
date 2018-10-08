<?php
declare(strict_types=1);

namespace ild78;

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

    /** @var string */
    protected $status;

    public function hydrateCard(array $data) : self
    {
        if (!$this->card) {
            $this->card = new Card;
        }

        $this->card->hydrate($data);

        return $this;
    }
}
