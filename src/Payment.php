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
class Payment extends Core
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

    /** @var integer|null */
    protected $id_customer;

    /** @var string */
    protected $method;

    /** @var integer */
    protected $order_id;

    /** @var string */
    protected $response;

    /** @var string */
    protected $status;
}
