<?php
declare(strict_types=1);

namespace Stancer\Core\Request;

use Stancer;
use Psr;

/**
 * Register API call.
 */
class Call extends Stancer\Core\AbstractObject
{
    /**
     * @var array
     * @phpstan-var array<string, DataModel>
     */
    protected $dataModel = [
        'exception' => [
            'restricted' => true,
            'type' => Stancer\Exceptions\Exception::class,
        ],
        'request' => [
            'restricted' => true,
            'type' => Psr\Http\Message\RequestInterface::class,
        ],
        'response' => [
            'restricted' => true,
            'type' => Psr\Http\Message\ResponseInterface::class,
        ],
    ];
}
