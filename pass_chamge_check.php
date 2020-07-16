<?php
session_start();

try
{
//$post=sanitize($_POST);
//値を変数に格納
$staff_number=$_POST['staff_number'];
$pass=$_POST['pass'];
$pass2=$_POST['pass2'];
$motourl2 = $_POST['motourl'];




$staff_number=htmlspecialchars($staff_number,ENT_QUOTES,'UTF-8');
$pass=htmlspecialchars($pass,ENT_QUOTES,'UTF-8');

if($staff_number==""){
	$_SESSION['errMsg1'] = '入力必須項目です。<br/>';
	header('Location:pass_change.php');
	//exit();
}
elseif (preg_match("/[^0-9]/", $staff_number)) {
	$_SESSION['errMsg1'] = "半角数字で入力してください。";
    header('Location:pass_change.php');
	exit();
}
else
{
	$staff_numberlength=mb_strlen($staff_number) ;
	if (4 < $staff_numberlength || $staff_numberlength < 4)  {
    	$_SESSION['errMsg1'] =  "4桁で入力してください。";
    	 header('Location:pass_change.php');
	exit();
	}
}
if($staff_number=="" && $pass!=""){
	$_SESSION['errMsg1'] = '入力必須項目です。<br/>';
	header('Location:pass_change.php');
	exit();
}
if($pass==""){
	$_SESSION['errMsg2'] = '入力必須項目です。<br/>';
	$_SESSION['staff_number']=$staff_number;
	header('Location:pass_change.php');
	exit();
}
elseif (!preg_match("/^[a-zA-Z0-9]+$/", $pass)) 
{	
	$_SESSION['errMsg2'] = '英字、数字混合で入力してください<br/>';
	$_SESSION['staff_number']=$staff_number;
	header('Location:pass_change.php');
	exit();
}
else
{
	$passlength=mb_strlen($pass);
	if($passlength < 8 || 20 < $passlength){
		//var_dump($pass);
		$_SESSION['errMsg2'] ='8桁以上20桁以内で入力してください.';
		$_SESSION['staff_number']=$staff_number;
		header('Location:pass_change.php');
	exit();
	}
}
if($pass!=$pass2){
	$_SESSION['errMsg2'] ='新パスワードと再入力パスワードが一致しません。';
	$_SESSION['staff_number']=$staff_number;
	header('Location:pass_change.php');
	exit();
}

$hash=password_hash($pass,PASSWORD_DEFAULT);
// $hash1=password_hash($pass2,PASSWORD_DEFAULT);


//DB接続
$dsn='mysql:dbname=kinmuhyo;host=localhost;charset=utf8';
$user='root';
$password='';
$dbh= new PDO($dsn,$user,$password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
$sql='update TBL_STAFF set password = ? where staff_number = ?';
$stmt = $dbh->prepare($sql);
$data = $stmt->execute(array($pass, $staff_number));


if($data==false)
{	
	$_SESSION['errMsg1'] = '入力された社員番号は存在しません。<br/>';
	header('Location:pass_change.php');
	exit();
}

$dsn='mysql:dbname=kinmuhyo;host=localhost;charset=utf8';
$user='root';
$password='';
$dbh= new PDO($dsn,$user,$password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql="SELECT * FROM TBL_STAFF WHERE staff_number=?";
$stmt=$dbh->prepare($sql);
$get[]=$staff_number;
$stmt->execute($get);
$rec = $stmt -> fetch(PDO::FETCH_ASSOC);

$dbh=null;
if(password_verify($rec['password'],$hash))
{
	if($motourl2!=NULL){
		$_SESSION['result']=$rec;	
		header('Location:change_pass_done.php');
		exit();
	}else{
		header('Location:change_pass_done2.html');
		exit();
	}
}
else
{
	$_SESSION['errMsg2'] = 'パスワードを正しく入力してください。<br/>';
	$_SESSION['staff_number']=$staff_number;
	header('Location:pass_change.php');
	exit();
}
}
catch(Exception $e)
{
	print 'システムエラーが発生しました。';
	exit();
}

?>



