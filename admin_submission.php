<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/css/done.css">
<title>入力完了画面</title>
</head>
<body>
<?php

require_once "Mail.php";

session_start();
ob_start();
include("seisansho.php");
ob_clean();
$result=$_SESSION['result'];
$staff_number=$result['staff_number'];
$staff_name=$result['familyname'].$result['firstname'];
$email=$result['email'];
$s_year_and_month = date("Y-").$_SESSION['month'].date("-01");

// try{
// 	$dsn = 'mysql:dbname=勤務表;host=localhost;charset=utf8';
// 	$user = 'root';
// 	$password = '';
// 	$dbh= new PDO($dsn,$user,$password);
// 	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// 	$sql="INSERT INTO `TBL_SUMMARY`(`staff_number`, `year_and_month`, `remaining_paid_days`, `status`,`create_date`, `update_date`) VALUES (:staff_number, :year_and_month, :remaining_paid_days, :status, now(), now())on duplicate key update staff_number=:staff_number, year_and_month = :year_and_month, remaining_paid_days = :remaining_paid_days, status = :status";
// 	$stmt=$dbh->prepare($sql);
//  	$params =array('staff_number' => $staff_number,'year_and_month' => $s_year_and_month, 'work_ID' => $work_id, 'remaining_paid_days' => $_SESSION["yukyuzan"], 'status' => '2');
// 		$stmt->execute($params);
// 		$dbh=null;
// 	}	
// 	catch(Exception $e){	
// 		var_dump($e);
// 	print 'システムエラーが発生しました。';
// 	exit();
// 	}

 $from = $email;
 $host = "ssl://smtp.gmail.com";
 $port = "465";
 $username = $email;
 $password = 'keisuk0625T';
 $to = 'k.takahashi@pro-s.co.jp';
 //件名
 $subject = substr($s_year_and_month, 0, 7).'月分勤務表';
 //本文
 $body ="真鍋さん。軽部さん。
 
お疲れ様です。$staff_name です。
勤務表の入力が完了しました。ご確認よろしくお願いいたします。";

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
print ("メッセージを送信しました。\n");
}
?>
<div class="img_class">
	<img class="img" src="/img/image_2020_4_10.png" alt="ロゴ">
</div>
<br/>
<div class="wrapper">
	<h1>勤務表の入力が完了しました。</h1>
<!--ログアウトする!-->
	<form method="post" action="rogaut.php">
		<button class="done" type="submit">ログアウト</button>
	</form>
	</div>
</div>
</body>
</html>