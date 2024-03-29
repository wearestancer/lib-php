<?php

namespace Stancer\Tests\Provider;

trait Dates
{
    public function timeZoneProvider()
    {
        $data = [
            'UTC',
            'America/Phoenix',
            'Atlantic/Bermuda',
            'Australia/Melbourne',
            'Europe/Paris',
        ];

        shuffle($data);

        return $data;
    }
}
