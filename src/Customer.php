<?php
declare(strict_types=1);

namespace Stancer;

use Stancer;

/**
 * Representation of a customer.
 *
 * @method string getEmail()
 * @method string getMobile()
 * @method string getName()
 *
 * @method $this setEmail(string $email)
 * @method $this setMobile(string $mobile)
 * @method $this setName(string $name)
 *
 * @property DateTimeImmutable|null $created
 * @property string $email
 * @property string $mobile
 * @property string $name
 */
class Customer extends Stancer\Core\AbstractObject
{
    /** @var string */
    protected $endpoint = 'customers';

    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
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
     * @return $this
     * @throws Stancer\Exceptions\BadMethodCallException When trying to send a customer without an email
     *    or a phone number.
     */
    public function send(): Stancer\Core\AbstractObject
    {
        if (!$this->getId() && !$this->getEmail() && !$this->getMobile()) {
            $message = 'You must provide an email or a phone number to create a customer.';

            throw new Stancer\Exceptions\BadMethodCallException($message);
        }

        return parent::send();
    }
}
