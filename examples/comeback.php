<?php

require_once(__DIR__ . '/bootstrap.php');

var_dump($client->transaction()->sorgu($_GET['oid']));
