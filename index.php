<?php

require('./includes/common.php');

$categories = getCategories($db);
$menu = getMenu();
$content = getModule();
$newest = getNewest($db);

show_template('layout', [
    'menu_navbar' => $menu['menu_navbar'],
    'menu_sidebar' => $menu['menu_sidebar'],
    'menu_bottom' => $menu['menu_bottom'],
    'categories_navbar' => $categories['categories_navbar'],
    'categories_sidebar' => $categories['categories_sidebar'],
    'content' => $content,
    'newest' => $newest,
    'profil' => isLoggedIn() ? parse_template('profil', [
        'name' => $_SESSION['name'],
    ]) : parse_template('login', []),
]);

mysqli_close($db);