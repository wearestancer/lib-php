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
class Customer extends Api\AbstractObject
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
     * Save a customer.
     *
     * @uses Request::post()
     * @return self
     * @throws ild78\Exceptions\BadMethodCallException When trying to save a customer without an email
     *    or a phone number.
     */
    public function save(): ild78\Api\AbstractObject
    {
        if (!$this->getId() && !$this->getEmail() && !$this->getMobile()) {
            $message = 'You must provide an email or a phone number to create a customer.';

            throw new ild78\Exceptions\BadMethodCallException($message);
        }

        return parent::save();
    }

    /**
     * Update customer's email
     *
     * @param string $email New email.
     * @return self
     * @throws ild78\Exceptions\InvalidEmailException When the email is invalid.
     */
    public function setEmail(string $email): self
    {
        try {
            return parent::setEmail($email);
        } catch (ild78\Exceptions\InvalidArgumentException $excep) {
            throw new ild78\Exceptions\InvalidEmailException($excep->getMessage(), $excep->getCode(), $excep);
        }
    }

    /**
     * Update customer's mobile phone number
     *
     * @param string $mobile New mobile phone number.
     * @return self
     * @throws ild78\Exceptions\InvalidMobileException When the mobile phone number is invalid.
     */
    public function setMobile(string $mobile): self
    {
        try {
            return parent::setMobile($mobile);
        } catch (ild78\Exceptions\InvalidArgumentException $excep) {
            throw new ild78\Exceptions\InvalidMobileException($excep->getMessage(), $excep->getCode(), $excep);
        }
    }

    /**
     * Update customer's name
     *
     * @param string $name New name.
     * @return self
     * @throws ild78\Exceptions\InvalidNameException When the name is invalid.
     */
    public function setName(string $name): self
    {
        try {
            return parent::setName($name);
        } catch (ild78\Exceptions\InvalidArgumentException $excep) {
            throw new ild78\Exceptions\InvalidNameException($excep->getMessage(), $excep->getCode(), $excep);
        }
    }
}
