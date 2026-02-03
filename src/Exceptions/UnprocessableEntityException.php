<?php

declare(strict_types=1);

namespace Stancer\Exceptions;

use Psr;
use Stancer\Interfaces\ExceptionInterface;

/**
 * The server did not find a current representation for the target resource.
 *
 * This represent an 404 HTTP return on the API.
 */
class UnprocessableEntityException extends ClientException implements ExceptionInterface
{
    protected static string $defaultMessage = 'Unable to Process the sent entity, check syntax and required parameters';

    protected static string $logLevel = Psr\Log\LogLevel::ERROR;

    protected static string $status = '422';

    /**
     * Create an Unprocessable Entity Exception and craft the message with info available.
     *
     * @param array<string,mixed> $params The response array.
     *
     * @phpstan-param CreateExceptionParameters $params
     *
     * @return static The Exception
     */
    public static function create(array $params = []): static
    {
        $message = '';
        if (array_key_exists('detail', $params) && is_array($params['detail'])) {
            $detail = $params['detail'];

            if (is_string($detail['type']) && array_key_exists('type', $detail)) {
                $message .= $detail['type'];
            }
            if (is_string($detail['msg']) && array_key_exists('msg', $detail)) {
                $message .= ': ' . $detail['msg'];
            }
            if (is_array($detail['loc']) && array_key_exists('loc', $detail)) {
                $message .= ' @ ' . implode(' -> ', $detail['loc']);
            }
        }
        $params['message'] = $message;

        return parent::create($params);
    }
}
