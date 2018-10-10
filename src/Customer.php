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

    /** @var string|null */
    protected $email;

    /** @var string|null */
    protected $mobile;

    /** @var string|null */
    protected $name;
}
