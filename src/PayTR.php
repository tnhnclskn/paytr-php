<?php

namespace Tnhnclskn\PayTR;

use GuzzleHttp\Client;

class PayTR
{
    private ?Client $client = null;

    public string $merchantId;
    public string $merchantKey;
    public string $merchantSalt;
    public bool $testMode;

    public function __construct(string $merchantId, string $merchantKey, string $merchantSalt, bool $testMode = false)
    {
        $this->merchantId = $merchantId;
        $this->merchantKey = $merchantKey;
        $this->merchantSalt = $merchantSalt;
        $this->testMode = $testMode;
    }

    protected function client(): Client
    {
        if (!$this->client) {
            $this->client = new Client([
                'base_uri' => 'https://www.paytr.com',
                'timeout' => 10.0,
            ]);
        }

        return $this->client;
    }

    public function request($method, $url, $data = []): array
    {
        $data['test_mode'] = $this->testMode ? 1 : 0;

        $request = $this->client()->request($method, $url, [
            ($method == 'GET' ? 'query' : 'form_params') => $data,
        ]);

        $response = json_decode($request->getBody()->getContents(), true);

        if (isset($response['status']) && $response['status'] == 'failed') {
            dd($response);
            throw new PayTRException($response['reason']);
        }

        return $response;
    }

    public function get($url, $data = []): array
    {
        return $this->request('GET', $url, $data);
    }

    public function post($url, $data = []): array
    {
        return $this->request('POST', $url, $data);
    }

    public function takeToken(array $data, array $keys): string
    {
        $data['merchant_id'] = $this->merchantId;
        $data['merchant_key'] = $this->merchantKey;
        $data['merchant_salt'] = $this->merchantSalt;
        $data['test_mode'] = $this->testMode ? 1 : 0;

        $hashString = implode('', array_map(function ($key) use ($data) {
            return $data[$key] ?? '';
        }, $keys));

        return base64_encode(hash_hmac('sha256', $hashString, $this->merchantKey, true));
    }

    public function iframe(): IframeAPI
    {
        return new IframeAPI($this);
    }

    public function transaction(): TransactionAPI
    {
        return new TransactionAPI($this);
    }
}
