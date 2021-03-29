<?php

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
