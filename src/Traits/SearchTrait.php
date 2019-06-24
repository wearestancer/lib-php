<?php

namespace ild78\Traits;

use DateTime;
use Generator;
use ild78;

/**
 * Simple trait to simplify object search from the API.
 */
trait SearchTrait
{
    /**
     * List elements
     *
     * `$terms` must be an associative array with one of the following key : `created`, `limit` or `start`.
     *
     * `created` must be an unix timestamp or a DateTime object which will filter payments equal
     * to or greater than this value.
     *
     * `limit` must be an integer between 1 and 100 and will limit the number of objects to be returned.
     *
     * `start` must be an integer, will be used as a pagination cursor, starts at 0.
     *
     * @param array $terms Search terms. May have `created`, `limit` or `start` key.
     * @return Generator
     * @throws ild78\Exceptions\InvalidSearchFilter When `$terms` is invalid.
     * @throws ild78\Exceptions\InvalidSearchCreationFilter When `created` is invalid.
     * @throws ild78\Exceptions\InvalidSearchLimit When `limit` is invalid.
     * @throws ild78\Exceptions\InvalidSearchStart When `start` is invalid.
     */
    public static function list(array $terms) : Generator
    {
        $allowed = array_flip(['created', 'limit', 'start']);
        $others = [];

        if (method_exists(static::class, 'filterListFilter')) {
            $others = static::filterListFilter($terms);
        }

        $params = array_merge(array_intersect_key($terms, $allowed), $others);

        if (!$params) {
            throw new ild78\Exceptions\InvalidSearchFilter();
        }

        if (array_key_exists('created', $terms)) {
            $created = $terms['created'];

            if ($terms['created'] instanceof DateTime) {
                $created = (int) $terms['created']->format('U');
            }

            $params['created'] = $created;

            $type = gettype($created);

            if (!$created || $type !== 'integer') {
                $message = 'Created must be a position integer or a DateTime object.';

                throw new ild78\Exceptions\InvalidSearchCreationFilter($message);
            }

            if ($created > time()) {
                $message = 'Created must be in the past.';

                throw new ild78\Exceptions\InvalidSearchCreationFilter($message);
            }
        }

        if (array_key_exists('limit', $terms)) {
            $params['limit'] = $terms['limit'];
            $type = gettype($terms['limit']);

            if ($type !== 'integer' || $terms['limit'] < 1 || $terms['limit'] > 100) {
                throw new ild78\Exceptions\InvalidSearchLimit();
            }
        }

        $params['start'] = 0;

        if (array_key_exists('start', $terms)) {
            $params['start'] = $terms['start'];
            $type = gettype($terms['start']);

            if ($type !== 'integer' || $terms['start'] < 0) {
                throw new ild78\Exceptions\InvalidSearchStart();
            }
        }

        $request = new ild78\Api\Request();
        $element = new static(); // Mandatory for requests.

        $gen = function () use ($request, $element, $params) {
            $more = true;
            $start = 0;

            do {
                $params['start'] += $start;

                $tmp = $request->get($element, $params);

                if (!$tmp) {
                    $more = false;
                } else {
                    $results = json_decode($tmp, true);
                    $more = $results['range']['has_more'];
                    $start += $results['range']['limit'];

                    foreach ($results['payments'] as $data) {
                        $obj = new static($data['id']);

                        yield $obj->hydrate($data, false);
                    }
                }
            } while ($more);
        };

        return $gen();
    }
}
