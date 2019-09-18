<?php

namespace ild78\Tests\Provider;

trait Currencies
{
    public function currencyDataProvider()
    {
        $data = [
            'EUR',
            'USD',
            'GBP',
        ];

        shuffle($data);

        return $data;
    }
}
