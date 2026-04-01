<?php

namespace Stancer\Tests;

use atoum\atoum as base;
use Faker;
use mock;
use Psr;
use Stancer;

class atoum extends base\test
{
    public function __construct(
        ?base\adapter $adapter = null,
        ?base\annotations\extractor $annotationExtractor = null,
        ?base\asserter\generator $asserterGenerator = null,
        ?base\test\assertion\manager $assertionManager = null,
        ?\Closure $reflectionClassFactory = null
    ) {
        parent::__construct($adapter, $annotationExtractor, $asserterGenerator, $assertionManager, $reflectionClassFactory);

        $this->getAsserterGenerator()->addNamespace('Stancer\\Tests\\asserters');
    }

    public function beforeTestMethod($method)
    {
        $env = getenv('API_VERSION');
        if ($env && $tmp = Stancer\Enum\ApiVersion::from($env)) {
            $version = $tmp;
        } else {
            $version = Stancer\Enum\ApiVersion::VERSION_1;
        }
        if ($method !== 'testGetGlobal_SetGlobal') {
            Stancer\Config::init(['stest_' . $this->getRandomString(24)])
                ->setVersion($version)
            ;
        }
    }

    public function choose(array $items, array $exclude = []): mixed
    {
        $item = $items[array_rand($items)];

        if (in_array($item, $exclude, true)) {
            return $this->choose($items, $exclude);
        }

        return $item;
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

    /**
     * We set 50 as a min.
     *
     * if we want to fail the payment we will not use this function
     *
     * @param integer $max
     */
    public function getRandomAmount(int $max = 1000): int
    {
        return rand(50, $max);
    }

    public function getRandomCvc(): string
    {
        return (string) str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    }

    public function getRandomDate(int $min, ?int $max = null): string
    {
        $year = $this->getRandomYear($min, $max);
        $month = $this->getRandomMonth();
        $day = $this->getRandomDay($month);

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    public function getRandomDay(int $month): int
    {
        $dMax = 31;
        if ($month == 2) {
            $dMax = 27;
        } elseif (in_array($month, [4, 6, 9, 11])) {
            $dMax = 30;
        }

        return rand(1, $dMax);
    }

    public function getRandomExpYear()
    {
        return $this->getRandomYear(date('Y') + 1, date('Y') + 30);
    }

    public function getRandomMonth(): int
    {
        return rand(1, 12);
    }

    public function getRandomNumber(): string
    {
        // Simulate a french mobile phone number
        $first = rand(0, 1) + 6;
        $loop = 3;

        $number = '+33' . $first;

        if ($first === 7) {
            $number .= str_pad(rand(30, 99), 2, '0');
        }
        if ($first === 6) {
            $nine_list = ['5', '8', '9'];
            $first_number_duo = [
                str_pad((string) rand(0, 20), 2, '0'),
                (string) rand(40, 80),
                '3' . (string) rand(0, 8),
                '9' . $nine_list[rand(0, 2)],
            ];
            $number .= $first_number_duo[rand(0, 3)];
        }

        for ($idx = 0; $idx < $loop; $idx++) {
            $number .= str_pad(rand(0, 99), 2, '0');
        }

        return $number;
    }

    public function getRandomString(int $min, ?int $max = null): string
    {
        if (!$max) {
            $max = $min;
        }

        $len = rand($min, $max);

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

    public function getRandomYear(int $min, ?int $max = null): int
    {
        if (!$max) {
            $max = date('Y');
        }

        return rand($min, $max);
    }

    public function getUuid(): string
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0F | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3F | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * @param mock\Psr\Http\Client\ClientInterface|Psr\Http\Client\ClientInterface $client
     * @param \Stancer\Enum\ApiVersion $version
     */
    public function mockConfig($client, Stancer\Enum\ApiVersion $version = Stancer\Enum\ApiVersion::VERSION_1): Stancer\Config
    {
        $config = Stancer\Config::init(['stest_' . $this->getRandomString(24)]);
        $config->setHttpClient($client);
        $config->setDebug(false);
        $config->setVersion($version);

        return $config;
    }

    public function mockEmptyJsonResponse(
        Psr\Http\Message\ResponseInterface $response = new mock\Stancer\Http\Response(200)
    ): Psr\Http\Message\ResponseInterface {
        return $this->mockResponse('{}', $response);
    }

    public function mockJsonResponse(
        string $dir,
        string $file,
        Psr\Http\Message\ResponseInterface $response = new mock\Stancer\Http\Response(200)
    ): Psr\Http\Message\ResponseInterface {
        return $this->mockResponse($this->getFixture($dir, $file), $response);
    }

    public function mockJsonResponses(
        array $files,
        Psr\Http\Message\ResponseInterface $response = new mock\Stancer\Http\Response(200)
    ): Psr\Http\Message\ResponseInterface {
        foreach ($files as $file) {
            $this->calling($response)->getBody[] = new Stancer\Http\Stream($this->getFixture(...$file));
        }

        return $response;
    }

    public function mockResponse(
        string $body,
        Psr\Http\Message\ResponseInterface $response = new mock\Stancer\Http\Response(200)
    ): Psr\Http\Message\ResponseInterface {
        $this->calling($response)->getBody = new Stancer\Http\Stream($body);

        return $response;
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

    public function versionDataProvider()
    {
        return [
            [Stancer\Enum\ApiVersion::VERSION_1],
            [Stancer\Enum\ApiVersion::VERSION_2],
        ];
    }
}
