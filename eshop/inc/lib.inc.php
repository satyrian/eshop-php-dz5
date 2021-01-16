<?php
function clearStr($data) {
    global $link;
    $data = trim(strip_tags($data));
    return mysqli_escape_string($link, $data);
}
function clearInt($data) {
    return abs((int)$data);
}
/*Добавление товара в каталог*/
function addItemToCatalog($title, $author, $pubyear, $price) {
    $sql = "INSERT INTO catalog (title, author, pubyear, price) 
            VALUES (?, ?, ?, ?)";
    global $link;
    if (!$stmt = mysqli_prepare($link, $sql))
        return false;
    mysqli_stmt_bind_param($stmt, 'ssii', $title, $author, $pubyear, $price);
    if (!mysqli_stmt_execute($stmt))
        return $id = 0;
    $id = mysqli_stmt_insert_id($stmt);
    mysqli_stmt_close($stmt);
    return $id;
}
/*Получение всех записей из каталога*/
function getItemsCatalog() {
    $sql = "SELECT id, title, author, pubyear, price
            FROM catalog";
    global $link;
    if (!$result = mysqli_query($link, $sql))
        return false;
    $item = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    return $item;
}
/*Добавление нового пользователя*/
function addNewUser($login, $password) {
    global $link;
    $sql = "INSERT INTO users (login, password) VALUES (?, ?)";
    if (!$stmt = mysqli_prepare($link, $sql))
        return false;
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    mysqli_stmt_bind_param($stmt, "ss", $login, $passwordHash);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return true;
}
/*Авторизация пользователя*/
function userLogin($login, $password) {
    global $link;
    $sql = "SELECT id, password FROM users WHERE login = ?";
    if (!$stmt = mysqli_prepare($link, $sql))
        return false;
    mysqli_stmt_bind_param($stmt, "s", $login);
    if (!mysqli_stmt_execute($stmt))
        return false;
    $result = mysqli_stmt_get_result($stmt);
    $item = mysqli_fetch_assoc($result);
    if (!password_verify($password, $item["password"]))
        return false;
    mysqli_stmt_close($stmt);
    mysqli_free_result($result);
    $_SESSION["user"] = $item["id"];
    return true;
}
/*Проверяем существует ли логин*/
function checkLogin($login) {
    global $link;
    $sql = "SELECT id FROM users WHERE login = ?";
    if (!$stmt = mysqli_prepare($link, $sql))
        return false;
    mysqli_stmt_bind_param($stmt, "s", $login);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (!mysqli_stmt_num_rows($stmt))
        return false;
    mysqli_stmt_close($stmt);
    return true;
}
/*Выход пользователя*/
function userLogout() {
    unset($_SESSION["user"]);
    header("Location: /eshop/");
    exit;
}
/*Добавление нового комментария*/
function addCommentToItem($itemId, $name, $email, $comment) {
    global $link;
    $sql = "INSERT INTO comments (item_id, name, email, comment)
                VALUES (?, ?, ?, ?)";
    if (!$stmt = mysqli_prepare($link, $sql))
        return false;
    mysqli_stmt_bind_param($stmt, "isss", $itemId, $name, $email, $comment);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return true;
}
/*Получение комментариев конкретной публикации и их кол-во*/
function getCommentsItemId($itemId) {
    global $link;
    $sql = "SELECT id, name, email, comment, UNIX_TIMESTAMP(datetime) as dt
                FROM comments WHERE item_id = ? 
                ORDER BY id DESC";
    if (!$stmt = mysqli_prepare($link, $sql))
        return false;
    mysqli_stmt_bind_param($stmt, "i", $itemId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $item = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $quantity = mysqli_num_rows($result);
    $item["quantity"] = $quantity;
    mysqli_free_result($result);
    mysqli_stmt_close($stmt);
    return $item;
}
/*Считаем рейтинг*/
function getRating($id, $rating) {
    $current = getTotalVoteAndRating($id);
    $ratingAvg = ($current["rating"] * $current["total_vote"] + $rating) / ($current["total_vote"] + 1);
    $totalVote = ++ $current["total_vote"];
    $result["rating"] = round($ratingAvg, 2);
    $result["total_vote"] = $totalVote;
    return $result;

}
/*Получение количества проголосовавших и рейтинг*/
function getTotalVoteAndRating($id) {
    global $link;
    $sql = "SELECT rating, total_vote FROM catalog WHERE id = ?";
    if (!$stmt = mysqli_prepare($link, $sql))
        return false;
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $item = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    mysqli_stmt_close($stmt);
    return $item;
}
/*Добавление рейтинга для товара*/
function addRatingToItem($id, $rating) {
    global $link, $voted;
    $sql = "UPDATE catalog SET rating = ?, total_vote = ? WHERE id = ?";
    $ratingAvg = getRating($id, $rating);
    if (!$stmt = mysqli_prepare($link, $sql))
        return false;
    mysqli_stmt_bind_param($stmt, "dii", $ratingAvg["rating"], $ratingAvg["total_vote"], $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $voted[$id] = true;
    saveVoted();
    return true;
}
/*Получение информации о конкретном товаре*/
function getItemById($id) {
    global $link;
    $sql = "SELECT id, title, author, pubyear, price, rating, total_vote
            FROM catalog WHERE id = ?";
    if (!$stmt = mysqli_prepare($link, $sql))
        return false;
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $item = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    mysqli_stmt_close($stmt);
    return $item;
}
/*Сохранение товаров корзины в куки*/
function saveBasket() {
    global $basket;
    $basket = base64_encode(serialize($basket));
    setcookie('basket', $basket, 0x7FFFFFFF);
}
/*Сохранение голосов в куки*/
function saveVoted() {
    global $voted;
    $voted = base64_encode(serialize($voted));
    setcookie("voted", $voted, 0x7FFFFFFF);
}
/*Инициализация голосов*/
function votedInit() {
    global $voted;
    if (!isset($_COOKIE["voted"])) {
        $voted = ["idUser" => uniqid()];
        saveVoted();
    } else {
        $voted = unserialize(base64_decode($_COOKIE["voted"]));
    }
}
/*Инициализация корзины*/
function basketInit() {
    global $basket, $count;
    if (!isset($_COOKIE['basket'])) {
        // Если куков нет, то создаем куки с уникальным id
        $basket = ['orderId' => uniqid()];
        saveBasket();
    } else {
        // Куки есть, то загружаем корзину
        $basket = unserialize(base64_decode($_COOKIE['basket']));
        $arr = $basket;
        array_shift($arr);
        foreach ($arr as $value) {
            $count += $value;
        }
    }
}
/*Добавление товара в корзину*/
function add2Basket($id) {
    global $basket;
    $basket[$id] = 1;
    saveBasket();
}
/*Принимает результат myBasket и возвращает массив товаров с их кол-ом*/
function result2Array($data) {
    global $basket;
    $arr = [];
    while ($row = mysqli_fetch_assoc($data)) {
        $row['quantity'] = $basket[$row['id']];
        $arr[] = $row;
    }
    return $arr;
}
/*Возвращаем корзину в виде массива*/
function myBasket() {
    global $link, $basket;
    $goods = array_keys($basket);
    array_shift($goods);
    if (!$goods)
        return false;
    $ids = implode(",", $goods);
    $sql = "SELECT id, author, title, pubyear, price
                FROM catalog WHERE id IN ($ids)";
    if (!$result = mysqli_query($link, $sql))
        return false;
    $items = result2Array($result);
    mysqli_free_result($result);
    return $items;
}
/*Удаление товара из корзины*/
function deleteItemFromBasket($id) {
    global $basket;
    if (array_key_exists($id, $basket)) {
        unset($basket[$id]);
        saveBasket();
    }
    return false;
}
/*Сохранение заказа в бд*/
function saveOrder($datetime) {
    global $basket, $link;
    $goods = myBasket(); // Получаем корзину
    $stmt = mysqli_stmt_init($link); // Инициализируем запрос
    $sql = "INSERT INTO orders (
                            title,
                            author,
                            pubyear,
                            price,
                            quantity,
                            orderid,
                            datetime)
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
    if (!mysqli_stmt_prepare($stmt, $sql))
        // Если при подготовке запроса произошлао шибка возвращаем false
        return false;
    foreach ($goods as $item) {
        mysqli_stmt_bind_param($stmt, 'ssiiisi',
            $item['title'],
            $item['author'],
            $item['pubyear'],
            $item['price'],
            $item['quantity'],
            $basket['orderId'],
            $datetime,
        );
        mysqli_stmt_execute($stmt);
    }
    /*Закрываем соединение с запросом и удаляем куки*/
    mysqli_stmt_close($stmt);
    setcookie('basket', "", time() - 3600);
    return true;
}
/*Получение всех заказов*/
function getOrders() {
    global $link;
    if (!is_file( ORDERS_LOG))
        return false;
    /*Получаем в виде массива персональные данные из файла*/
    $orders = file(ORDERS_LOG);
    /*Массив который будет возвращен функцией*/
    $allOrders = [];
    foreach ($orders as $order) {
        list($name, $email, $phone, $address, $orderId, $date) = explode("|", $order);
        /*Промежуточный массив для хранения информации о конкретном заказе*/
        $orderInfo = [];
        /*Сохраняем информацию о конкретном пользователе*/
        $orderInfo['name'] = $name;
        $orderInfo['email'] = $email;
        $orderInfo['phone'] = $phone;
        $orderInfo['address'] = $address;
        $orderInfo['orderId'] = $orderId;
        $orderInfo['date'] = $date;
        /*Запрос на выборку из таблицы всех товаров для конкретного покупателя*/
        $sql = "SELECT title, author, pubyear, price, quantity
                    FROM orders
                    WHERE orderid = '$orderId'";
        /*Получение результата выборки*/
        if (!$result = mysqli_query($link, $sql))
            return false;
        $items = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        /*Сохранение результата в промежуточном массиве*/
        $orderInfo['goods'] = $items;
        /*Добавление промежуточного массива в возвращаемый массив*/
        $allOrders[] = $orderInfo;
    }
    return $allOrders;
}
/*Получаем путь до картинки*/
function getPathToImage($id) {
    $noImg = "no-image.png";
    $path =  "/eshop/uploads/collections/";
    $pathToItem = $path . "$id.jpg";
    if (!is_file($_SERVER["DOCUMENT_ROOT"] . $pathToItem)) {
        return $path . $noImg;
    }
    return $pathToItem;
}