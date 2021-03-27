<?php

require('./includes/constants.php');
require('./includes/functions.php');
require('./includes/template_engine.php');
require('./includes/db_connection.php');

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
]);

mysqli_close($db);