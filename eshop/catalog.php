<?php
require "inc/lib.inc.php";
require "inc/config.inc.php";
$goods = getItemsCatalog();
if ($goods === false) {
    echo "Произошла ошибка";
    exit;
}
if (!count($goods)) {
    echo "Товары отсутствуют";
    exit;
}
if (!isset($_SESSION["user"]))
    $menu = "<p><a href='register.php'>Регистрация</a></p><p><a href='login.php'>Авторизация</a></p>";
else
    $menu = "<p><a href='catalog.php?logout'>Выход</a></p>";
if (isset($_GET["logout"])) {
    userLogout();
    header("Location: /eshop/");
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Каталог товаров</title>
</head>
<body>
<div class="user">
    <p>Товаров в <a href="basket.php">корзине</a>: <?= $count?></p>
    <?= $menu ?>
</div>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
<tr>
	<th>Название</th>
	<th>Автор</th>
	<th>Год издания</th>
	<th>Цена, руб.</th>
	<th>В корзину</th>
</tr>
<?php
    foreach ($goods as $item) {
?>
    <tr>
        <td><a href="view.php?id=<?= $item['id']?>"><?= $item['title']?></a></td>
        <td><?= $item['author']?></td>
        <td><?= $item['pubyear']?></td>
        <td><?= $item['price']?></td>
        <?php
        if (!isset($basket[$item["id"]]))
            echo "<td><a href='add2basket.php?id={$item['id']}'>В корзину</a></td>";
         else
            echo "<td><a href='basket.php'>Оформить</a></td>";
        ?>

    </tr>
<?php
    }
?>
</table>
</body>
</html>