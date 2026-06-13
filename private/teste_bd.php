<?php

echo "Início<br>";

try {

    $ligacao = new PDO(
        "mysql:host=vsgate-s1.dei.isep.ipp.pt;port=10464;dbname=db1241126;charset=utf8",
        "1241126",
        "benido_126",
        [
            PDO::ATTR_TIMEOUT => 5,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );

    echo "Ligação efetuada com sucesso!";

} catch (PDOException $e) {

    echo "ERRO:<br>";
    echo $e->getMessage();

}