<?php

namespace Stancer\Tests\unit;

use Stancer;

class Event extends Stancer\Tests\atoum
{
    /**
     * @tags Event
     */
    public function test__construct()
    {
        $this
            ->if($data = [
                'id' => 'evt_9Om8qngWyslwRJizGObuXH5y',
                'type' => 'customer.created',
                'data' => [
                    'id' => 'cust_Rm5yShB2eas3QD93nyRZaiix',
                    'country' => null,
                    'created' => '2026-10-20T10:43:00',
                    'date_birth' => null,
                    'email' => null,
                    'external_id' => null,
                    'legal_id' => null,
                    'mobile' => '+33646464600',
                    'name' => null,
                    'billing_address' => null,
                    'shipping_address' => null,
                    'deleted' => false,
                ],
            ])->then
        ->object($event = $this->newTestedInstance($data))
            ->isInstanceOfTestedClass
        ->string($event->id)
            ->isEqualTo('evt_9Om8qngWyslwRJizGObuXH5y')
        ->string($event->type)
            ->isEqualTo('customer.created')
        ->string($event->action)
            ->isEqualTo('created')
        ->string($event->modelName)
            ->isEqualTo('customer')
        ->array($event->data)
            ->isEqualTo($data['data'])
        ;
    }

    /**
     * @tags Event
     */
    public function test_invalidData()
    {
        $this
            ->assert('No type result in error')
                ->if($invalidData = [
                    'id' => 'evt_9Om8qngWyslwRJizGObuXH5y',
                    'data' => [
                        'id' => 'cust_Rm5yShB2eas3QD93nyRZaiix',
                        'mobile' => '+33646464600',
                    ],
                ])
                ->exception(fn () => $this->newTestedInstance($invalidData))
                    ->isInstanceOf(Stancer\Exceptions\InvalidEventException::class)

            ->assert('No Id result in error')
                ->if($invalidData = [
                    'data' => [
                        'id' => 'cust_Rm5yShB2eas3QD93nyRZaiix',
                        'mobile' => '+33646464600',
                    ],
                    'type' => 'customer.created',
                ])
                ->exception(fn () => $this->newTestedInstance($invalidData))
                    ->isInstanceOf(Stancer\Exceptions\InvalidEventException::class)

            ->assert('No Data result in error')
                ->if($invalidData = [
                    'id' => 'cust_Rm5yShB2eas3QD93nyRZaiix',
                    'type' => 'customer.created',
                ])
                ->exception(fn () => $this->newTestedInstance($invalidData))
                    ->isInstanceOf(Stancer\Exceptions\InvalidEventException::class)

            ->assert('No Data id result in error')
                ->if($invalidData = [
                    'id' => 'cust_Rm5yShB2eas3QD93nyRZaiix',
                    'type' => 'customer.created',
                    'data' => [
                        'mobile' => '+33646464600',
                    ],
                ])
                ->exception(fn () => $this->newTestedInstance($invalidData))
                    ->isInstanceOf(Stancer\Exceptions\InvalidEventException::class)

            ->assert('Type malformed result in error')
                ->if($invalidData = [
                    'id' => 'cust_Rm5yShB2eas3QD93nyRZaiix',
                    'type' => 'customercreated',
                    'data' => [
                        'id' => 'cust_Rm5yShB2eas3QD93nyRZaiix',
                        'mobile' => '+33646464600',
                    ],
                ])
                ->exception(fn () => $this->newTestedInstance($invalidData))
                    ->isInstanceOf(Stancer\Exceptions\InvalidEventException::class)
        ;
    }
}
