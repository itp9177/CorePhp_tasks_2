<?php

namespace App\code;

use GuzzleHttp\Client;

class ShippingApi
{

    private const CREDENTIALS ='77qn9aax-qrrm-idki:lnh0-fm2nhmp0yca7';
    private $client;

    function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.printful.com/',
            'headers' => ['Authorization' => 'Basic ' . base64_encode(self::CREDENTIALS)]
        ]);
    }

    public function getRates($request)
    {
        $response = $this->client->request('POST', '/shipping/rates', ['body' => $request]);
        $data = json_decode($response->getBody()->getContents(), true);

        return ($data['code'] == 200) ? $data['result']['0'] : NULL;

    }
}