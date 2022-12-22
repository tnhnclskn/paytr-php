<?php

require_once(__DIR__ . '/bootstrap.php');

$result = $client->iframe()->check($_POST);

echo 'OK';
