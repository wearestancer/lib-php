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
 *
 * @property DateTime|null $created
 */
class Customer extends ild78\Core\AbstractObject
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
        'externalId' => [
            'size' => [
                'max' => 36,
            ],
            'type' => self::STRING,
        ],
        'mobile' => [
            'size' => [
                'min' => 8,
                'max' => 16,
            ],
            'type' => self::STRING,
        ],
        'name' => [
            'size' => [
                'min' => 4,
                'max' => 64,
            ],
            'type' => self::STRING,
        ],
    ];

    /**
     * Send a customer.
     *
     * @uses Request::post()
     * @return self
     * @throws ild78\Exceptions\BadMethodCallException When trying to send a customer without an email
     *    or a phone number.
     */
    public function send(): ild78\Core\AbstractObject
    {
        if (!$this->getId() && !$this->getEmail() && !$this->getMobile()) {
            $message = 'You must provide an email or a phone number to create a customer.';

            throw new ild78\Exceptions\BadMethodCallException($message);
        }

        return parent::send();
    }
}
