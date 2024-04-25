<?php
require("functions.php");
require("connect.php");

$log_entry_request = $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['REQUEST_URI'];
log_append($log_entry_request);

if (empty($_REQUEST))
{
    respond("error_no_request");
    die();
}

$player_id = $_REQUEST['id'];
$update = $_REQUEST['update'];
$token = $_REQUEST['token'];

if (empty($token))
{
    respond("error_not_authorized");
    die();
}

$sql = 'select token from `tokens` where id = :id';
$sth = $dbh->prepare($sql);
$sth->bindValue(':id', $player_id);
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);

$token_saved = $result[0]['token'];

if ($token_saved !== $token)
{
    respond("error_wrong_token");
    die;
}

if (empty($update))
{
    $sql = 'select * from `our_game` where id = :id';
    
    $sth = $dbh->prepare($sql);
    $sth->bindValue(':id', $player_id);
    $sth->execute();
    $result = $sth->fetchAll(PDO::FETCH_OBJ);

    if (empty($result))
    {
	respond("error_select_result_empty");
	die();
    }

    $data['userinfo'] = $result;
    
    header('Content-Type: application/json');
    respond(json_encode($data));
}
else
{
    $data = json_decode($update, true);
    if (is_null($data))
    {
	respond('error_update_decode');
	die();
    }
    //print_r($data[0]);
    $sql = 'update `our_game` set coord_x = :x, coord_y = :y, coord_z = :z, ammunition = :ammo, name = :name, password = :pass where id=:id';
    $sth = $dbh->prepare($sql);
    $sth->bindValue(':id', $data['userinfo'][0]['id']);
    $sth->bindValue(':x', $data['userinfo'][0]['coord_x']);
    $sth->bindValue(':y', $data['userinfo'][0]['coord_y']);
    $sth->bindValue(':z', $data['userinfo'][0]['coord_z']);
    $sth->bindValue(':ammo', $data['userinfo'][0]['ammunition']);
    $sth->bindValue(':name', $data['userinfo'][0]['name']);
    $sth->bindValue(':pass', $data['userinfo'][0]['password']);

    if ($sth->execute())
	respond('success_update');
    else
	respond('error_update');
}

$dbh = null;
