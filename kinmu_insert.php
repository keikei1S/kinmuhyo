<?
if(!isset($_SESSION)){
	session_start();
}
//ファイル読み込み
ob_start();
include("kinmuhyo.php");
ob_clean();
require_once('kinmu_common.php');

//変数の整理S//
$staff_number = $_SESSION['staff_number'];
$get_month = $_SESSION['get_month'];
$naiyou = $_SESSION['naiyou'];
$open = $_SESSION['open'];
$open2 = $_SESSION['open2'];
$close = $_SESSION['close'];
$close2 = $_SESSION['close2'];
$rest = $_SESSION['rest'];
$rest2 = $_SESSION['rest2'];
$total = $_SESSION['total'];
$overtime = $_SESSION['overtime'];
$overtime_night = $_SESSION['overtime_night'];
$Shortage = $_SESSION['Shortage'];
$bikou = $_SESSION['bikou'];
$holiday = $_SESSION['holiday'];
$check = $_SESSION['check'];
$shift = $_SESSION['shift'];
//変数の整理E//

//insert用日付S///
//今月末
$now_month = date('t', strtotime($get_month));
//insert用日付E///

//勤務表テーブルに挿入
for ($i=0; $i < $now_month; $i++) {
	try{
	$dsn='mysql:dbname=kinmuhyo;host=localhost;charset=utf8';
	$user='root';
	$password='';
	$dbh= new PDO($dsn,$user,$password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//レコードがなければinsert。レコードがあればupdateする。
		$sql="INSERT INTO `TBL_ATTENDANCE`(`staff_number`, `year_and_month`, `content`, `opening_hours`, `closing_hours`, `break_time`, `total`, `overtime_normal`, `overtime_night`, `short`, `bikou`, `vacation`, `check_result`, `shift`, `create_date`, `update_date`) VALUES (:staff_number, :year_and_month, :content, :opening_hours, :closing_hours, :break_time, :total, :overtime_normal, :overtime_night, :short, :bikou, :vacation, :check_result, :shift, now(), now())on duplicate key update staff_number=:staff_number, content = :content, opening_hours = :opening_hours, closing_hours = :closing_hours, break_time = :break_time, total = :total, overtime_normal = :overtime_normal, overtime_night = :overtime_night, short=:short, bikou =:bikou, vacation = :vacation, check_result = :check_result, shift = :shift";
		$stmt=$dbh->prepare($sql);
		$params = array('staff_number' => $staff_number,'year_and_month' => $get_month, 'content' => $naiyou[$i], 'opening_hours' => $open[$i].$open2[$i]."00", 'closing_hours' => $close[$i].$close2[$i]."00", 'break_time' => $rest[$i].$rest2[$i]."00", 'total' => $total[$i], 'overtime_normal' => $overtime[$i], 'overtime_night' => $overtime_night[$i], 'short'=>$Shortage[$i],'bikou' =>$bikou[$i], 'vacation' =>$holiday[$i], 'check_result' => $check[$i], 'shift' => $shift[$i]);
		$stmt->execute($params);
		$dbh = null;
	}catch(Exception $e){
		var_dump($e);
		print 'システムエラーが発生しました。';
		exit();
	}
	$get_month++;
}
//明細テーブルにインサート
$summary= kinmu_common::INSERT_Summary($result['staff_number']);
print "勤務表を保存しました。";
header('Location:kinmuhyo.php');
exit;
?>
