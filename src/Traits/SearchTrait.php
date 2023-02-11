<?php

namespace Stancer\Traits;

use DatePeriod;
use DateTimeInterface;
use Generator;
use Stancer;

/**
 * Simple trait to simplify object search from the API.
 */
trait SearchTrait
{
    // phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber

    /**
     * List elements.
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
     * @return Generator<Stancer\Core\AbstractObject>
     * @throws Stancer\Exceptions\InvalidSearchFilterException When `$terms` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchCreationFilterException When `created` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchCreationFilterException When `created` is a DatePeriod without end.
     * @throws Stancer\Exceptions\InvalidSearchCreationUntilFilterException When `created_until` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchLimitException When `limit` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchStartException When `start` is invalid.
     *
     * @phpstan-param SearchFilters $terms
     */
    public static function list(array $terms): Generator
    {
        $element = new static(); // Mandatory for requests.
        $property = strtolower($element->getEntityName() . 's');

        return $element->search(static::class, $property, $terms);
    }

    /**
     * Inner wrapper for `list` method.
     *
     * @param string $class Base class.
     * @param string $property Searched property.
     * @param array $terms Search terms.
     * @return Generator<Stancer\Core\AbstractObject>
     * @throws Stancer\Exceptions\InvalidSearchFilterException When `$terms` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchCreationFilterException When `created` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchCreationFilterException When `created` is a DatePeriod without end.
     * @throws Stancer\Exceptions\InvalidSearchCreationUntilFilterException When `created_until` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchLimitException When `limit` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchStartException When `start` is invalid.
     *
     * @phpstan-param class-string<Stancer\Core\AbstractObject> $class
     * @phpstan-param SearchFilters $terms
     */
    protected function search(string $class, string $property, array $terms): Generator
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
            throw new Stancer\Exceptions\InvalidSearchFilterException();
        }

        $until = $params['created_until'] ?? null;
        unset($params['created_until']);

        if (array_key_exists('created', $terms)) {
            $exception = Stancer\Exceptions\InvalidSearchCreationFilterException::class;

            if (!($terms['created'] instanceof DatePeriod)) {
                $created = $terms['created'];
            } else {
                $created = $terms['created']->getStartDate();

                if (is_null($terms['created']->getEndDate())) {
                    throw new $exception('DatePeriod must have an end to be used.');
                }

                if (!$until) {
                    $until = $terms['created']->getEndDate();
                }
            }

            $params['created'] = static::validateDateRelativeFilter($created, 'Created', $exception, true);
        }

        if (!is_null($until)) {
            $exception = Stancer\Exceptions\InvalidSearchCreationUntilFilterException::class;
            $until = static::validateDateRelativeFilter($until, 'Created until', $exception);
        }

        if ($until && array_key_exists('created', $params) && $params['created'] > $until) {
            $message = 'Created until must be after created date.';

            throw new Stancer\Exceptions\InvalidSearchCreationUntilFilterException($message);
        }

        if (array_key_exists('limit', $terms)) {
            $params['limit'] = $terms['limit'];
            $type = gettype($terms['limit']);

            if ($type !== 'integer' || $terms['limit'] < 1 || $terms['limit'] > 100) {
                throw new Stancer\Exceptions\InvalidSearchLimitException();
            }
        }

        $params['start'] = 0;

        if (array_key_exists('start', $terms)) {
            $params['start'] = $terms['start'];
            $type = gettype($terms['start']);

            if ($type !== 'integer' || $terms['start'] < 0) {
                throw new Stancer\Exceptions\InvalidSearchStartException();
            }
        }

        $request = new Stancer\Core\Request();

        // phpcs:ignore Squiz.PHP.CommentedOutCode.Found
        // @var callable(): Generator<static> $gen
        $gen = function () use ($class, $request, $params, $property, $until): Generator {
            $more = true;

            do {
                try {
                    $tmp = $request->get($this, $params);

                    if (!$tmp) {
                        $more = false;
                    } else {
                        $results = json_decode($tmp, true);

                        if (!is_array($results) || !array_key_exists($property, $results)) {
                            $more = false;
                        } else {
                            $more = $results['range']['has_more'];
                            $params['start'] += $results['range']['limit'];

                            foreach ($results[$property] as $data) {
                                if ($until && $data['created'] > $until) {
                                    $more = false;
                                    break;
                                }

                                $obj = new $class($data['id']);

                                $obj->cleanModified = true;
                                $obj->hydrate($data);

                                yield $obj;
                            }
                        }
                    }
                } catch (Stancer\Exceptions\NotFoundException $exception) {
                    $more = false;
                }
            } while ($more);
        };

        return $gen();
    }

    // phpcs:enable
    // phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.NewlineBeforeOpenBrace

    /**
     * Validate date relative filter.
     *
     * @param mixed $value Parameter value.
     * @param string $name Parameter name.
     * @param string $exception Exception to throw.
     * @param boolean $allowPeriod Allow DatePeriod object.
     *
     * @return integer Ready to use timestamp.
     *
     * @throws Stancer\Exceptions\InvalidSearchFilterException When `created` is invalid.
     *
     * @phpstan-param DateTimeInterface|integer $value Parameter value.
     * @phpstan-param class-string<Stancer\Exceptions\Exception> $exception Exception to throw.
     */
    protected static function validateDateRelativeFilter(
        $value,
        string $name,
        string $exception,
        bool $allowPeriod = false
    ): int
    {
        // phpcs:enable
        $timestamp = $value;

        if ($value instanceof DateTimeInterface) {
            $timestamp = $value->getTimestamp();
        }

        if (!$timestamp || !is_int($timestamp)) {
            $message = $name . ' must be a positive integer or a DateTime object.';

            if ($allowPeriod) {
                $message = $name . ' must be a positive integer, a DateTime object or a DatePeriod object.';
            }

            throw new $exception($message);
        }

        if ($timestamp > time()) {
            throw new $exception($name . ' must be in the past.');
        }

        return $timestamp;
    }
}
