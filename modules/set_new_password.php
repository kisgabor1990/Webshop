<?php

require './includes/db_connection.php';

if (isset($_POST['password'])) {
    $valid = true;
    $errors = [];

    if ($_POST['password'] == '') {
        $valid = false;
        $errors[] = 'A jelszó megadása kötelező!';
    }
    if ($_POST['password'] != $_POST['password2']) {
        $valid = false;
        $errors[] = 'A két jelszó nem egyezik!';
    }

    $error_msg = '<ul class="mb-0">';
    if ($valid) {


        $query = 'UPDATE users 
                SET
                    password="' . password_hash($_POST['password'], PASSWORD_BCRYPT) . '",
                    recovery_hash=null 
                WHERE recovery_hash="' . $_POST['recovery_hash'] . '" LIMIT 1';
        $result = mysqli_query($db, $query);
        $affected_rows = mysqli_affected_rows($db);
        $error_msg .= !$affected_rows ? '<li> Belső hiba, kérjük később próbálja újra! </li>' : '';
    } else {

        foreach ($errors as $error) {
            $error_msg .= '<li> ' . $error . ' </li>';
        }
    }
    
    $error_msg .= '</ul>';
}

show_template('set_new_password', [
    'alert_message' => isset($valid) ? parse_template('alert_message', [
                'alert_type' => (isset($affected_rows) && $affected_rows) ? 'success' : 'danger',
                'alert_message' => (isset($affected_rows) && $affected_rows) ? 'Sikeres jelszócsere!' : $error_msg,
            ]) : '',
    'recovery_hash' => $_GET['recovery_hash'] ?? 0,
]);
