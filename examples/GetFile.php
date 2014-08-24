<?php

include_once '../lib/DropboxClient.php';

$accessToken = '';

$client = new DropboxClient($accessToken);

$file = fopen('./Car.jpg', 'wb');
$response = $client->getFile('/Photos/BMW.jpg', $file);
fclose($file);

print_r($response);
