<?php

declare(strict_types=1);

namespace Stancer;

class Event
{
    public readonly string $id;
    public readonly string $action;

    /**
     * Data is a mixed array comming directly from json.
     *
     * @var mixed[]
     */
    public readonly array $data;
    public readonly string $modelName;
    public readonly string $modelId;
    public readonly string $type;

    /**
     * Construct a readonly Event.
     *
     * @param mixed[] $data
     */
    public function __construct(array $data)
    {
        if (!key_exists('type', $data) || !is_string($data['type'])) {
            throw new \Stancer\Exceptions\InvalidEventException();
        }

        $this->type = $data['type'];

        if (!key_exists('id', $data) || !is_string($data['id'])) {
            throw new \Stancer\Exceptions\InvalidEventException();
        }

        $this->id = $data['id'];

        if (!key_exists('data', $data) || !is_array($data['data'])) {
            throw new \Stancer\Exceptions\InvalidEventException();
        }

        $this->data = $data['data'];

        if (!key_exists('id', $data['data']) || !is_string($data['data']['id'])) {
            throw new \Stancer\Exceptions\InvalidEventException();
        }

        $this->modelId = $data['data']['id'];

        if (!str_contains($this->type, '.')) {
            throw new \Stancer\Exceptions\InvalidEventException();
        }

        [$this->modelName,$this->action] = explode('.', $this->type);
    }
}
