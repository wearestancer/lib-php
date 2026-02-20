<?php

namespace Stancer\Tests\functional;

use atoum;
use Stancer;

/**
 * @internal
 */
class TestCase extends Stancer\Tests\atoum
{
    protected Stancer\Config $config;

    public function __construct(
        ?atoum\atoum\adapter $adapter = null,
        ?atoum\atoum\annotations\extractor $annotationExtractor = null,
        ?atoum\atoum\asserter\generator $asserterGenerator = null,
        ?atoum\atoum\test\assertion\manager $assertionManager = null,
        ?\Closure $reflectionClassFactory = null
    ) {
        parent::__construct($adapter, $annotationExtractor, $asserterGenerator, $assertionManager, $reflectionClassFactory);

        $this->config = Stancer\Config::setGlobal(new Stancer\Config([]));
    }

    public function beforeTestMethod($testMethod, ?Stancer\Enum\ApiVersion $version = null)
    {
        $env = [
            'API_HOST' => '',
            'API_KEY' => '',
            'API_VERSION' => '',
        ];

        foreach ($env as $key => &$value) {
            $value = getenv($key);

            if (!$value) {
                $this->skip('Missing env ' . $key);
            }
        }
        $envVersion = Stancer\Enum\ApiVersion::from($env['API_VERSION']);
        if (!$envVersion) {
            $this->skip('Api version incorrect');
        }

        // We had a check to run test only if we can contact the server.
        $this->config
            ->setKeys($env['API_KEY'])
            ->setHost($env['API_HOST'])
            ->setVersion($version ?? $envVersion)
        ;
        $client = $this->config->getHttpClient();
        $verb = 'get';
        $options['headers']['Authorization'] = $this->config->getBasicAuthHeader();
        $options['headers']['Content-Type'] = 'application/json';
        $options['headers']['User-Agent'] = $this->config->getDefaultUserAgent();
        $version = $this->config->getVersion();
        $location = 'https://' . $this->config->getHost() . '/v' . $version->value . '/cards/?start=1';

        try {
            $client->request($verb, $location, $options);
        } catch (Stancer\Exceptions\ClientException $e) {
            if ($e->getCode() == 401) {
                $this->skip('You don\'t have permission, check your key and host');
            }
        } catch (Stancer\Exceptions\HttpException) {
            $this->skip("The server cannot be contacted, check it's name or your certifications.");
        } catch (\Exception $e) {
            $this->skip('Contacting the server result in a ' . $e->getCode() . 'error, ' . $e->getMessage());
        }
    }

    public function getDisputedCardNumber()
    {
        $cards = [
            '4000000000000259',
            '4000000000001976',
            '4000000000005423',
        ];

        shuffle($cards);

        return array_shift($cards);
    }

    public function getNotFoundExceptionMessage($id, $ressource_name): string
    {
        if ($this->config->version === Stancer\Enum\ApiVersion::VERSION_1) {
            return 'No such ' . strtolower($ressource_name) . ' ' . $id;
        }

        return $ressource_name . ' `' . $id . '` not found';
    }

    public function getValidCardNumber(): string
    {
        $cards = $this->getCardBranded();
        $card = array_merge(...array_values($cards));

        shuffle($card);

        return $card[0];
    }

    public function getValidCardAndNetwork()
    {
        $cardsByNetworks = $this->getCardBranded();
        $networks = array_keys($cardsByNetworks);
        $network = $networks[rand(0, count($networks) - 1)];
        $cards = $cardsByNetworks[$network];
        shuffle($cards);

        return [
            'network' => Stancer\Card\PreferredNetwork::tryFrom($network),
            'card' => $cards[0],
        ];
    }

    public function getCardBranded(): array
    {
        return [
            'visa' => [
                '4000000400000008',
                '4000000560000004',
                '4000002080000001',
                '4000002460000001',
                '4000002760000016',
                '4000003720000005',
                '4000003800000008',
                '4000005280000002',
                '4000005780000007',
                '4000006200000007',
                '4000006430000009',
                '4000007240000007',
                '4000007520000008',
                '4000007560000009',
                '4000008260000000',
                '4242424242424242',
                '4444333322221111',
                '4000000000003055',
                '4000000760000002',
                '4000001240000000',
                '4000004840000008',
            ],
            'mastercard' => [
                '5555555555554444',
                '5200828282828210',
                '5105105105105100',
            ],
            'national' => [
                '4000002500000003',
            ],
        ];
    }

    public function getValidIban()
    {
        $iban = [
            'AT611904300234573201',
            'BE62510007547061',
            'CH2089144321842946678',
            'DE89370400440532013000',
            'EE382200221020145685',
            'ES0700120345030000067890',
            'FI2112345600000785',
            'FR1420041010050500013M02606',
            'GB33BUKB20201555555555',
            'IE29AIBK93115212345678',
            'LT121000011101001000',
            'LU280019400644750000',
            'IT02A0301926102000000490887',
            'NL39RABO0300065264',
            'NO9386011117947',
            'PT50000201231234567890154',
            'SE3550000000054910000003',
        ];

        shuffle($iban);

        return $iban[0];
    }
}
