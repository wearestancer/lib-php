<?php

namespace ild78\Tests\Provider;

trait Network
{
    public function ipv4DataProvider()
    {
        $data = [];

        $data[] = '212.27.48.10'; // www.free.fr

        $data[] = '216.58.206.238'; // www.google.com

        $data[] = '17.178.96.59'; // www.apple.com
        $data[] = '17.142.160.59'; // www.apple.com
        $data[] = '17.172.224.47'; // www.apple.com

        $data[] = '179.60.192.36'; // www.facebook.com

        $data[] = '198.41.0.4'; // a.root-servers.org

        shuffle($data);

        return $data;
    }

    public function ipv6DataProvider()
    {
        $data = [];

        $data[] = '2a01:0e0c:0001:0000:0000:0000:0000:0001'; // www.free.fr
        $data[] = '2a01:e0c:1:0:0:0:0:1'; // www.free.fr
        $data[] = '2a01:e0c:1::1'; // www.free.fr

        $data[] = '2a00:1450:4007:080f:0000:0000:0000:200e'; // www.google.com
        $data[] = '2a00:1450:4007:80f::200e'; // www.google.com

        $data[] = '2a03:2880:f11f:0083:face:b00c:0000:25de'; // www.facebook.com
        $data[] = '2a03:2880:f11f:83:face:b00c:0:25de'; // www.facebook.com

        $data[] = '2001:0503:ba3e:0000:0000:0000:0002:0030'; // a.root-servers.org
        $data[] = '2001:503:ba3e::2:30'; // a.root-servers.org

        shuffle($data);

        return $data;
    }
}
