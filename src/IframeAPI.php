<?php

namespace Tnhnclskn\PayTR;

class IframeAPI extends BaseAPI
{
    const IFRAME_CHECK_ERRORS = [
        '1' => 'Kimlik Doğrulama yapılmadı. Lütfen tekrar deneyin ve işlemi tamamlayın. Müşteri, kimlik doğrulama adımında cep telefonu numarasını girmedi.',
        '2' => 'Kimlik Doğrulama başarısız. Lütfen tekrar deneyin ve şifreyi doğru girin. Müşteri, cep telefonuna gelen şifreyi doğru girmedi.',
        '3' => 'Güvenlik kontrolü sonrası onay verilmedi veya kontrol yapılamadı. Müşterinin işlemi PayTR tarafından güvenlik kontrolünden geçemedi veya kontrol yapılamadı.',
        '6' => 'Müşteri ödeme yapmaktan vazgeçti ve ödeme sayfasından ayrıldı. Müşteri, kendisine tanınmış olan işlem süresinde (1.ADIM’da tanımlanan timeout_limit değeri) işlemini tamamlamadı veya müşteri ödeme sayfasını kapatarak işlemi sonlandırdı.',
        '8' => 'Bu karta taksit yapılamamaktadır. Müşterinin kullanmakta olduğu kart ile seçmiş olduğu taksitli ödeme yöntemi kullanılamaz.',
        '9' => 'Bu kart ile işlem yetkisi bulunmamaktadır. Müşterinin kullanmakta olduğu kart için mağazanızın işlem yetkisi bulunmuyor.',
        '10' => 'Bu işlemde 3D Secure kullanılmalıdır. Müşteri, yapmış olduğu işlemde 3D Secure ile ödeme yapmalıdır.',
        '11' => 'Güvenlik uyarısı. İşlem yapan müşterinizi kontrol edin. Müşterinin işleminde fraud tespiti bulunuyor. Güvenliğiniz için müşterinin işlemlerini kontrol edin.',
        '99' => 'İşlem başarısız: Teknik entegrasyon hatası. Teknik entegrasyon hatası varsa dönülecektir. (debug_on değeri 0 ise)',
    ];

    public function pay(array $data): string
    {
        $data = [
            'merchant_id' => $this->paytr()->merchantId,
            'user_ip' => $data['user_ip'],
            'merchant_oid' => $data['merchant_oid'],
            'email' => $data['email'],
            'payment_amount' => (int) ($data['payment_amount'] * 100),
            'currency' => $data['currency'],
            'user_basket' => base64_encode(json_encode($data['user_basket'])),
            'no_installment' => $data['no_installment'],
            'max_installment' => $data['max_installment'],
            'user_name' => $data['user_name'],
            'user_address' => $data['user_address'],
            'user_phone' => $data['user_phone'],
            'merchant_ok_url' => $data['merchant_ok_url'],
            'merchant_fail_url' => $data['merchant_fail_url'],
            'debug_on' => $data['debug_on'],
            'timeout_limit' => $data['timeout_limit'],
            'lang' => $data['lang'],
        ];
        $data['paytr_token'] = $this->payToken($data);

        $response = $this->paytr()->post('/odeme/api/get-token', $data);

        return "https://www.paytr.com/odeme/guvenli/{$response['token']}";
    }

    private function payToken(array $data): string
    {
        return $this->paytr()->takeToken($data, [
            'merchant_id', 'user_ip', 'merchant_oid', 'email', 'payment_amount', 'user_basket', 'no_installment', 'max_installment', 'currency', 'test_mode', 'merchant_salt'
        ]);
    }

    public function check(array $data): array
    {
        $data = array_filter($data, function ($key) {
            return in_array($key, ['merchant_oid', 'status', 'total_amount', 'hash', 'failed_reason_code', 'failed_reason_msg', 'test_mode', 'payment_type', 'currency', 'payment_amount']);
        }, ARRAY_FILTER_USE_KEY);
        $token = $this->checkToken($data);

        if ($token != $data['hash']) {
            throw new PayTRException('Token mismatch');
        }

        if ($data['status'] != "success") {
            if ($data['failed_reason_code'] != '0' && in_array($data['failed_reason_code'], self::IFRAME_CHECK_ERRORS)) {
                throw new PayTRException(self::IFRAME_CHECK_ERRORS[$data['failed_reason_code']]);
            } else {
                throw new PayTRException($data['failed_reason_msg']);
            }
        }

        return $data;
    }

    private function checkToken(array $data): string
    {
        return $this->paytr()->takeToken($data, [
            'merchant_oid', 'merchant_salt', 'status', 'total_amount'
        ]);
    }
}
