<?php
include_once "inc/lib.inc.php";
include_once "inc/config.inc.php";

$title = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = clearStr($_POST["login"]);
    $password = clearStr($_POST["password"]);
    if ($login and $password) {
        if (checkLogin($login)) {
            if (userLogin($login, $password)) {
                header("Location: /eshop/");
                exit();
            } else {
                $title = "Неправильный логин или пароль";
            }
        } else {
            $title = "Неправильный логин или пароль";
        }
    } else {
        $title = "Заполните все поля формы!";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Вход</title>
</head>
<body>
<h1><?= $title ?></h1>
<form action="<?= $_SERVER["REQUEST_URI"] ?>" method="post">
    <p>Логин:</p>
    <input type="text" name="login">
    <p>Пароль:</p>
    <input type="password" name="password">
    <input type="submit" value="Вход">
</form>
</body>
</html>
