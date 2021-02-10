<?php
session_start();
unset($_SESSION['delete']);
unset($_SESSION['save']);

require_once "Mail.php";

ob_start();
include("seisansho.php");
ob_clean();
try {
//文字の最大値
$limit = 20;
	print '<span style="font-weight:bold;">'.'No.'.$staff_number.'</span>';
//姓
	if(mb_strlen($result['familyname']) < $limit) {
	print '<span style="font-weight:bold;">'.$result['familyname'].'</span>';
	}else{
	print '<span style="font-weight:bold;">'.mb_substr($result['familyname'],0,20).'</span>';
	}
//名
if(mb_strlen($result['firstname']) < $limit) {
	print '<span style="font-weight:bold;">'.$result['firstname'].'</span>';
	}else{
	print '<span style="font-weight:bold;">'.mb_substr($result['firstname'],0,20).'</span>';
	}
  print '<br/>';	 
  

$result=$_SESSION['result'];
$staff_number=$result['staff_number'];
$staff_name=$result['familyname'].$result['firstname'];
$email=$result['email'];

$month = $_SESSION["select1"];
if(isset($month)){
        $s_year_and_month = $month.date("-01");
        $now_month = date('t', strtotime($s_year_and_month));
        $e_year_and_month = $month.date("-".$now_month);
    }

?>
<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style>
.img{
	position: absolute;
	top: 0%;
	right: 0%;
}
</style>
<title>入力完了画面</title>
</head>
<body>
<div class="img">
	<img class="img"src="/img/imgs_logo.PNG" alt="ロゴ" width="150" height="60">
</div>
<br/>
<div class="wrapper">
<br><br><br>
<div align="center">
	<h1>交通費精算表の入力が完了しました。</h1>
</div>
<!--ログアウトする!-->
<br><br>
	<form method="post" action="switch.php">
  <div align="center">
  <input  type="submit"  formaction="switch.php" style="background-color: #87cefa; width:230px;padding:15px;font-size:20px;"value="OK">
  </div>
	</form>
	</div>
</div>
<?php

  // //////////////////データベースの読込 S//////////////////////

$dbh = db_connect();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//ローカル用
// $sql="SELECT * FROM tbl_checkout_status WHERE staff_number=:staff_number AND year_and_month = :year_and_month";
//サーバー用
$sql="SELECT * FROM TBL_CHECKOUT_STATUS WHERE staff_number=:staff_number AND year_and_month = :year_and_month";
$stmt=$dbh->prepare($sql);
$stmt->bindValue(":staff_number",$staff_number,PDO::PARAM_STR);
$stmt->bindValue(":year_and_month",$month,PDO::PARAM_STR);
$stmt->execute();
$rec = $stmt -> fetchAll(PDO::FETCH_ASSOC);

//データが入っていたらUPDATE,なかったらINSERT
if(count($rec) === 1){
  //ローカル用
  // $sql = "UPDATE tbl_checkout_status SET Appdate = :Appdate WHERE staff_number = :staff_number AND year_and_month = :year_and_month";
  //サーバー用
  $sql = "UPDATE TBL_CHECKOUT_STATUS SET Appdate = :Appdate WHERE staff_number = :staff_number AND year_and_month = :year_and_month";
  $stmt=$dbh->prepare($sql);
 $params =array('Appdate' => date("Y-m-d"),'staff_number' => $staff_number,'year_and_month'=>$month);
$stmt->execute($params);
}
else{
  //ローカル用
  $sql='INSERT INTO TBL_CHECKOUT_STATUS  (
    staff_number,
    year_and_month,
    checkout_visit,
    Appdate
    )
    VALUES(:staff_number,:year_and_month,:checkout_visit,:Appdate)';
    $stmt=$dbh->prepare($sql);
    $params =array('staff_number' => $staff_number,'year_and_month' => $month,'checkout_visit' => '1','Appdate'=>date("Y-m-d"));
    $stmt->execute($params);
}


  $dbh = null;
} catch (Exception $e) {
  print 'システムエラーが発生しました';
  exit();
}
	// ////////////////データベースの読込 E//////////////////////
?>

<?php
 $from = $email;
 $host = "ssl://smtp.gmail.com";
 $port = "465";
//送信元のアドレス
$username = "prossystem.test@gmail.com";
//送信元アドレスのパスワード
$password = 'prostest';
//軽部さんと真鍋さんのメールアドレス、自分を設定する
 $to = "keisuk0625t@gmail.com , $email";
 //件名
 $subject = substr($s_year_and_month, 0, 7).'月分交通費精算';
 //本文
 $body ="真鍋さん。軽部さん。
 

お疲れ様です。$staff_name です。
交通費精算書の入力が完了しました。ご確認よろしくお願いいたします。

※本メールは送信専用のため、ご返信いただけません。
※ご返信の際は、お手数ですが下記からお願いいたします。
◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆
株式会社プロズサービス
$staff_name ($email)　　　　　　　　　　　　　　　　　

　ISO/IEC 27001:2013 JIS Q 27001:2014 認証登録番号：IA 160164a

　〒141-0022 東京都品川区東五反田1-11-15 電波ビル 4F
　tel:03-5789-2188/fax:03-5789-2901
https://www.pros-service.co.jp/
◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆";

  $headers = array ('From' => $from, 'To' => $to,'Subject' => $subject);
  $smtp = Mail::factory('smtp',
  array ('host' => $host,
  'port' => $port,
  'auth' => true,
  'username' => $username,
  'password' => $password));

  $mail = $smtp->send($to, $headers, $body);

if (PEAR::isError($mail)) {
echo($mail->getMessage());
} else {
//DBに接続し、データがなければyear_and_month,Application dateに日付を追加する。
//データがあれば、更新する。
}
?>

</body>
</html>