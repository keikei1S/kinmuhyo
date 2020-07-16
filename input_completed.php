<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
$first_date = date("Y-m-01");

try{
	$dsn='mysql:dbname=kinmuhyo;host=localhost;charset=utf8';
	$user='root';
	$password='';
	$dbh= new PDO($dsn,$user,$password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql="INSERT INTO `TBL_SUMMARY`(`staff_number`, `year_and_month`, `work_ID`, `remaining_paid_days`, `status`, `opening_hours`, `closing_hours`, `create_date`, `update_date`) VALUES (:staff_number, :year_and_month, :work_ID, :remaining_paid_days, :status, :opening_hours, :closing_hours, now(), now())on duplicate key update staff_number=:staff_number, year_and_month = :year_and_month, work_ID = :work_ID, remaining_paid_days = :remaining_paid_days, status = :status, opening_hours = :opening_hours, closing_hours = :closing_hours";
	$stmt=$dbh->prepare($sql);
 	$params =array('staff_number' => $staff_number,'year_and_month' => $first_date, 'work_ID' => $work_id, 'remaining_paid_days' => '11', 'status' => '2', 'opening_hours' => $opening, 'closing_hours' => $closong);
		$stmt->execute($params);
		$dbh=null;
	}	
	catch(Exception $e){	
		var_dump($e);
	print 'システムエラーが発生しました。';
	exit();
		}

 $from = $email;
 $host = "ssl://smtp.gmail.com";
 $port = "465";
 $username = $email;
 $password = 'Keiichi1106';
 $to = '6656keiichi@gmail.com';
 $subject = '勤務表';
  $body ='勤務表の入力が完了しました。';

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
<h1>勤務表の入力が完了しました。</h1>

</body>
</html>