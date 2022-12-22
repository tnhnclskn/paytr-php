<?php

use Tnhnclskn\PayTR\PayTR;

require_once(__DIR__ . '/../vendor/autoload.php');

$merchantId = 'Merchant ID';
$merchantKey = 'Merchant Key';
$merchantSalt = 'Merchant Salt';
$testMode = true;

$client = new PayTR($merchantId, $merchantKey, $merchantSalt, $testMode);
