<?php

namespace ild78\Tests\Provider;

trait Cards
{
    public function brandDataProvider()
    {
        $random = uniqid();

        $data = [
            ['visa', 'VISA'],
            ['mastercard', 'MasterCard'],
            ['amex', 'American Express'],
            ['jcb', 'JCB'],
            ['maestro', 'Maestro'],
            ['discover', 'Discover'],
            ['dankort', 'Dankort'],
            [$random, $random],
        ];

        shuffle($data);

        return $data;
    }

    public function cardNumberDataProvider($one = false)
    {
        // Card number found on https://www.freeformatter.com/credit-card-number-generator-validator.html
        $data = [];

        // VISA
        $data[] = '4532160583905253';
        $data[] = '4103344114503410';
        $data[] = '4716929813250776300';

        // MasterCard
        $data[] = '5312580044202748';
        $data[] = '2720995588028031';
        $data[] = '5217849688268117';

        // American Express (AMEX)
        $data[] = '370301138747716';
        $data[] = '340563568138644';
        $data[] = '371461161518951';

        // Discover
        $data[] = '6011651456571367';
        $data[] = '6011170656779399';
        $data[] = '6011693048292929421';

        // JCB
        $data[] = '3532433013111566';
        $data[] = '3544337258139297';
        $data[] = '3535502591342895821';

        // Diners Club - North America
        $data[] = '5480649643931654';
        $data[] = '5519243149714783';
        $data[] = '5509141180527803';

        // Diners Club - Carte Blanche
        $data[] = '30267133988393';
        $data[] = '30089013015810';
        $data[] = '30109478108973';

        // Diners Club - International
        $data[] = '36052879958170';
        $data[] = '36049904526204';
        $data[] = '36768208048819';

        // Maestro
        $data[] = '5893433915020244';
        $data[] = '6759761854174320';
        $data[] = '6759998953884124';

        // Visa Electron
        $data[] = '4026291468019846';
        $data[] = '4844059039871494';
        $data[] = '4913054050962393';

        // InstaPayment
        $data[] = '6385037148943057';
        $data[] = '6380659492219803';
        $data[] = '6381454097795863';

        // Classic one
        $data[] = '4111111111111111';
        $data[] = '4242424242424242';
        $data[] = '4444333322221111';

        shuffle($data);

        if ($one) {
            return $data[0];
        }

        return $data;
    }
}
