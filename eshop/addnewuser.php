<?php
include_once "inc/lib.inc.php";
include_once "inc/config.inc.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = clearStr($_POST["login"]);
    $password = clearStr($_POST["password"]);
    if (checkLogin($login)) {
        echo "<p>Такой логин уже существует</p><a href='{$_SERVER["HTTP_REFERER"]}'>Назад</a>";
        exit;
    }
    addNewUser($login, $password);
    echo "<p>Вы зарегистрированы</p><a href='/eshop/'>Каталог</a>";
    exit;
}
header("Location: /eshop/");
