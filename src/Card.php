<?php
declare(strict_types=1);

namespace ild78;

/**
 * Representation of a card
 */
class Card extends Core
{
    /** @var integer */
    protected $cvc;

    /** @var integer */
    protected $exp_month;

    /** @var integer */
    protected $exp_year;

    /** @var integer */
    protected $number;
}
