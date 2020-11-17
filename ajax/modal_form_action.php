<?php
require '../vendor/autoload.php';
session_start(); 
if(!isLogin() || !isset($_GET)) {
	return;
}

foreach($_GET as $key => $val){
	${$key} = $val;
}

$db = Database::get();

switch($action){
	case "edit":
		$table = "security_event";
		$condition = "EventID = :EventID";
		$data_array[':EventID'] = $id;
		$entry = $db->query($table, $condition, $order_by = "1", $fields = "*", $limit = "", $data_array);
		echo json_encode($entry[0]);
		break;
	case "delete":
		$table = "security_event";
		$key_column = "EventID";
		$db->delete($table, $key_column, $id);
		break;
}
