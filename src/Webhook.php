<?php

declare(strict_types=1);

namespace Stancer;

use Stancer;
use Stancer\Exceptions\InvalidEventException;
use Stancer\Exceptions\InvalidSignatureException;
use Stancer\Traits\SearchTrait;

/**
 * Representation of a Webhook.
 *
 * @method void check_hmac(string $signature, string $time, string $body)
 * @method ?\DateTimeImmutable getCreated() Get creation date.
 * @method ?boolean getEnabled()
 * @method ?string getSecret() Get the secret to validate incoming events.
 * @method ?string getUrl() Get url endpoint for the webhook.
 * @method ?\DateTimeImmutable get_created() Get creation date.
 * @method ?\DateTimeImmutable get_created_at() Get creation date.
 * @method ?\DateTimeImmutable get_creation_date() Get creation date.
 * @method ?boolean get_enabled()
 * @method string get_endpoint() Get API endpoint.
 * @method string get_entity_name() Get entity name.
 * @method Stancer\Event get_event(string $body)
 * @method ?string get_id() Get object ID.
 * @method ?string get_secret() Get the secret to validate incoming events.
 * @method string get_uri() Get entity resource location.
 * @method ?string get_url() Get url endpoint for the webhook.
 * @method $this setEnabled(boolean $enabled)
 * @method $this setUrl(string $url) Set url endpoint for the webhook.
 * @method $this set_enabled(boolean $enabled)
 * @method $this set_url(string $url) Set url endpoint for the webhook.
 * @method void verify_body(string $body, string $stancer_signature, \DateTimeImmutable $now, \DateInterval $delta)
 * @method void verify_request(\Psr\Http\Message\MessageInterface $message, \DateTimeImmutable $now = null, \DateInterval $delta = null) Verify that an event request (PSR object) is correctly formed.
 *
 * @property ?boolean $enabled
 * @property ?string $url url endpoint for the webhook.
 *
 * @property-read ?\DateTimeImmutable $created Creation date.
 * @property-read ?\DateTimeImmutable $createdAt Creation date.
 * @property-read ?\DateTimeImmutable $created_at Creation date.
 * @property-read ?\DateTimeImmutable $creationDate Creation date.
 * @property-read ?\DateTimeImmutable $creation_date Creation date.
 * @property-read string $endpoint API endpoint.
 * @property-read string $entityName Entity name.
 * @property-read string $entity_name Entity name.
 * @property-read ?string $id Object ID.
 * @property-read ?string $secret The secret to validate incoming events.
 * @property-read string $uri Entity resource location.
 */
class Webhook extends Stancer\Core\AbstractObject
{
    use SearchTrait;

    #[Stancer\WillChange\PHP8_3\TypedClassConstants]
    final public const ENDPOINT = 'webhooks';

    /**
     * @var array<string,DataModel>
     */
    protected array $dataModel = [
        'url' => [
            'desc' => 'url endpoint for the webhook',
            'required' => true,
            'type' => self::STRING,
        ],
        'enabled' => [
            'type' => self::BOOLEAN,
        ],
        'secret' => [
            'desc' => 'The secret to validate incoming events',
            'type' => self::STRING,
            'restricted' => true,
        ],
    ];

    /**
     * Check the hmac of the Event Request.
     *
     * @param string $signature The stancer signature from 'stancer-signature' header
     * @param string $time The timestamp from 'stancer-signature' header
     * @param string $body The raw body of the request
     *
     * @throws InvalidSignatureException If the signature is invalid.
     */
    public function checkHmac(string $signature, string $time, string $body): void
    {
        $secret = hex2bin($this->getSecret() ?? '');
        if ($secret === false) {
            throw new InvalidSignatureException('The webhook must exist in our database to get the event');
        }
        $computed = hash_hmac('sha256', $time . '.' . $body, $secret);
        $ok = hash_equals($signature, $computed);
        if (!$ok) {
            throw new InvalidSignatureException();
        }
    }

    /**
     * Get an Event object from an Event request.
     *
     * @param string $body T+he raw request body
     *
     * @return Event An Event object from Event request
     */
    public function getEvent(string $body): Event
    {
        $jsonData = json_decode($body, true);

        if (!is_array($jsonData)) {
            throw new InvalidEventException();
        }

        return new Event($jsonData);
    }

    /**
     * Verify the body and stancer signature of an Event.
     *
     * @param string $body The raw body of the request
     * @param string $stancerSignature The 'stancer-signature' header
     * @param \DateTimeImmutable $now A datetime to check against the event timestamp?
     * @param \DateInterval $delta A time interval.
     */
    public function verifyBody(
        string $body,
        string $stancerSignature,
        \DateTimeImmutable $now,
        \DateInterval $delta
    ): void {
        [$time,$signature] = $this->parseSignature($stancerSignature);
        $date = new \DateTimeImmutable('@' . $time);
        if (($now->sub($delta) <=> $date) > 0) {
            throw new InvalidSignatureException('Expired signature');
        }
        $this->checkHmac($signature, $time, $body);
    }

    /**
     * Verify that an event request (PSR object) is correctly formed.
     *
     * @param \DateTimeImmutable|null $now A date to check againt the timestamp of the event.
     * If null we check against now.
     * @param \DateInterval|null $delta A time interval.
     * If null two minute is used.
     *
     * We keep the default parameter as null, for compatibility with some external tools.
     */
    public function verifyRequest(
        \Psr\Http\Message\MessageInterface $message,
        ?\DateTimeImmutable $now = null,
        ?\DateInterval $delta = null,
    ): void {
        if (!$now) {
            $now = new \DateTimeImmutable('now');
        }
        if (!$delta) {
            $delta = new \DateInterval('PT2M');
        }
        $payload = (string) $message->getBody();
        $stancerSignature = $message->getHeader('stancer-signature')[0];
        $this->verifyBody($payload, $stancerSignature, $now, $delta);
    }

    /**
     * Parse the signature and return timestamp and signature.
     *
     * @param string $stancerSignature The stancer signature from 'stancer-signature' header
     * @param string $version the version of the hmac key (not useful yet but just in case)
     *
     * @return string[]
     */
    private function parseSignature(string $stancerSignature, string $version = 'v1'): array
    {
        $tmp = explode(',', $stancerSignature);
        $part = [];

        foreach ($tmp as $element) {
            [$key,$value] = explode('=', $element);
            $part[$key] = $value;
        }
        $timestamp = $part['t'];

        return [$timestamp, $part[$version]];
    }
}
