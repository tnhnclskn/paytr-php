<?php

namespace Tnhnclskn\PayTR;

class TransactionAPI extends BaseAPI
{
    public function sorgu(string $merchantOid)
    {
        $data = [
            'merchant_id' => $this->paytr()->merchantId,
            'merchant_oid' => $merchantOid,
        ];
        $data['paytr_token'] = $this->sorguToken($data);

        $request = $this->paytr()->post('odeme/durum-sorgu', $data);

        return $request;
    }

    public function sorguToken(array $data): string
    {
        return $this->paytr()->takeToken($data, [
            'merchant_id', 'merchant_oid', 'merchant_salt'
        ]);
    }

    public function iade(string $merchantOid, float $returnAmount, ?string $referenceNo = null)
    {
        $data = [
            'merchant_id' => $this->paytr()->merchantId,
            'merchant_oid' => $merchantOid,
            'return_amount' => (int) ($returnAmount * 100),
            'reference_no' => $referenceNo,
        ];
        $data['paytr_token'] = $this->iadeToken($data);
        $request = $this->paytr()->post('odeme/iade', $data);

        return $request;
    }

    public function iadeToken(array $data): string
    {
        return $this->paytr()->takeToken($data, [
            'merchant_id', 'merchant_oid', 'return_amount', 'merchant_salt', 'merchant_key'
        ]);
    }
}
