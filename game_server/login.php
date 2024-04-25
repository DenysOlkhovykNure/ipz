<?php
require("functions.php");
require("connect.php");

$log_entry_request = $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['REQUEST_URI'];
log_append($log_entry_request);

$username = $_REQUEST['username'];
$password = $_REQUEST['password'];

if (empty($username))
{
    respond("error_login_no_username");
    die();
}

$sql = 'select id, password from `our_game` where name = :name';

$sth = $dbh->prepare($sql);
$sth->bindValue(':name', $username);
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);

if(empty($result))
{
    respond("error_login_failed");
    die();
}

$id = $result[0]['id'];
$password_saved = $result[0]['password'];

if ($password_saved !== $password)
{
    respond("error_login_failed");
    die();
}

$token = hash('sha1', $password_saved . time());

$sql = 'update `tokens` set token = :token where id = :id';

$sth = $dbh->prepare($sql);
$sth->bindValue(':token', $token);
$sth->bindValue(':id', $id);
$sth->execute();

respond("$token");

$dbh = null;
