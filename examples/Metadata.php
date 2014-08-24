<?php

include_once '../lib/DropboxClient.php';

$accessToken = '';

$client = new DropboxClient($accessToken);
$response = $client->metadata('/Photos');

print_r($response);
