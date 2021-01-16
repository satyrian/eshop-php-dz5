<?php
require "inc/lib.inc.php";
require "inc/config.inc.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST as $key => $value) {
        $basket[$key] = $value;
    }
    saveBasket();
    header("Location: basket.php");
    exit;
}

