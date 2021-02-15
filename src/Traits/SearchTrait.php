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
     * @throws ild78\Exceptions\InvalidSearchCreationUntilFilterException When `created_until` is invalid.
     * @throws ild78\Exceptions\InvalidSearchLimitException When `limit` is invalid.
     * @throws ild78\Exceptions\InvalidSearchStartException When `start` is invalid.
     *
     * @phpstan-param array{ created?: DateTimeInterface|int, created_until?: DateTimeInterface|int, limit?: int, start?: int} $terms
     */
    public static function list(array $terms): Generator
    {
        $allowed = array_flip(['created', 'created_until', 'limit', 'start']);
        $others = [];
        $until = null;

        if (method_exists(static::class, 'filterListParams')) {
            // @phpstan-ignore-next-line Method must be defined or we can not be there
            $others = static::filterListParams($terms);
        }

        $params = array_merge(array_intersect_key($terms, $allowed), $others);

        if (!$params) {
            throw new ild78\Exceptions\InvalidSearchFilterException();
        }

        if (array_key_exists('created', $terms)) {
            $exception = ild78\Exceptions\InvalidSearchCreationFilterException::class;
            $params['created'] = static::validateDateRelativeFilter($terms['created'], 'Created', $exception);
        }

        if (array_key_exists('created_until', $terms)) {
            $exception = ild78\Exceptions\InvalidSearchCreationUntilFilterException::class;
            $until = static::validateDateRelativeFilter($terms['created_until'], 'Created until', $exception);
            unset($params['created_until']);
        }

        if ($until && array_key_exists('created', $params) && $params['created'] > $until) {
            $message = 'Created until must be after created date.';

            throw new ild78\Exceptions\InvalidSearchCreationUntilFilterException($message);
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
        $gen = function () use ($request, $element, $params, $property, $until): Generator {
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
                                if ($until && $data['created'] > $until) {
                                    $more = false;
                                    break;
                                }

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

    /**
     * Validate date relative filter.
     *
     * @param mixed $value Parameter value.
     * @param string $name Parameter name.
     * @param string $exception Exception to throw.
     *
     * @return integer Ready to use timestamp.
     *
     * @throws ild78\Exceptions\InvalidSearchFilterException When `created` is invalid.
     *
     * @phpstan-param class-string $exception Exception to throw.
     */
    protected static function validateDateRelativeFilter($value, string $name, string $exception): int
    {
        $timestamp = $value;

        if ($value instanceof DateTimeInterface) {
            $timestamp = $value->getTimestamp();
        }

        $type = gettype($timestamp);

        if (!$timestamp || $type !== 'integer') {
            $message = $name . ' must be a positive integer or a DateTime object.';

            throw new $exception($message);
        }

        if ($timestamp > time()) {
            $message = $name . ' must be in the past.';

            throw new $exception($message);
        }

        return $timestamp;
    }
}
