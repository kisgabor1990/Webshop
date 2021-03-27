<?php

require './includes/db_connection.php';


if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = 'SELECT * FROM products WHERE id=' . $id . ' LIMIT 1';
    $result = mysqli_query($db, $query);
    $row = mysqli_fetch_assoc($result);
    
    show_template('product_view', [
        'product' => $row,
    ]);
} else {

$query_filter = ''
        . (isset($_GET['brand']) ? ' AND brand IN ("' . implode('","', $_GET['brand']) . '")' : '')
        . (isset($_GET['design']) ? ' AND design IN ("' . implode('","', $_GET['design']) . '")' : '')
        . (isset($_GET['price']) ? ' AND price BETWEEN ' . ($_GET['price']['min'] ?? 0) . ' AND ' . ($_GET['price']['max'] ?? PHP_INT_MAX) : '')
        ;

$query = 'SELECT count(id) AS count FROM categories';
$result = mysqli_query($db, $query);
$row = mysqli_fetch_assoc($result);
$total_categories = (int) $row['count'];
$category_id = (int) ($_GET['category_id'] ?? 1); // items per page
$category_id = min(max(1, $category_id), $total_categories);

$query = 'SELECT name FROM categories WHERE id = '. $category_id;
$result = mysqli_query($db, $query);
$row = mysqli_fetch_assoc($result);
$category_name = $row['name'];

$ipp = (int) ($_GET['ipp'] ?? 6); // items per page
$ipp = $ipp < 1 ? 6 : $ipp;

$query =  'SELECT count(id) AS count '
        . 'FROM products '
        . 'WHERE category_id=' . $category_id 
        . $query_filter;
$result = mysqli_query($db, $query);
$row = mysqli_fetch_assoc($result);
$total_count = $row['count'];
$total_pages = ceil($total_count / $ipp) < 1 ? 1 : ceil($total_count / $ipp);

$page = (int) ($_GET['page'] ?? 1);
$page = min(max(1, $page), $total_pages);

unset($_GET['page']);

$url = 'index.php?' . http_build_query($_GET);

$previousIsDisabled = $page == 1 ? 'disabled' : '';
$nextIsDisabled = $page == $total_pages ? 'disabled' : '';
$previousPages = '';
if ($page == $total_pages && $total_pages > 2) {
    $previousPages .= '<li class="page-item"><a class="is-ajax page-link" href="' . $url . '&page=' . ($page - 2) . '">' . $page - 2 . '</a></li>';
}
if ($page != 1) {
    $previousPages .= '<li class="page-item"><a class="is-ajax page-link" href="' . $url . '&page=' . ($page - 1) . '">' . $page - 1 . '</a></li>';
}
$nextPages = '';
if ($page != $total_pages) {
    $nextPages .= '<li class="page-item"><a class="is-ajax page-link" href="' . $url . '&page=' . ($page + 1) . '">' . $page + 1 . '</a></li>';
}
if ($page == 1 && $total_pages > 2) {
    $nextPages .= '<li class="page-item"><a class="is-ajax page-link" href="' . $url . '&page=' . ($page + 2) . '">' . $page + 2 . '</a></li>';
}



$products = '';
$query = '  SELECT products.*,categories.name AS category_name 
            FROM products 
            LEFT JOIN categories ON categories.id=products.category_id
            WHERE category_id=' . $category_id 
            . $query_filter . ' 
            ORDER BY products.id 
            LIMIT ' . (($page - 1) * $ipp) . ',' . $ipp;
$result = mysqli_query($db, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $products .= parse_template('product', ['product' => $row]);
}


$filter_brands = '';
$query = '  SELECT brand
            FROM products
            WHERE category_id = ' . $category_id . '
            GROUP BY brand';
$result = mysqli_query($db, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $filter_brands .= ' <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="' . $row['brand'] . '" id="brand_' . $row['brand'] . '" name="brand[]" ' . ((isset($_GET['brand']) && in_array($row['brand'], $_GET['brand'])) ? 'checked' : '') . '>
                            <label class="form-check-label" for="brand_' . $row['brand'] . '">
                                ' . $row['brand'] . ' 
                            </label>
                        </div>';
}

$filter_design = '';
$query = '  SELECT design
            FROM products
            WHERE category_id = ' . $category_id . '
            GROUP BY design';
$result = mysqli_query($db, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $filter_design .= ' <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="' . $row['design'] . '" id="design_' . $row['design'] . '" name="design[]" ' . ((isset($_GET['design']) && in_array($row['design'], $_GET['design'])) ? 'checked' : '') . '>
                            <label class="form-check-label" for="design_' . $row['design'] . '">
                                ' . $row['design'] . '
                            </label>
                        </div>';
}

    

show_template('products', [
    'category_name' => $category_name,
    'category_id' => $category_id,
    'products' => $products,
    'url' => $url,
    'total_pages' => $total_pages,
    'page-1' => $page - 1,
    'page+1' => $page + 1,
    'page' => $page,
    'previousIsDisabled' => $previousIsDisabled,
    'nextIsDisabled' => $nextIsDisabled,
    'previousPages' => $previousPages,
    'nextPages' => $nextPages,
    'filter_brands' => $filter_brands,
    'filter_design' => $filter_design,
    'total_count' => $total_count > 0 ? $total_count . ' db. termék!' : 'Nincs találat!',
]);

}