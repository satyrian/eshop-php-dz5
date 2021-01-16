<?php
include_once "inc/lib.inc.php";
include_once "inc/config.inc.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_GET["id"])) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
    $id = clearInt($_GET["id"]);
    if (!getItemById($id)) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
    $name = clearStr($_POST["name"]);
    $email = clearStr($_POST["email"]);
    $comment = trim(strip_tags($_POST["comment"]));
    addCommentToItem($id, $name, $email, $comment);
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit;
}
header("Location: /eshop/");