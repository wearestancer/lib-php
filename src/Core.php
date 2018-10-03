<?php
declare(strict_types=1);

namespace ild78;

/**
 * Manage common code between API object
 */
abstract class Core
{
    /** @var string */
    protected $endpoint = '';

    /**
     * Return API endpoint
     *
     * @return string
     */
    public function getEndpoint() : string
    {
        return $this->endpoint;
    }
}
