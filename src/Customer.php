<?php
declare(strict_types=1);

namespace ild78;

/**
 * Representation of a customer
 */
class Customer extends Core
{
    /** @var string */
    protected $endpoint = 'customers';


    /** @var string */
    protected $email;

    /** @var string */
    protected $mobile;

    /** @var string */
    protected $name;
}
