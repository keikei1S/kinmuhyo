<?php
if(!isset($_SESSION)){
	session_start();
}
if(!isset($_SESSION["login"])){
	header('Location:staff_login.php');
	exit;
}
//ファイル読み込み
	require_once('kinmu_common.php');
//変数の整理S//
	$staff_number = $_SESSION['staff_number'];
	$first_date = $_SESSION['get_month'];
	$naiyou = $_SESSION['naiyou'];
	$open_ampm = $_SESSION['open_ampm'];
	$open = $_SESSION['open'];
	$close_ampm = $_SESSION['close_ampm'];
	$close = $_SESSION['close'];
	$rest = $_SESSION['rest'];
	$total = $_SESSION['total'];
	$overtime = $_SESSION['overtime'];
	$overtime_night = $_SESSION['overtime_night'];
	$Shortage = $_SESSION['Shortage'];
	$bikou = $_SESSION['bikou'];
	$holiday = $_SESSION['holiday'];
	$shift = $_SESSION['shift'];
	$err_msg = $_SESSION['err_msg'];
	$y_kyuka = $_SESSION['y_kyuka'];
	$check = $_SESSION['check'];
//変数の整理E//
//insert用日付S///
//今月末


	$now_month = date('t', strtotime($first_date));
//insert用日付E//
//勤務表テーブルに挿入
	for ($i=0; $i < $now_month; $i++) {
		if($check[$i]=="NG"){
			$bikou[$i]=$err_msg[$i];
		}
		try{
			$dbh = db_connect();
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//レコードがなければinsert。レコードがあればupdateする。
			$sql="INSERT INTO `TBL_ATTENDANCE`(`staff_number`, `year_and_month`, `content`, `open_ampm`, `opening_hours`, `close_ampm`, `closing_hours`, `break_time`, `total`, `overtime_normal`, `overtime_night`, `short`, `bikou`, `vacation`, `check_result`, `shift`, `create_date`, `update_date`)
			VALUES (:staff_number, :year_and_month, :content, :open_ampm, :opening_hours, :close_ampm, :closing_hours, :break_time, :total, :overtime_normal, :overtime_night, :short, :bikou, :vacation, :check_result, :shift, now(), now())
			on duplicate key update staff_number= :staff_number, year_and_month = :year_and_month, content = :content, open_ampm = :open_ampm, opening_hours = :opening_hours, close_ampm = :close_ampm, closing_hours = :closing_hours, break_time = :break_time, total = :total, overtime_normal = :overtime_normal, overtime_night = :overtime_night, short=:short, bikou =:bikou, vacation = :vacation, check_result = :check_result, shift = :shift";
			$stmt=$dbh->prepare($sql);
			$params =array('staff_number' => $staff_number, 'year_and_month' => $first_date, 'content' => $naiyou[$i], 'open_ampm' => $open_ampm[$i], 'opening_hours' => $open[$i], 'close_ampm' => $close_ampm[$i], 'closing_hours' => $close[$i], 'break_time' => $rest[$i], 'total' => $total[$i], 'overtime_normal' => $overtime[$i], 'overtime_night' => $overtime_night[$i], 'short' => $Shortage[$i], 'bikou' => $bikou[$i], 'vacation' => $holiday[$i], 'check_result' => $check[$i], 'shift' => $shift[$i]);
			$stmt->execute($params);
			$dbh = null;
		}catch(Exception $e){
			header('Location: err_report.php');
  exit();
		}
		$first_date++;
	}
//明細テーブルにインサート
	$summary= kinmu_common::INSERT_Summary($staff_number);
	unset($_SESSION["yukyu_err"]);
	header('Location:kinmuhyo_done.php');
	exit;
?>