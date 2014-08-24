<?php

include_once '../lib/DropboxClient.php';

$accessToken = '';

$client = new DropboxClient($accessToken);

$file = fopen('./bmw.jpg', 'rb');
$response = $client->uploadFile('/Photos/Car.jpg', $file );
fclose($file);

print_r($response);
