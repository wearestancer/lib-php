<?php

namespace ild78\Tests\Provider;

trait Currencies
{
    public function cardCurrencyDataProvider($one = false)
    {
        $data = [
            'AUD',
            'CAD',
            'CHF',
            'DKK',
            'EUR',
            'GBP',
            'NOK',
            'PLN',
            'SEK',
            'USD',
        ];

        shuffle($data);

        if ($one) {
            return $data[0];
        }

        return $data;
    }

    public function sepaCurrencyDataProvider($one = false)
    {
        $data = [
            'EUR',
        ];

        shuffle($data);

        if ($one) {
            return $data[0];
        }

        return $data;
    }
}
