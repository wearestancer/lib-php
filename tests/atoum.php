<?php

namespace Stancer\Tests;

use atoum\atoum as base;
use Stancer;
use Faker;
use Psr;
use mock;

class atoum extends base\test
{
    public function __construct(
        base\adapter $adapter = null,
        base\annotations\extractor $annotationExtractor = null,
        base\asserter\generator $asserterGenerator = null,
        base\test\assertion\manager $assertionManager = null,
        \closure $reflectionClassFactory = null
    ) {
        parent::__construct($adapter, $annotationExtractor, $asserterGenerator, $assertionManager, $reflectionClassFactory);

        $this->getAsserterGenerator()->addNamespace('Stancer\Tests\asserters');
    }

    public function beforeTestMethod($method)
    {
        if ($method !== 'testGetGlobal_SetGlobal') {
            Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]);
        }
    }

    public function fake(): Faker\Generator
    {
        return Faker\Factory::create();
    }

    public function getFixture(string ...$parts): string
    {
        return file_get_contents(__DIR__ . '/fixtures/' . implode('/', $parts) . '.json');
    }

    public function getFixtureData(string ...$parts): array
    {
        return json_decode($this->getFixture(...$parts), true);
    }

    public function getRandomDate(int $min, int $max = null): string
    {
        if (!$max) {
            $max = date('Y');
        }

        $year = random_int($min, $max);
        $month = random_int(1, 12);

        $dMax = 31;

        if ($month == 2) {
            $dMax = 27;
        } else if (in_array($month, [4, 6, 9, 11])) {
            $dMax = 30;
        }

        $day = random_int(1, $dMax);

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    public function getRandomNumber(): string
    {
        // Simulate a french mobile phone number
        $first = rand(0, 1) + 6;
        $loop = 4;

        $number = '+33' . $first;

        if ($first === 7) {
            $number .= str_pad(rand(30, 99), 2, '0');
            $loop--;
        }

        for ($idx = 0; $idx < $loop; $idx++) {
            $number .= str_pad(rand(0, 99), 2, '0');
        }

        return $number;
    }

    public function getRandomString(int $min, int $max = null): string
    {
        if (!$max) {
            $max = $min;
        }

        $len = random_int($min, $max);

        if (!$len) {
            return '';
        }

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charlen = strlen($characters) - 1;
        $randomString = '';

        for ($i = 0; $i < $len; $i++) {
            $index = rand(0, $charlen);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    public function getUuid(): string
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * @param Psr\Http\Client\ClientInterface|mock\Psr\Http\Client\ClientInterface $client
     */
    public function mockConfig($client): Stancer\Config
    {
        $config = Stancer\Config::init(['stest_' . bin2hex(random_bytes(12))]);
        $config->setHttpClient($client);
        $config->setDebug(false);

        return $config;
    }

    #[Stancer\WillChange\PHP8_1\NewInInitializers]
    public function mockEmptyJsonResponse(
        Psr\Http\Message\ResponseInterface $response = null
    ): Psr\Http\Message\ResponseInterface
    {
        return $this->mockResponse('{}', $response);
    }

    #[Stancer\WillChange\PHP8_1\NewInInitializers]
    public function mockJsonResponse(
        string $dir,
        string $file,
        Psr\Http\Message\ResponseInterface $response = null
    ): Psr\Http\Message\ResponseInterface
    {
        return $this->mockResponse($this->getFixture($dir, $file), $response);
    }

    #[Stancer\WillChange\PHP8_1\NewInInitializers]
    public function mockJsonResponses(
        array $files,
        Psr\Http\Message\ResponseInterface $response = null
    ): Psr\Http\Message\ResponseInterface
    {
        $resp = $response ?? new mock\Stancer\Http\Response(200);

        foreach ($files as $file) {
            $this->calling($resp)->getBody[] = new Stancer\Http\Stream($this->getFixture(...$file));
        }

        return $resp;
    }

    #[Stancer\WillChange\PHP8_1\NewInInitializers]
    public function mockResponse(
        string $body,
        Psr\Http\Message\ResponseInterface $response = null
    ): Psr\Http\Message\ResponseInterface
    {
        $resp = $response ?? new mock\Stancer\Http\Response(200);

        $this->calling($resp)->getBody = new Stancer\Http\Stream($body);

        return $resp;
    }

    public function mockRequestOptions(Stancer\Config $config, array $more = []): array
    {
        return array_merge([
            'headers' => [
                'Authorization' => $config->getBasicAuthHeader(),
                'Content-Type' => 'application/json',
                'User-Agent' => $config->getDefaultUserAgent(),
            ],
            'timeout' => $config->getTimeout(),
        ], $more);
    }
}
