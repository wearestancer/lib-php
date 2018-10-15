<?php
declare(strict_types=1);

namespace ild78;

use ild78;

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

    /** @var array */
    protected $dataModel = [
        'email' => [
            'size' => [
                'min' => 5,
                'max' => 64,
            ],
            'type' => self::STRING,
        ],
        'mobile' => [
            'size' => [
                'min' => 4,
                'max' => 64,
            ],
            'type' => self::STRING,
        ],
        'name' => [
            'size' => [
                'min' => 8,
                'max' => 16,
            ],
            'type' => self::STRING,
        ],
    ];

    /**
     * Save a customer.
     *
     * @uses Request::post()
     * @return self
     * @throws ild78\Exceptions\BadMethodCallException When trying to save a customer without an email
     *    or a phone number.
     */
    public function save() : Api\Object
    {
        if (!$this->getEmail() && !$this->getMobile()) {
            $message = 'You must provide an email or a phone number to create a customer.';

            throw new ild78\Exceptions\BadMethodCallException($message);
        }

        return parent::save();
    }
}
