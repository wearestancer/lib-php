<?php
declare(strict_types=1);

namespace ild78;

/**
 * Representation of a payment
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
    protected $currency;

    /** @var ild78\\Customer */
    protected $customer;

    /** @var string */
    protected $description;
}
