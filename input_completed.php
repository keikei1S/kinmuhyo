<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
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
include("kinmuhyo.php");
ob_clean();
$result=$_SESSION['result'];
$staff_number=$result['staff_number'];
$staff_name=$result['familyname'].$result['firstname'];
$email=$result['email'];
$s_year_and_month = date("Y-").$_SESSION['month'].date("-01");
//有給残数から有給休暇数を引いた値をテーブルに格納する
$staff_table= kinmu_common::staff_table($staff_number);
$remaining = $staff_table["holiday_with_pay"];

try{
	$send_date = date("Y-m-d");
	$dbh = db_connect();
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//$sql="INSERT INTO `TBL_SUMMARY`(`staff_number`, `year_and_month`, `remaining_paid_days`, `status`,`create_date`, `update_date`) VALUES (:staff_number, :year_and_month, :remaining_paid_days, :status, now(), now())on duplicate key update staff_number=:staff_number, year_and_month = :year_and_month, remaining_paid_days = :remaining_paid_days, status = :status";
	$sql = "UPDATE TBL_SUMMARY SET remaining_paid_days=:remaining_paid_days, status=:status, send_date=:send_date WHERE staff_number=:staff_number AND year_and_month=:year_and_month";
	$stmt=$dbh->prepare($sql);
 	$params =array('staff_number' => $staff_number,'year_and_month' => $s_year_and_month, 'remaining_paid_days' => $remaining, 'status' => '2','send_date' => $send_date);
		$stmt->execute($params);
		$dbh=null;
	}
	catch(Exception $e){
	header('Location: err_report.php');
  exit();
	}

//メール処理
//誰から
 $from = $email;
 //smtサーバーの設定
 $host = "ssl://smtp.gmail.com";
 //port番号の設定
 $port = "465";
 //送信元のアドレス
 $username = "prossystem.test@gmail.com";
 //送信元アドレスのパスワード
 $password = 'prostest';
 //宛先（この場合管理者（真鍋さんと軽部さん？？）増える場合もあり？？？）
 $to = "goh.karube@pro-s.co.jp , $email";
 //必要があればcc,bcc,の設定要
 //$cc = $email;
 // $bcc = 'aaa@gmail.com';
 //件名
 $subject = substr($s_year_and_month, 0, 7).'月分勤務表'."(".$staff_name.")";
 //本文
 $body ="真鍋さん。軽部さん。

お疲れ様です。$staff_name です。
勤務表の入力が完了しました。ご確認よろしくお願いいたします。
https://www.pros-service.co.jp/kinmu/staff_login.php


※本メールは送信専用のため、ご返信いただけません。
※ご返信の際は、お手数ですが下記からお願いいたします。
◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆
株式会社プロズサービス
$staff_name ($email)　　　　　　　　　　　　　　　　　

　ISO/IEC 27001:2013 JIS Q 27001:2014 認証登録番号：IA 160164a

　〒141-0022 東京都品川区東五反田1-11-15 電波ビル 4F
　tel:03-5789-2188/fax:03-5789-2901
https://www.pros-service.co.jp/
◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆
";
//メールヘッダの設定
  $headers = array ('From' => $from, 'To' => $to, 'Subject' => $subject);
//メール送信
	$smtp = Mail::factory('smtp',
  array ('host' => $host,
  'port' => $port,
  'auth' => true,
  'username' => $username,
  'password' => $password));

  $mail = $smtp->send($to, $headers, $body);

if (PEAR::isError($mail)) {
echo($mail->getMessage());
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
<?php
//セッションを削除することでブラウザバック、リロードをした場合自動的にログイン画面に戻る
session_destroy();
 ?>
