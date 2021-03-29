<?php

require('./includes/db_connection.php');

$query = 'SELECT * FROM users WHERE email="' . $_POST['email'] . '" LIMIT 1';
$result = mysqli_query($db, $query);

if (($row = mysqli_fetch_assoc($result)) && password_verify($_POST['password'], $row['password'])) {
    $_SESSION['id'] = $row['id'];
    $_SESSION['name'] = $row['billing_name'];
    $_SESSION['email'] = $row['email'];

//    mergeCart($db);
//    clearCart();
}
header('Location: index.php');