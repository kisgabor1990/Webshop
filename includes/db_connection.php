<?php

$db = @mysqli_connect(
    '127.0.0.1',
    'gabor',
    '12345',
    'webshop');

if (mysqli_connect_error()) {
    header('Location: maintenance.php');
    exit();
}