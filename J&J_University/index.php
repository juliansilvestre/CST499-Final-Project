<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('session.use_only_cookies','1');
session_start();

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

$allowedPages = ['home', 'courses', 'profile', 'cart', 'registration', 'login', 'schedule', 'offeringsscheduling', 'courseofferings'];

if (!in_array($page, $allowedPages)) {
    $page = 'home'; }

$currentPage = $page;

require 'master.php';
?>