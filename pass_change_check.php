<?php
session_start();
require('kinmu_common.php');
//$post=sanitize($_POST);
//値を変数に格納
if(isset($_SESSION["result"])){
	$staff_number = $_SESSION["result"]["staff_number"];
}else{
	$staff_number = $_POST["number"];
	$_SESSION["result"]["staff_number"]=$staff_number;
}
$pass=$_POST['pass'];
$pass2=$_POST['pass2'];
$err = [];

$staff_number=htmlspecialchars($staff_number,ENT_QUOTES,'UTF-8');
$pass=htmlspecialchars($pass,ENT_QUOTES,'UTF-8');

//新パスワードエラーチェック
if(empty($pass)){
	$err['pass1'] = '入力必須項目です。<br/>';
	//$_SESSION['staff_number']=$staff_number;
}
elseif($passlength=mb_strlen($pass))
{
	if($passlength < 8 || 20 < $passlength){
		$err['pass1'] ='8桁以上20桁以内で入力してください.';
	}
}
elseif (!preg_match("/([0-9].*[a-zA-Z]|[a-zA-Z].*[0-9])/", $pass))
{
	$err['pass1'] = '英字、数字混合で入力してください<br/>';
}
//新パスワード（確認）エラーチェック
if(empty($pass2)){
	$err['pass2'] = '入力必須項目です。<br/>';
}
elseif($passlength=mb_strlen($pass2))
{
	if($passlength < 8 || 20 < $passlength){
		$err['pass2'] ='8桁以上20桁以内で入力してください.';
	}
}
elseif (!preg_match("/([0-9].*[a-zA-Z]|[a-zA-Z].*[0-9])/", $pass2))
{
	$err['pass2'] = '英字、数字混合で入力してください<br/>';
}

if($pass!=$pass2){
	$err['pass1'] ='新パスワードと再入力パスワードが一致しません。';
}
if (!empty($err)) {
	$_SESSION['err'] = $err;
	$_SESSION['pass'] = $pass;
	$_SESSION['pass2'] = $pass2;
	header('Location:pass_change.php');
	exit;
}else{
$hash=password_hash($pass,PASSWORD_DEFAULT);
try
{
//DB接続
$dbh = db_connect();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql="UPDATE TBL_STAFF SET password = :password WHERE staff_number = :staff_number";
$stmt=$dbh->prepare($sql);
$params = array(':password' => $hash, ':staff_number' => $staff_number);
$stmt->execute($params);
$dbh=null;
}
catch(Exception $e)
{
	header('Location: err_report.php');
  exit();
}
header('Location:change_pass_done.php');
exit();
}
?>