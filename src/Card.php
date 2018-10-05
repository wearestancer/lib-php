<?php
declare(strict_types=1);

namespace ild78;

/**
 * Representation of a card
 */
class Card extends Api\Object
{
    /** @var string */
    protected $brand;

    /** @var string */
    protected $country;

    /** @var integer */
    protected $cvc;

    /** @var integer */
    protected $expMonth;

    /** @var integer */
    protected $expYear;

    /** @var string */
    protected $last4;

    /** @var string|null */
    protected $name;

    /** @var integer */
    protected $number;

    /** @var string|null */
    protected $zipCode;
}
