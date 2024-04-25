<?php
$db_driver="mysql";
$host = "127.0.0.1";
$database = "game";
$dsn = "$db_driver:host=$host; dbname=$database";
$username = "root"; $password = "";

try {
    $dbh = new PDO ($dsn, $username, $password);
}
catch (PDOException $e) {
    echo "Error!: " . $e->getMessage() . "<br/>"; die();
}
