<?php
session_start();
require_once "Mail.php";
$staff_number=$_POST['staff_number'];
$email = $_POST['email'];
$staff_number=htmlspecialchars($staff_number,ENT_QUOTES,'UTF-8');
$email=htmlspecialchars($email,ENT_QUOTES,'UTF-8');
$reg_str = "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/";

if($staff_number==""){
  $_SESSION['errMsg1'] = '入力必須項目です。<br/>';
  header('Location:re_pass.php');
  //exit();
}
elseif (preg_match("/[^0-9]/", $staff_number)) {
  $_SESSION['errMsg1'] = "半角数字で入力してください。";
    header('Location:re_pass.php');
  exit();
}
else
{
  $staff_numberlength=mb_strlen($staff_number) ;
  if (4 < $staff_numberlength || $staff_numberlength < 4)  {
      $_SESSION['errMsg1'] =  "4桁で入力してください。";
       header('Location:re_pass.php');
  exit();
  }
}
if($email=="")
{
  $_SESSION['errMsg'] ='入力必須項目です。';
  header('Location:re_pass.php');
  exit();
}
if( 30 < mb_strlen($email))
{
  $_SESSION['errMsg'] ='30桁以内で入力してください。';
  header('Location:re_pass.php');
  exit();
} 
elseif(!preg_match($reg_str, $email))
{
  $_SESSION['errMsg'] ='使用できない文字が含まれています。';
  header('Location:re_pass.php');
  exit();
}

try{
  $urltoken = hash('sha256',uniqid(rand(),1));
  $url = "http://localhost:8080/kinmuhyo/pass_change.php"."?urltoken=".$urltoken;
    //例外処理を投げる（スロー）ようにする
  $dsn='mysql:dbname=kinmuhyo;host=localhost;charset=utf8';
  $user='root';
  $password='';
  $dbh= new PDO($dsn,$user,$password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
  $sql='update TBL_STAFF set urltoken = ? where staff_number = ?';
  $stmt = $dbh->prepare($sql);
  $result = $stmt->execute(array($urltoken, $staff_number));
}
catch(Exception $e)
{
  print 'システムエラーが発生しました。';
  exit();
}

try{
$dsn='mysql:dbname=kinmuhyo;host=localhost;charset=utf8';
$user='root';
$password='';
$dbh= new PDO($dsn,$user,$password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
$sql="SELECT * FROM TBL_STAFF WHERE staff_number=?";
$stmt=$dbh->prepare($sql);
$data[]=$staff_number;
$stmt->execute($data);
$rec = $stmt -> fetch(PDO::FETCH_ASSOC);

if($rec==false)
{ 
  $_SESSION['errMsg1'] = '入力された社員番号は存在しません。<br/>';
  header('Location:re_pass.php');
  exit();
}

if($rec['email']!=$email)
{
  $_SESSION['errMsg1'] = '登録されているメールアドレスを入力してください。<br/>';
  header('Location:re_pass.php');
  exit();
}
else
{
  // mb_language("Japanese");
  // mb_internal_encoding("UTF-8");
  $from = '6656keiichi@gmail.com';
  $host = "ssl://smtp.gmail.com";
  $port = "465";
  $username = '6656keiichi@gmail.com';
  $password = 'Keiichi1106';

  $subject = 'パスワード変更';
  $body ='以下からパスワードを再設定してください。'
  .$url;


  $headers = array ('From' => $from, 'To' => $email,'Subject' => $subject);
  $smtp = Mail::factory('smtp',
  array ('host' => $host,
  'port' => $port,
  'auth' => true,
  'username' => $username,
  'password' => $password));

  $mail = $smtp->send($email, $headers, $body);
  if (PEAR::isError($mail)) {
    echo($mail->getMessage());
  } else {
    print ("メッセージを送信しました。\n");
    print "<a href="."staff_login.php".">ログイン画面へ</a>";
  }
 
}
}
catch(Exception $e)
{
  print 'システムエラーが発生しました。';
  exit();
}
?>

