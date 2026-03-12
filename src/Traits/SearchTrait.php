<?php

declare(strict_types=1);

namespace Stancer\Traits;

use DatePeriod;
use DateTimeInterface;
use Generator;
use Stancer;
use Stancer\Core\SearchObject;

/**
 * Simple trait to simplify object search from the API.
 */
trait SearchTrait
{
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
     *
     * @phpstan-param SearchFilters $terms
     *
     * @return \Generator<static>
     * @throws Stancer\Exceptions\InvalidSearchFilterException When `$terms` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchCreationFilterException When `created` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchCreationFilterException When `created` is a DatePeriod without end.
     * @throws Stancer\Exceptions\InvalidSearchCreationUntilFilterException When `created_until` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchLimitException When `limit` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchStartException When `start` is invalid.
     */
    public static function list(array $terms): \Generator
    {
        $element = new static(); // Mandatory for requests.

        /** @phpstan-var 'disputes'|'payments'|'refunds'|'payment_intents' $property */
        $property = strtolower($element->getEntityName() . 's');

        return $element->search(static::class, $property, $terms);
    }

    /**
     * Filter the parameters to have an array of valid parameter.
     *
     * @param array<mixed> $terms The parameters to be filtered.
     * @param array<string,int> $allowed The allowed query parameters.
     *
     * @return array<mixed>
     *
     * @phstan-param SearchFilter $terms The parameters to be filtered.
     *
     * @phpstan-return array{0:SearchFiltered, 1:DateTimeInterface|int|null}
     *
     * @throws Stancer\Exceptions\InvalidSearchFilterException When `$terms` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchCreationFilterException When `created` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchCreationFilterException When `created` is a DatePeriod without end.
     * @throws Stancer\Exceptions\InvalidSearchCreationUntilFilterException When `created_until` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchLimitException When `limit` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchStartException When `start` is invalid.
     */
    protected function filterParams(array $terms, array $allowed): array
    {
        $others = [];
        $until = null;

        if (method_exists(static::class, 'filterListParams')) {
            // @phpstan-ignore-next-line Method must be defined or we can not be there
            $others = static::filterListParams($terms);
        }

        /** @phpstan-var SearchFiltered & array<mixed> $params */
        $params = array_merge(array_intersect_key($terms, $allowed), $others);

        if (!$params) {
            throw new Stancer\Exceptions\InvalidSearchFilterException();
        }

        $until = $params['created_until'] ?? null;
        unset($params['created_until']);

        if (array_key_exists('created', $terms)) {
            $exception = Stancer\Exceptions\InvalidSearchCreationFilterException::class;
            $termCreated = $terms['created'];

            if ($termCreated instanceof \DatePeriod) {
                $created = $termCreated->getStartDate();

                if (is_null($termCreated->getEndDate())) {
                    throw new $exception('DatePeriod must have an end to be used.');
                }

                if (!$until) {
                    $until = $termCreated->getEndDate();
                }
            } else {
                $created = $termCreated;
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

        return [
            $params,
            $until,
        ];
    }

    /**
     * Inner wrapper for `list` method.
     *
     * @param string $class Base class.
     * @param 'disputes'|'payment_intents'|'payments'|'refunds' $property Searched property.
     * @param array $terms Search terms.
     * @param string $innerSearch The innerSearch name (appended at the end of the uri).
     *
     * @phpstan-param class-string<Stancer\Core\AbstractObject> $class Base class.
     * @phpstan-param SearchFilters $terms
     *
     * @return \Generator<static>
     * @throws Stancer\Exceptions\InvalidSearchFilterException When `$terms` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchFilterException When `$this->id" is empty.
     * @throws Stancer\Exceptions\InvalidSearchCreationFilterException When `created` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchCreationFilterException When `created` is a DatePeriod without end.
     * @throws Stancer\Exceptions\InvalidSearchCreationUntilFilterException When `created_until` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchLimitException When `limit` is invalid.
     * @throws Stancer\Exceptions\InvalidSearchStartException When `start` is invalid.
     */
    protected function search(string $class, string $property, array $terms, ?string $innerSearch = null): \Generator
    {
        $object = $this;
        if ($innerSearch !== null) {
            if ($this->id === null) {
                throw new Stancer\Exceptions\InvalidSearchFilterException(
                    'You cannot search linked item before sending the object.'
                );
            }
            $object = new SearchObject($this->id, $innerSearch, $this::ENDPOINT);
        }
        $allowed = [
            'created',
            'created_until',
            'limit',
            'start',
        ];
        $allowed = array_flip($allowed);
        [
            $params,
            $until,
        ] = $this->filterParams($terms, $allowed);
        $request = new Stancer\Core\Request();
        // phpcs:ignore Squiz.PHP.CommentedOutCode.Found
        // @var callable(): Generator<static> $gen
        $gen = function () use ($class, $object, $request, $params, $property, $until): \Generator {
            $more = true;

            do {
                try {
                    $tmp = $request->get($object, $params);

                    if (!$tmp) {
                        $more = false;
                    } else {
                        /** @phpstan-var SearchResult $results */
                        $results = json_decode($tmp, true);

                        if (!is_array($results) || !array_key_exists($property, $results)) {
                            $more = false;
                        } else {
                            $more = $results['range']['has_more'];
                            $params['start'] += $results['range']['limit'];

                            /** @var array<string, mixed> $data */
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
     * @phpstan-param DateTimeInterface|integer $value Parameter value.
     * @phpstan-param class-string<Stancer\Exceptions\Exception> $exception Exception to throw.
     *
     * @return integer Ready to use timestamp.
     * @throws Stancer\Exceptions\InvalidSearchFilterException When `created` is invalid.
     */
    protected static function validateDateRelativeFilter(
        mixed $value,
        string $name,
        string $exception,
        bool $allowPeriod = false
    ): int {
        $timestamp = $value;

        if ($value instanceof \DateTimeInterface) {
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
