<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../login/login.php');
    exit();
}
$username = $_POST['text_username'] ?? '';
$password = $_POST['text_password'] ?? '';

if ($username == '' || $password == '') {
    header('Location: ../login/login.php');
    exit();
}
$_SESSION['utilizador'] = $username;
header('Location: index.php');
exit();