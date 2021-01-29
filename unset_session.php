<?php
if(!isset($_SESSION)){
	session_start();
session_regenerate_id(true);
}
if (isset($_SESSION["login"])==false)
{
	header("Location: staff_login.php");
	exit();
}
//セッション情報を削除するため、必要なセッション情報のみ一度変数に格納
$login = $_SESSION["login"];
$result = $_SESSION["result"];
$status = $_SESSION["status"];
$url = $_SESSION["url"];
$new = $_SESSION["newRegister"];

//セッション情報の初期化
$_SESSION = array();

//再度セッションに入れ直す
$_SESSION["login"] = $login;
$_SESSION["result"] = $result;
$_SESSION["status"] = $status;

if(isset($_POST["continue"])){
  $_SESSION["newRegister"]=$new;
  $_SESSION["url"]=$url;
  header('Location: index.php');
  exit;
}else{
  header('Location: list_of_members.php');
  exit;
}
?>
