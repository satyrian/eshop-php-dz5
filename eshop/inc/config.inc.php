<?php
const DB_HOST = '';
const DB_USER = '';
const DB_PASS = '';
const DB_NAME = '';

const ORDERS_LOG = 'orders.log';

$basket = [];
$voted = [];
/*Количество товаров в корзине*/
$count = 0;

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$link)
    echo mysqli_connect_error();

/*Инициализируем корзину*/
basketInit();
/*Инициализируем голоса*/
votedInit();
session_start();
