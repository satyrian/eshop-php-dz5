<?php
require "inc/lib.inc.php";
require "inc/config.inc.php";
/*Получение id товара для добавления в корзину*/
if (isset($_GET['id'])) {
    $id = clearInt($_GET['id']);
    if ($id) {
        add2Basket($id);
    }
    header('Location: /eshop/');
    exit;
}

if (isset($_GET["mr"])) {
    $id = clearInt($_GET["mr"]);
    if ($id) {
        add2Basket($id);
    }
    header("Location: /eshop/basket.php");
    exit();
}