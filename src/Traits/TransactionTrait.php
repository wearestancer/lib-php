<?php

declare(strict_types=1);

namespace Stancer\Traits;

use Stancer;

trait TransactionTrait
{
    /**
     * Add an allowed method.
     *
     * @param Stancer\Payment\MethodsAllowed|string $method New method.
     *
     * @return $this
     * @throws Stancer\Exceptions\InvalidArgumentException When currency is not EUR and trying to set "sepa" method.
     */
    public function addMethodsAllowed(Stancer\Payment\MethodsAllowed|string $method): static
    {
        $currency = $this->getCurrency();

        if ($currency && $method && $method === 'sepa' && $currency !== Stancer\Currency::EUR) {
            $message = sprintf('You can not use "%s" method with "%s" currency.', $method, $currency->value);

            throw new Stancer\Exceptions\InvalidArgumentException($message);
        }

        // @phpstan-ignore-next-line The method is not defined in parent object so it will trigger __call ...
        return parent::addMethodsAllowed($method);
        // ... and that's that we want
    }

    /**
     * Capture an authorized payment.
     *
     * @throws Stancer\Exceptions\BadRequestException If the payment isn't Capturable.
     */
    public function capture(): static
    {
        if ($this->getId() === null || $this->getStatus() === null || !$this->getStatus()->isCapturable()) {
            $message = 'The ' . $this->getEntityName() . ' must be authorized to be captured.';

            throw new Stancer\Exceptions\BadRequestException($message);
        }
        $capture = new Stancer\Core\SearchObject($this->getId(), 'capture', $this->getEndpoint());
        $request = new Stancer\Core\Request();
        // We post the serialized object, jsonSerialize if notModified return the ID, that's what we want!
        $response = $request->post($capture);

        // Maybe make this a protected function "hydrateFromResponse(string $response) :static.
        /** @phpstan-var array<string, mixed> $body */
        $body = json_decode($response, true);

        if ($body) {
            $this->cleanModified = true;
            $this->hydrate($body);
        }

        $this->modified = [];

        $message = sprintf('%s "%s" %s', $this->getEntityName(), $this->id, 'capture');
        Stancer\Config::getGlobal()->getLogger()->info($message);

        return $this;
    }

    /**
     * Set the currency.
     *
     * @param Stancer\Currency|string $currency The currency.
     *
     * @return $this
     * @throws Stancer\Exceptions\InvalidCurrencyException When currency is EUR and "sepa" is already allowed.
     * @throws Stancer\Exceptions\InvalidCurrencyException When the currency is invalid.
     */
    public function setCurrency(Stancer\Currency|string $currency): static
    {
        try {
            if (is_string($currency)) {
                $new = Stancer\Currency::from(strtolower($currency));
            } else {
                $new = $currency;
            }
        } catch (\ValueError $exception) {
            $params = [
                $currency,
                implode(', ', array_map(fn (Stancer\Currency $case): string => $case->value, Stancer\Currency::cases())),
            ];
            $message = vsprintf('"%s" is not a valid currency, please use one of the following: %s', $params);

            throw new Stancer\Exceptions\InvalidCurrencyException($message, previous: $exception);
        }

        $methods = $this->getMethodsAllowed();

        if (in_array(Stancer\Payment\MethodsAllowed::SEPA, $methods, true) && $new !== Stancer\Currency::EUR) {
            $message = sprintf('You can not use "%s" currency with "%s" method.', $new->value, 'sepa');

            throw new Stancer\Exceptions\InvalidCurrencyException($message);
        }

        return parent::setCurrency($new);
    }

    /**
     * Update return URL.
     *
     * @param string $url New HTTPS URL.
     *
     * @return $this
     * @throws Stancer\Exceptions\InvalidUrlException When URL is not an HTTPS URL.
     */
    public function setReturnUrl(string $url): self
    {
        if (strpos($url, 'https://') !== 0) {
            throw new Stancer\Exceptions\InvalidUrlException('You must provide an HTTPS URL.');
        }

        return parent::setReturnUrl($url);
    }
}
