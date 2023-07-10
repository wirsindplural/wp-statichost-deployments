<?php

require_once 'functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php'; 

$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, statichost_deployments_get_webhook_url());
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$data = curl_exec($curl);
curl_close($curl);

print_r($data);

?>

