<?php
//根据客户端传过来的ID 删除对应的ID

require_once '../functions.php';

if (empty($_GET['id'])) {
	exit('缺少必要参数');
}

$id = $_GET['id'];

//delete from users where id in ($id);
$rows = xiu_execute('delete from users where id in (' . $id . ');');

header('Location: users.php');