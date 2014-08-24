<?php

include_once '../lib/DropboxClient.php';

$accessToken = '';

$client = new DropboxClient($accessToken);
$response = $client->delete('/Migrations');

print_r($response);
