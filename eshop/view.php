<?php
include_once "inc/lib.inc.php";
include_once "inc/config.inc.php";
$title = "";
$result = "";
if (!isset($_GET["id"])) {
    header("Location: catalog.php");
    exit;
}
$id = clearInt($_GET["id"]);
$item = getItemById($id);
if (!$item) {
    header("HTTP/1.1 404 Not Found");
    echo "Ой, ты куда-то не туда зашел, дружок</br>404 Not Found";
    exit;
}
$comments = getCommentsItemId($id);
$pathToImg = getPathToImage($id);
$title = $item["title"];
list("rating" => $rating, "total_vote" => $totalVote) = getTotalVoteAndRating($id);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($voted[$id])) {
        $ratingVote = clearInt($_POST["rating"]);
        addRatingToItem($id, $ratingVote);
        $result = "<p>Спасибо за ваш голос!</p>";
        header("refresh: 2;url=" . $_SERVER["REQUEST_URI"]);
    } else {
        $result = "<p>Вы уже голосовали</p>";
        header("refresh: 2;url=" . $_SERVER["REQUEST_URI"]);
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
    <title><?= $title ?></title>
</head>
<body>
<div class="main">
    <h1><?= $item["title"]?></h1>
    <form action="<?= $_SERVER["REQUEST_URI"] ?>" method="post">
        <p>Рейтинг: <?= $rating ?> голосов <?= $totalVote ?></p>
        <select name="rating">
            <option value="5">5</option>
            <option value="4">4</option>
            <option value="3">3</option>
            <option value="2">2</option>
            <option value="1">1</option>
        </select>
        <input type="submit" value="Оценить" />
        <?= $result ?>
    </form>
    <div class="image" style="width: 350px; padding-top: 15px">
        <img src="<?= $pathToImg ?>" alt="<?= $item["title"]?>" style="width: 100%">
    </div>
    <p>Автор: <?= $item["author"]?></p>
    <p>Дата издания: <?= $item["pubyear"]?> г.</p>
    <p>Цена: <?= $item["price"]?> р.</p>
</div>
<div class="comm">
    <div class="form">
        <h3>Оставьте свой комментарий</h3>
        <form method="post" action="add2comment.php?id=<?= $item["id"] ?>">
            <p>Имя:</p>
            <input type="text" name="name"/>
            <p>Email:</p>
            <input type="email" name="email" />
            <p>Комментарий:</p>
            <textarea name="comment"></textarea>

            <br />

            <input type="submit" value="Отправить!" />
        </form><br>
    </div>
    <div class="comments">
        <?php
        include_once "comments.php";
        ?>
    </div>
</div>
</body>
</html>
