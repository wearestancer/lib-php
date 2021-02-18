<?php

namespace ild78\Traits;

use DateTimeInterface;
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
     * @return Generator<static>
     * @throws ild78\Exceptions\InvalidSearchFilterException When `$terms` is invalid.
     * @throws ild78\Exceptions\InvalidSearchCreationFilterException When `created` is invalid.
     * @throws ild78\Exceptions\InvalidSearchLimitException When `limit` is invalid.
     * @throws ild78\Exceptions\InvalidSearchStartException When `start` is invalid.
     *
     * @phpstan-param array{ created?: DateTimeInterface|int, limit?: int, start?: int} $terms
     */
    public static function list(array $terms): Generator
    {
        $allowed = array_flip(['created', 'limit', 'start']);
        $others = [];

        if (method_exists(static::class, 'filterListParams')) {
            // @phpstan-ignore-next-line Method must be defined or we can not be there
            $others = static::filterListParams($terms);
        }

        $params = array_merge(array_intersect_key($terms, $allowed), $others);

        if (!$params) {
            throw new ild78\Exceptions\InvalidSearchFilterException();
        }

        if (array_key_exists('created', $terms)) {
            $created = $terms['created'];

            if ($terms['created'] instanceof DateTimeInterface) {
                $created = (int) $terms['created']->format('U');
            }

            $params['created'] = $created;

            $type = gettype($created);

            if (!$created || $type !== 'integer') {
                $message = 'Created must be a position integer or a DateTime object.';

                throw new ild78\Exceptions\InvalidSearchCreationFilterException($message);
            }

            if ($created > time()) {
                $message = 'Created must be in the past.';

                throw new ild78\Exceptions\InvalidSearchCreationFilterException($message);
            }
        }

        if (array_key_exists('limit', $terms)) {
            $params['limit'] = $terms['limit'];
            $type = gettype($terms['limit']);

            if ($type !== 'integer' || $terms['limit'] < 1 || $terms['limit'] > 100) {
                throw new ild78\Exceptions\InvalidSearchLimitException();
            }
        }

        $params['start'] = 0;

        if (array_key_exists('start', $terms)) {
            $params['start'] = $terms['start'];
            $type = gettype($terms['start']);

            if ($type !== 'integer' || $terms['start'] < 0) {
                throw new ild78\Exceptions\InvalidSearchStartException();
            }
        }

        $request = new ild78\Core\Request();
        $element = new static(); // Mandatory for requests.
        $property = strtolower($element->getEntityName() . 's');

        // phpcs:ignore Squiz.PHP.CommentedOutCode.Found
        // @var callable(): Generator<static> $gen
        $gen = function () use ($request, $element, $params, $property): Generator {
            $more = true;
            $start = 0;

            do {
                $params['start'] += $start;

                try {
                    $tmp = $request->get($element, $params);

                    if (!$tmp) {
                        $more = false;
                    } else {
                        $results = json_decode($tmp, true);

                        if (!array_key_exists($property, $results)) {
                            $more = false;
                        } else {
                            $more = $results['range']['has_more'];
                            $start += $results['range']['limit'];

                            foreach ($results[$property] as $data) {
                                $obj = new static($data['id']);

                                $obj->cleanModified = true;
                                $obj->hydrate($data);

                                yield $obj;
                            }
                        }
                    }
                } catch (ild78\Exceptions\NotFoundException $exception) {
                    $more = false;
                }
            } while ($more);
        };

        return $gen();
    }
}
