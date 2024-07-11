<?php
declare(strict_types=1);

namespace Stancer\Exceptions;

use Stancer\Interfaces\ExceptionInterface;
use Psr;

/**
 * The server did not find a current representation for the target resource.
 *
 * This represent an 404 HTTP return on the API.
 */
class UnprocessableEntityException extends ClientException implements ExceptionInterface
{
    protected static string $defaultMessage = 'Request parameters had bad type';

    protected static string $logLevel = Psr\Log\LogLevel::ERROR;

    protected static string $status = '422';

    /**
     * Create an Unprocessable Entity Exception and craft the message with info available
     *
     * @param array<string,mixed> $params The response array.
     * @return static The Exception
     *
     * @phpstan-param CreateExceptionParameters $params
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
        $obj = parent::create($params);


        return $obj;
    }
}
