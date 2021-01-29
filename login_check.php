<?php
if(!isset($_SESSION)){
	session_start();
}
ob_start();
include("kinmu_common.php");
ob_clean();
//今日日付を取得し、退職日と比較する
$today = date("Y-m-d");
//値を変数に格納
if(!isset($_POST)){
	header("Location: staff_login.php");
	exit();
}else{
	$staff_number=$_POST['staff_number'];
	$pass=$_POST['pass'];
}

$staff_number=htmlspecialchars($staff_number,ENT_QUOTES,'UTF-8');
$pass=htmlspecialchars($pass,ENT_QUOTES,'UTF-8');

$err = [];
//社員番号のエラーチェック
if($staff_number==""){
	$err['staff_number'] = '入力必須項目です。<br/>';
}elseif(preg_match('/^([0-9]{4})$/', $staff_number) == false){
	$err['staff_number'] =  "4桁で入力してください。";
}elseif (preg_match("/[^0-9]/", $staff_number)) {
	$err['staff_number'] = "半角数字で入力してください。";
}
//パスワードのエラーチェック
$passlength=mb_strlen($pass);
if($pass==""){
	$err['pass'] = '入力必須項目です。<br/>';
}elseif($passlength < 8 || 20 < $passlength){
	$err['pass'] ='8桁以上20桁以内で入力してください。';
}elseif (!preg_match("/([0-9].*[a-zA-Z]|[a-zA-Z].*[0-9])/", $pass)){
	$err['pass'] = '英字、数字混合で入力してください。';
}

if (!empty($err)) {
	$_SESSION["st_num"] = $staff_number;
	$_SESSION["password"] = $pass;
	$_SESSION["err"] = $err;
	header('Location:staff_login.php');
	exit();
}else{
try
{
//DB接続
$dbh = db_connect();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql="SELECT `staff_number`, `familyname`, `firstname`,  `password`, `email`, `admin_flag`, `holiday_with_pay`, `new_work_id`, `new_start_month`, `new_end_month`, `old_work_id`, `old_start_month`, `old_end_month`,`retirement_date` FROM `TBL_STAFF` WHERE staff_number=:staff_number";
$stmt=$dbh->prepare($sql);
$stmt->bindValue(":staff_number",$staff_number,PDO::PARAM_STR);
$stmt->execute();
$rec = $stmt -> fetch(PDO::FETCH_ASSOC);

$dbh=null;
if($rec==false)
{
	$err['staff_number'] = '入力された社員番号は存在しません。<br/>';
	$_SESSION["st_num"] = $staff_number;
	$_SESSION["password"] = $pass;
	$_SESSION["err"] = $err;
	header('Location:staff_login.php');
	exit();
}elseif($rec['retirement_date'] < $today){
	$err['staff_number'] = '既に退職しています<br/>';
	$_SESSION["st_num"] = $staff_number;
	$_SESSION["password"] = $pass;
	$_SESSION["err"] = $err;
	header('Location:staff_login.php');
	exit();
}
//入力されたパスワードとテーブルのハッシュ値(パスワード)を比較
if(password_verify($pass , $rec['password']))
{
	//セッション情報の初期化
	$_SESSION = array();
	$_SESSION['login']=1;
	$_SESSION['result']=$rec;
	if($pass=="abc12345678"){
		header('Location:pass_change.php');
		exit();
	}else{
		//クッキーに保存し、2回目以降は自動ログインする（現状は２週間）
		setcookie("st_num", $staff_number);
		setcookie("pass", $pass);
		header('Location:switch.php');
		exit();
	}
}
else
{
	$err['pass'] = 'パスワードが違います。ご確認ください。<br/>';
	$_SESSION["err"] = $err;
	$_SESSION["st_num"] = $staff_number;
	$_SESSION["password"] = $pass;
	header('Location:staff_login.php');
	exit();
}
}
catch(Exception $e)
{
	header('Location: err_report.php');
  exit();
}
}
?>
