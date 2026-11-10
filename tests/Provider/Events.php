<?php

namespace Stancer\Tests\Provider;

trait Events
{
    public function signatureCheckProvider()
    {
        $falseEventProvider = [
            'hmac' => '7996915bd0a63c0ad70956d7e43c5c0a788c2895c3cce3dd63c8391b834ef961',
            'payload' => 'foo',
            'secret' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
            'timestamp' => '946684800',
        ];
        $falseEventProvider['signature'] = 't=' . $falseEventProvider['timestamp'] . ',v1=' . $falseEventProvider['hmac'];

        return [[$falseEventProvider], $this->eventProvider()];
    }

    public function eventProvider()
    {
        $realEventProvider = [
            'hmac' => '02f0efd40bcfdbf23fab999953cbdc0323b8817657947bd56966f1b0fe7dabde',
            'payload' => '{"id": "evt_9Om8qngWyslwRJizGObuXH5y", "type": "customer.created", "data": {"id": "cust_Rm5yShB2eas3QD93nyRZaiix", "country": null, "created": "2026-10-20T10:43:00", "date_birth": null, "email": null, "external_id": null, "legal_id": null, "mobile": "+33646464600", "name": null, "billing_address": null, "shipping_address": null, "deleted": false}}',
            'secret' => 'f61e1955a003c1a9973ccf0cb849bd861a4452d0ead317bb',
            'timestamp' => '1792485781',
            'signature' => 't=1792485781,v1=02f0efd40bcfdbf23fab999953cbdc0323b8817657947bd56966f1b0fe7dabde',
        ];

        return [$realEventProvider];
    }
}
