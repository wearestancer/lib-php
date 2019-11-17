<?php

namespace ild78\Tests\Provider;

trait Currencies
{
    public function currencyDataProvider($one = false)
    {
        $data = [
            'EUR',
            'USD',
            'GBP',
        ];

        shuffle($data);

        if ($one) {
            return $data[0];
        }

        return $data;
    }
}
