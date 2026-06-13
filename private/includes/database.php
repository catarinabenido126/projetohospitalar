<?php

require_once __DIR__ . '/../../config/config.php';

try {

    $database = new PDO(
        "mysql:host=" . MYSQL_HOST .
        ";port=" . MYSQL_PORT .
        ";dbname=" . MYSQL_DATABASE .
        ";charset=utf8",

        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );

    $database->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {

    die("Erro na ligação à base de dados.");
}