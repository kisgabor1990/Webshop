<?php

require './includes/db_connection.php';

if (isset($_POST['email'])) {
    $valid = true;
    $error_msg = '';
    if ($_POST['email'] == '') {
        $valid = false;
        $error_msg = 'Az email megadása kötelező!';
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $valid = false;
        $error_msg = 'Az email formátuma nem megfelelő!';
    }
    if ($valid) {
        $query = 'SELECT * FROM users WHERE email="' . $_POST['email'] . '" LIMIT 1';
        $result = mysqli_query($db, $query);
        if ($result->num_rows == 1) {
            $user = mysqli_fetch_assoc($result);

            $recovery_hash = sha1(uniqid(more_entropy: true));
            mysqli_query($db, 'UPDATE users SET recovery_hash="' . $recovery_hash . '" WHERE id=' . $user['id'] . ' LIMIT 1');
            $affected_rows = mysqli_affected_rows($db);
            sendEmail($user['email'], $user['billing_name'], 'Elfelejtett jelszó',
                    parse_template('password_recovery_email', [
                'url' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'],
                'recovery_hash' => $recovery_hash,
            ]));
        } else {
            $error_msg = 'A megadott e-mail cím nem szerepel az adatbázisunkban!';
        }
    }
}

show_template('password_recovery', [
    'alert_message' => isset($valid) ? parse_template('alert_message', [
                'alert_type' => (isset($affected_rows) && $affected_rows) ? 'success' : 'danger',
                'alert_message' => (isset($affected_rows) && $affected_rows) ? 'Sikeres email küldés, kérjük ellenőrizze a postafiókját!' : $error_msg,
            ]) : '',
]);
