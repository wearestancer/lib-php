<?php

namespace ild78\Tests\Provider;

trait Banks
{
    public function bicDataProvider()
    {
        // Thanks Wikipedia
        $data = [
            'DEUTDEFF',
            'DEUTDEFFXXX',
            'DEUTGBFFA23',
            'DEUTDEFF500',
            'UKCBUau102v',
            'UKIOLT2XXXX',
            'GBMCMRMRXXX',
            'gbtxus31xxx',
            'JUBIGB21XXX',
        ];

        shuffle($data);

        return $data;
    }

    public function ibanDataProvider()
    {
        // Thanks Wikipedia
        $data = [
            'BE71 0961 2345 6769',
            'FR76 3000 6000 0112 3456 7890 189',
            'DE91 1000 0000 0123 4567 89',
            'GR9608100010000001234567890',
            'RO09 BCYP 0000 0012 3456 7890',
            'SA4420000001234567891234',
            'ES79 2100 0813 6101 2345 6789',
            'CH56 0483 5012 3456 7800 9 ',
            'GB98 MIDL 0700 9312 3456 78',
            'GB82WEST12345698765432',
        ];

        shuffle($data);

        return $data;
    }
}
