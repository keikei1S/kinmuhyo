<?php
session_start();
//$post=sanitize($_POST);
//値を変数に格納
$staff_number=$_POST['staff_number'];
$pass=$_POST['pass'];

$staff_number=htmlspecialchars($staff_number,ENT_QUOTES,'UTF-8');
$pass=htmlspecialchars($pass,ENT_QUOTES,'UTF-8');

if($staff_number==""){
	$_SESSION['errMsg1'] = '入力必須項目です。<br/>';
	header('Location:staff_login.php');
	//exit();
}
elseif (preg_match("/[^0-9]/", $staff_number)) {
	$_SESSION['errMsg1'] = "半角数字で入力してください。";
    header('Location:staff_login.php');
	exit();
}
else
{
	$staff_numberlength=mb_strlen($staff_number) ;
	if (4 < $staff_numberlength || $staff_numberlength < 4)  {
    	$_SESSION['errMsg1'] =  "4桁で入力してください。";
    	 header('Location:staff_login.php');
	exit();
	}
}
if($staff_number=="" && $pass!=""){
	$_SESSION['errMsg1'] = '入力必須項目です。<br/>';
	header('Location:staff_login.php');
	exit();
}
if($pass==""){
	$_SESSION['errMsg2'] = '入力必須項目です。<br/>';
	$_SESSION['staff_number']=$staff_number;
	header('Location:staff_login.php');
	exit();
}
elseif (!preg_match("/^[a-zA-Z0-9]+$/", $pass)) 
{	
	$_SESSION['errMsg2'] = '英字、数字混合で入力してください<br/>';
	$_SESSION['staff_number']=$staff_number;
	header('Location:staff_login.php');
	exit();
}
else
{
	$passlength=mb_strlen($pass);
	if($passlength < 8 || 20 < $passlength){
		//var_dump($pass);
		$_SESSION['errMsg2'] ='8桁以上20桁以内で入力してください.';
		$_SESSION['staff_number']=$staff_number;
		header('Location:staff_login.php');
	exit();
	}
}	
$hash=password_hash($pass,PASSWORD_DEFAULT);
try
{
//DB接続
$dsn='mysql:dbname=pros-service_kinmu;host=mysql731.db.sakura.ne.jp;charset=utf8';
$user='pros-service';
$password='cl6cNJs2lt5W';
$dbh= new PDO($dsn,$user,$password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
$sql="SELECT * FROM TBL_STAFF WHERE staff_number=?";
$stmt=$dbh->prepare($sql);
$data[]=$staff_number;
$stmt->execute($data);
$rec = $stmt -> fetch(PDO::FETCH_ASSOC);
setcookie('st_num',$staff_number);
setcookie('pass',$pass);



$dbh=null;

if($rec==false)
{	
	$_SESSION['errMsg1'] = '入力された社員番号は存在しません。<br/>';
	header('Location:staff_login.php');
	exit();
}
if(password_verify($rec['password'],$hash))
{
	$_SESSION['login']=1;
	$_SESSION['result']=$rec;
	// $_SESSION['familyname']=$rec['familyname'];
	// $_SESSION['firstname']=$rec['firstname'];
	// $_SESSION['email']=$rec['email'];
	// $_SESSION['admin_flag']=$rec['admin_flag'];
	// $_SESSION['holiday_with_pay']=$rec['holiday_with_pay'];
	header('Location:switch.php');
	exit();
}
else
{
	$_SESSION['errMsg2'] = 'パスワードを正しく入力してください。<br/>';
	$_SESSION['staff_number']=$staff_number;
	header('Location:staff_login.php');
	exit();
}
}
catch(Exception $e)
{
	print 'システムエラーが発生しました。';
	exit();
}

?>



