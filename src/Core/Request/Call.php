<?php
declare(strict_types=1);

namespace ild78\Core\Request;

use ild78;
use Psr;

/**
 * Register API call
 */
class Call extends ild78\Core\AbstractObject
{
    /** @var array */
    protected $dataModel = [
        'exception' => [
            'restricted' => true,
            'type' => ild78\Exceptions\Exception::class,
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
