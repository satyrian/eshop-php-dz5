<?php
	require "secure/session.inc.php";
	require "../inc/lib.inc.php";
	require "../inc/config.inc.php";
?>
<!DOCTYPE html>
<html>
<head>
	<title>Поступившие заказы</title>
	<meta charset="utf-8">
</head>
<body>
<h1>Поступившие заказы:</h1>
<?php
$orders = getOrders();
if (!$orders) {
    echo "Заказов нет";
    exit();
}
foreach ($orders as $order):
    $dt = date("d-m-Y в H:i", (int)$order['date'])
    ?>
    <hr>
    <h2>Заказ номер: <?= $order['orderId'] ?></h2>
    <p><b>Заказчик</b>: <?= $order['name'] ?></p>
    <p><b>Email</b>: <?= $order['email'] ?></p>
    <p><b>Телефон</b>: <?= $order['phone'] ?></p>
    <p><b>Адрес доставки</b>: <?= $order['address'] ?></p>
    <p><b>Дата размещения заказа</b>: <?= $dt ?></p>

    <h3>Купленные товары:</h3>
    <table border="1" cellpadding="5" cellspacing="0" width="90%">
        <tr>
            <th>N п/п</th>
            <th>Название</th>
            <th>Автор</th>
            <th>Год издания</th>
            <th>Цена, руб.</th>
            <th>Количество</th>
        </tr>
        <?php
        $i = 1;
        $sum = 0;
        foreach ($order['goods'] as $item):
            ?>
            <tr>
                <th><?= $i ?></th>
                <th><?= $item['title'] ?></th>
                <th><?= $item['author'] ?></th>
                <th><?= $item['pubyear'] ?></th>
                <th><?= $item['price'] ?></th>
                <th><?= $item['quantity'] ?></th>
            </tr>
            <?php
            $i++;
            $sum += $item['price'] * $item['quantity'];
        endforeach;
        ?>
    </table>
    <p>Всего товаров в заказе на сумму: <?= $sum ?> руб.</p>
<?php
endforeach;
?>
</body>
</html>