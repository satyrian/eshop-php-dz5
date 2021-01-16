<?php
const FILE_NAME = ".htpasswd";

function getHash($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function checkHash($password, $hash) {
    return password_verify($password, trim($hash));
}

function saveUser($login, $has) {
    $str = "$login:$has\n";
    if (file_put_contents(FILE_NAME, $str, FILE_APPEND))
        return true;
    else
        return false;
}

function userExists($login) {
    if (!is_file(FILE_NAME))
        return false;
    $users = file(FILE_NAME);
    foreach ($users as $user) {
        if (strpos($user, $login . ":") !== false)
            return $user;
    }
    return false;
}

function clearStr($str) {
    return trim(strip_tags($str));
}

function logOut() {
    session_destroy();
    header("Location: secure/login.php");
    exit();
}