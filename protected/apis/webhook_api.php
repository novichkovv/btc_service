<?php
class webhook_api extends GuzzleHttp\Client
{
    public function __construct()
    {
        parent::__construct([
            'base_uri' => WEBHOOK_API_URL,
            'verify' => false
        ]);
    }

    public function balance(string $symbol, string $adderss, string $amount)
    {
        $res = $this->post('webhook/', [
            'json' => [
                'symbol' => $symbol,
                'address' => $adderss,
                'amount' => $amount
            ]
        ]);
        $contents = $res->getBody()->getContents();
        if($arr = json_decode($contents, true)) {
            return $arr;
        }
        throw new Exception('Invalid Response Body - ' . $contents, 123);
    }
}