<?php

require_once(__DIR__ . '/bootstrap.php');

// You can create your own merchant oid format
$merchantOid = 'TEST' . rand(1000, 9999) . substr(md5(time()), 0, 6);

// You can take the iframe link from here
$iframeLink = $client->iframe()->pay([
    'user_ip' => '127.0.0.1',
    'merchant_oid' => $merchantOid,
    'email' => 'mail@tunahancaliskan.com',
    'payment_amount' => 10.35,
    'currency' => 'TRY',
    'user_basket' => [
        ['Ürün 1', 10.35, 1],
    ],
    'no_installment' => 0,
    'max_installment' => 0,
    'user_name' => 'Tunahan Çalışkan',
    'user_address' => 'Turkiye',
    'user_phone' => '+905300000000',
    'merchant_ok_url' => 'https://example.com/comeback.php?oid=' . $merchantOid,
    'merchant_fail_url' => 'https://example.com/comeback.php?oid=' . $merchantOid,
    'debug_on' => 1,
    'timeout_limit' => 15,
    'lang' => 'tr',
]);

?>
<script src="https://www.paytr.com/js/iframeResizer.min.js"></script>
<h1><?= $merchantOid ?></h1>
<iframe src="<?= $iframeLink ?>" id="paytriframe" frameborder="0" scrolling="no" style="width: 100%;"></iframe>
<script>
    iFrameResize({}, '#paytriframe');
</script>