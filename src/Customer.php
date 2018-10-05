<?php
declare(strict_types=1);

namespace ild78;

/**
 * Representation of a customer
 *
 * @method string getEmail()
 * @method string getMobile()
 * @method string getName()
 */
class Customer extends Api\Object
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
