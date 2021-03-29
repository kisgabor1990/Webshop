<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

function isLoggedIn(): bool
{
    return isset($_SESSION['name']);
}

function getModule() {
    ob_start();
    $module = $_GET['module'] ?? 'home';
    if (
            in_array($module, AJAX_MODULES) &&
            is_file('modules/' . $module . '.php') &&
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'
    ) {
        include('modules/' . (is_file('modules/' . $module . '.php') ? $module : '404') . '.php');
        exit;
    }

    include('modules/' . (is_file('modules/' . $module . '.php') ? $module : '404') . '.php');
    return ob_get_clean();
}

function getCategories($db) {
    $categories_navbar = '';
    $categories_sidebar = '';
    $query = 'SELECT * FROM categories ORDER BY id';
    $result = mysqli_query($db, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $url = 'index.php?module=products&category_id=' . $row['id'];
        $categories_navbar .= '<a class="nav-link is-ajax nav-category' . $row['id'] . '" href="' . $url . '">' . $row['name'] . '</a>';
        $categories_sidebar .= '<a class="btn is-ajax nav-category' . $row['id'] . '" href="' . $url . '">' . $row['name'] . '</a>';
    }
    return [
        'categories_navbar' => $categories_navbar,
        'categories_sidebar' => $categories_sidebar,
    ];
}

function getNewest($db) {
    $newest = '';
    $query = '  SELECT products.*,categories.name AS category_name 
            FROM products 
            LEFT JOIN categories ON categories.id=products.category_id
            ORDER BY products.id DESC 
            LIMIT 8';
    $result = mysqli_query($db, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $newest .= parse_template('product', ['product' => $row]);
    }
    return $newest;
}

function getMenu() {
    $menu_navbar = '';
    $menu_sidebar = '';
    $menu_bottom = '';

    foreach (MENU as $key => $menuitem) {
        $menu_navbar .= '<li class="nav-item"><a class="nav-link is-ajax nav-' . $key . '" href="index.php?module=' . $key . '">' . $menuitem . '</a></li>';
        $menu_sidebar .= '<a class="nav-link is-ajax nav-' . $key . '" href="index.php?module=' . $key . '">' . $menuitem . '</a>';
        $menu_bottom .= '<p><a href="index.php?module=' . $key . '" class="is-ajax nav-' . $key . '">' . $menuitem . '</a></p>';
    }

    return [
        'menu_navbar' => $menu_navbar,
        'menu_sidebar' => $menu_sidebar,
        'menu_bottom' => $menu_bottom,
    ];
}

function sendEmail(string $address_email, string $address_name, string $subject, string $content) {
    $mail = new PHPMailer(true);

    $mail->CharSet = PHPMailer::CHARSET_UTF8;
    $mail->SMTPDebug = SMTP::DEBUG_OFF;                         //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host = 'smtp.mailtrap.io';                           //Set the SMTP server to send through
    $mail->SMTPAuth = true;                                     //Enable SMTP authentication
    $mail->Username = '0ac58cc5ed12a0';                         //SMTP username
    $mail->Password = 'e955c18ac44e7b';                         //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port = 2525;                                         //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    $mail->setFrom('noreplay@webhop.com', 'Webhop Webshop');
    $mail->addAddress($address_email, $address_name);           //Add a recipient

    //Content
    $mail->isHTML(true);                                        //Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body = $content;

    $mail->send();
}