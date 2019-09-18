<?php

namespace ild78\Tests\Provider;

use ild78;

trait Http
{
    public function httpStatusDataProvider()
    {
        $data = [];

        $data[] = [100, 'Continue'];
        $data[] = [101, 'Switching Protocols'];
        $data[] = [102, 'Processing'];
        $data[] = [103, 'Early Hints'];

        $data[] = [200, 'OK'];
        $data[] = [201, 'Created'];
        $data[] = [202, 'Accepted'];
        $data[] = [203, 'Non-Authoritative Information'];
        $data[] = [204, 'No Content'];
        $data[] = [205, 'Reset Content'];
        $data[] = [206, 'Partial Content'];
        $data[] = [207, 'Multi-Status'];
        $data[] = [208, 'Already Reported'];
        $data[] = [226, 'IM Used'];

        $data[] = [300, 'Multiple Choices'];
        $data[] = [301, 'Moved Permanently'];
        $data[] = [302, 'Found'];
        $data[] = [303, 'See Other'];
        $data[] = [304, 'Not Modified'];
        $data[] = [305, 'Use Proxy'];
        $data[] = [306, 'Switch Proxy'];
        $data[] = [307, 'Temporary Redirect'];
        $data[] = [308, 'Permanent Redirect'];

        $data[] = [400, 'Bad Request', ild78\Exceptions\BadRequestException::class];
        $data[] = [401, 'Unauthorized', ild78\Exceptions\NotAuthorizedException::class];
        $data[] = [402, 'Payment Required', ild78\Exceptions\PaymentRequiredException::class];
        $data[] = [403, 'Forbidden', ild78\Exceptions\ForbiddenException::class];
        $data[] = [404, 'Not Found', ild78\Exceptions\NotFoundException::class];
        $data[] = [405, 'Method Not Allowed', ild78\Exceptions\MethodNotAllowedException::class];
        $data[] = [406, 'Not Acceptable', ild78\Exceptions\NotAcceptableException::class];
        $data[] = [407, 'Proxy Authentication Required', ild78\Exceptions\ProxyAuthenticationRequiredException::class];
        $data[] = [408, 'Request Timeout', ild78\Exceptions\RequestTimeoutException::class];
        $data[] = [409, 'Conflict', ild78\Exceptions\ConflictException::class];
        $data[] = [410, 'Gone', ild78\Exceptions\GoneException::class];
        $data[] = [411, 'Length Required'];
        $data[] = [412, 'Precondition Failed'];
        $data[] = [413, 'Payload Too Large'];
        $data[] = [414, 'URI Too Long'];
        $data[] = [415, 'Unsupported Media Type'];
        $data[] = [416, 'Range Not Satisfiable'];
        $data[] = [417, 'Expectation Failed'];
        $data[] = [418, 'I\'m a teapot'];
        $data[] = [421, 'Misdirected Request'];
        $data[] = [422, 'Unprocessable Entity'];
        $data[] = [423, 'Locked'];
        $data[] = [424, 'Failed Dependency'];
        $data[] = [426, 'Upgrade Required'];
        $data[] = [428, 'Precondition Required'];
        $data[] = [429, 'Too Many Requests'];
        $data[] = [431, 'Request Header Fields Too Large'];
        $data[] = [451, 'Unavailable For Legal Reasons'];

        $data[] = [500, 'Internal Server Error', ild78\Exceptions\InternalServerErrorException::class];
        $data[] = [501, 'Not Implemented'];
        $data[] = [502, 'Bad Gateway'];
        $data[] = [503, 'Service Unavailable'];
        $data[] = [504, 'Gateway Timeout'];
        $data[] = [505, 'HTTP Version Not Supported'];
        $data[] = [506, 'Variant Also Negotiates'];
        $data[] = [507, 'Insufficient Storage'];
        $data[] = [508, 'Loop Detected'];
        $data[] = [510, 'Not Extended'];
        $data[] = [511, 'Network Authentication Required'];

        // Unknown status will result empty message.
        $data[] = [999, '', ild78\Exceptions\HttpException::class];

        shuffle($data);

        return $data;
    }

    public function statusDataProvider()
    {
        $data = [];

        foreach ($this->httpStatusDataProvider() as $value) {
            if (!empty($value[2])) {
                $data[] = [
                    $value[0],
                    $value[2],
                ];
            }
        }


        $data[] = [310, ild78\Exceptions\TooManyRedirectsException::class];

        // Levels
        $data[] = [399, ild78\Exceptions\RedirectionException::class];
        $data[] = [499, ild78\Exceptions\ClientException::class];
        $data[] = [599, ild78\Exceptions\ServerException::class];

        shuffle($data);

        return $data;
    }
}
