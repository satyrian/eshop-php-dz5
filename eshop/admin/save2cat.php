<?php
// подключение библиотек
require "../inc/lib.inc.php";
require "../inc/config.inc.php";
require "secure/session.inc.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_FILES["image"]["error"] != UPLOAD_ERR_OK) {
        echo "При загрузке файла произошла ошибка";
        exit();
    }
    $title = clearStr($_POST['title']);
    $author = clearStr($_POST['author']);
    $pubyear = clearInt($_POST['pubyear']);
    $price = clearInt($_POST['price']);

    $id = addItemToCatalog($title, $author, $pubyear, $price);
    if ($id) {
        if (is_uploaded_file($_FILES["image"]["tmp_name"])) {
            move_uploaded_file($_FILES["image"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . "/eshop/uploads/collections/$id.jpg");
        }
        header("Location: add2cat.php");
    } else {
        echo "Произошла ошибка при добавлении товара";
    }

} else {
    header('Location: add2cat.php');
}

