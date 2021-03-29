<?php

require('./includes/db_connection.php');

if (isLoggedIn()) {
    header('Location: index.php?module=home');
}

if (isset($_POST) && isset($_POST['email'])) {
    $valid = true;
    $errors = [];
    $query = 'SELECT * FROM users WHERE email="' . $_POST['email'] . '" LIMIT 1';
    $result = mysqli_query($db, $query);
    if ($result->num_rows == 1) {
        $valid = false;
        $errors[] = 'Ezzel az email címmel már regisztráltak!';
    }
    if ($_POST['email'] == '') {
        $valid = false;
        $errors[] = 'Az email megadása kötelező!';
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $valid = false;
        $errors[] = 'Az email formátuma nem megfelelő!';
    }
    if ($_POST['password'] == '') {
        $valid = false;
        $errors[] = 'A jelszó megadása kötelező!';
    }
    if ($_POST['password'] != $_POST['password2']) {
        $valid = false;
        $errors[] = 'A két jelszó nem egyezik!';
    }
    if ($_POST['phone'] == '') {
        $valid = false;
        $errors[] = 'A telefonszám megadása kötelező!';
    }
    if ($_POST['billing-name'] == '') {
        $valid = false;
        $errors[] = 'A számlázási név megadása kötelező!';
    }
    if ($_POST['choose_company'] == 'is-company' && $_POST['billing-taxnum'] == '') {
        $valid = false;
        $errors[] = 'Cég esetén az adószám megadása kötelező!';
    }
    if ($_POST['billing-city'] == '') {
        $valid = false;
        $errors[] = 'A számlázási város megadása kötelező!';
    }
    if ($_POST['billing-address'] == '') {
        $valid = false;
        $errors[] = 'A számlázási cím megadása kötelező!';
    }
    if ($_POST['billing-zip'] == '') {
        $valid = false;
        $errors[] = 'A számlázási irányítószám megadása kötelező!';
    }
    if (!isset($_POST['shipping_same']) && $_POST['shipping-name'] == '') {
        $valid = false;
        $errors[] = 'A szállítási név megadása kötelező!';
    }

    if (!isset($_POST['shipping_same']) && $_POST['shipping-city'] == '') {
        $valid = false;
        $errors[] = 'A szállítási város megadása kötelező!';
    }
    if (!isset($_POST['shipping_same']) && $_POST['shipping-address'] == '') {
        $valid = false;
        $errors[] = 'A szállítási cím megadása kötelező!';
    }
    if (!isset($_POST['shipping_same']) && $_POST['shipping-zip'] == '') {
        $valid = false;
        $errors[] = 'A szállítási irányítószám megadása kötelező!';
    }
    if (!isset($_POST['aszf'])) {
        $valid = false;
        $errors[] = 'Az ászf elfogadása kötelező!';
    }

    if ($valid) {
        $query = 'INSERT INTO `users` SET
        `email`="' . htmlspecialchars($_POST['email']) . '",
        `password`="' . password_hash($_POST['password'], PASSWORD_BCRYPT) . '",
        `choose_company`="' . $_POST['choose_company'] . '",
        `phone`="+36' . $_POST['phone'] . '",
        `billing_name`="' . htmlspecialchars($_POST['billing-name']) . '",
        `billing_taxnum`="' . ($_Post['billing-taxnum'] ?? null) . '",
        `billing_city`="' . htmlspecialchars($_POST['billing-city']) . '",
        `billing_address`="' . htmlspecialchars($_POST['billing-address']) . '",
        `billing_zip`="' . htmlspecialchars($_POST['billing-zip']) . '",
        `shipping_name`="' . htmlspecialchars($_POST['shipping-name'] ?? $_POST['billing-name']) . '",
        `shipping_city`="' . htmlspecialchars($_POST['shipping-city'] ?? $_POST['billing-city']) . '",
        `shipping_address`="' . htmlspecialchars($_POST['shipping-address'] ?? $_POST['billing-address']) . '",
        `shipping_zip`="' . htmlspecialchars($_POST['shipping-zip'] ?? $_POST['billing-zip']) . '"';

        $result = mysqli_query($db, $query);
        
    } else {

        $error_msg = '<ul class="mb-0">';
        foreach ($errors as $error) {
            $error_msg .= '<li> ' . $error . ' </li>';
        }
        $error_msg .= '</ul>';
    }
}

show_template('regisztracio', [
    'alert_message' => isset($valid) ? parse_template('alert_message', [
                'alert_type' => $valid ? 'success' : 'danger',
                'alert_message' => $valid ? 'Sikeres regisztráció!' : $error_msg,
            ]) : '',
    'email' => (isset($valid) && !$valid) ? $_POST['email'] : '',
    'is-person' => (isset($valid) && !$valid && $_POST['choose_company'] == 'is-person') || (!isset($valid) && !isset($_POST['choose-company'])) ? 'checked' : '',
    'is-company' => (isset($valid) && !$valid && $_POST['choose_company'] == 'is-company') ? 'checked' : '',
    'phone' => (isset($valid) && !$valid) ? $_POST['phone'] : '',
    'billing-name' => (isset($valid) && !$valid) ? $_POST['billing-name'] : '',
    'billing-taxnum' => (isset($valid) && !$valid && $_POST['choose_company'] == 'is-company') ? $_POST['billing-taxnum'] : '',
    'billing-taxnum-hidden' => (!isset($valid) && !isset($_POST['choose_company'])) || (isset($valid) && !$valid && $_POST['choose_company'] == 'is-person') ? 'style="display: none"' : '',
    'billing-taxnum-disabled' => (isset($valid) && !$valid && $_POST['choose_company'] == 'is-person') ? 'disabled' : '',
    'billing-city' => (isset($valid) && !$valid) ? $_POST['billing-city'] : '',
    'billing-address' => (isset($valid) && !$valid) ? $_POST['billing-address'] : '',
    'billing-zip' => (isset($valid) && !$valid) ? $_POST['billing-zip'] : '',
    'shipping_same' => (!isset($valid) && !isset($_POST['shipping_same'])) || (isset($valid) && !$valid && isset($_POST['shipping_same'])) ? 'checked' : '',
    'shipping_same_hidden' => (!isset($valid) && !isset($_POST['shipping_same'])) || (isset($valid) && !$valid && isset($_POST['shipping_same'])) ? 'style="display: none"' : '',
    'shipping_same_disabled' => (!isset($valid) && !isset($_POST['shipping_same'])) || (isset($valid) && !$valid && isset($_POST['shipping_same'])) ? 'disabled' : '',
    'shipping-name' => (isset($valid) && !$valid && isset($_POST['shipping-name'])) ? $_POST['shipping-name'] : '',
    'shipping-city' => (isset($valid) && !$valid && isset($_POST['shipping-city'])) ? $_POST['shipping-city'] : '',
    'shipping-address' => (isset($valid) && !$valid && isset($_POST['shipping-address'])) ? $_POST['shipping-address'] : '',
    'shipping-zip' => (isset($valid) && !$valid && isset($_POST['shipping-zip'])) ? $_POST['shipping-zip'] : '',
]);
