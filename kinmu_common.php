<?php
//表示月
//初期表示は今月
if(empty($_SESSION["month"])){
	$_SESSION["month"]=date('m');
}
if(isset($_POST["show"])){
	if($_POST["show"]=="1"){
		$_SESSION["month"]=date('m');
	}elseif($_POST["show"]=="2"){
		$_SESSION["month"]=date('m', strtotime('-1 month'));
	}else{
		if($_SESSION["month"]==date('m')){
			$_SESSION["month"]=date('m');
		}else{
		$_SESSION["month"]=date('m', strtotime('-1 month'));
		}
	}
}
class kinmu_common{
	/**
	*ログインしたユーザーの社員番号に紐づく勤務表テーブルを取得する。
	*/
	public static function Kinmuhyo($staff_number){
	$rec = false;

	$dsn='mysql:dbname=kinmuhyo;host=localhost;charset=utf8';
	$user='root';
	$password='';
	$dbh= new PDO($dsn,$user,$password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql="SELECT * FROM TBL_SUMMARY WHERE staff_number=? ";
	try{
		$stmt=$dbh->prepare($sql);
		$data[]=$staff_number;
		$stmt->execute($data);
		$rec = $stmt -> fetch(PDO::FETCH_ASSOC);
		$dbh=null;
		return $rec;
	}
	catch(Exception $e)
{
	return $rec;
	print 'システムエラーが発生しました。';
	exit();

		}
	}
	public static function work_tbl($staff_number){
	$rec = false;

	$dsn='mysql:dbname=kinmuhyo;host=localhost;charset=utf8';
	$user='root';
	$password='';
	$dbh= new PDO($dsn,$user,$password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql="SELECT work_name FROM `TBL_BELONGSS` WHERE 1";
	try{
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
		$rec = $stmt -> fetchAll(PDO::FETCH_ASSOC);
		$dbh=null;
		return $rec;
	}
	catch(Exception $e)
{
	return $rec;
	print 'システムエラーが発生しました。';
	exit();

		}
}

	//勤務表テーブルの取得する
	public static function Attendance($staff_number){
	$s_year_and_month = date("Y-").$_SESSION['month'].date("-01");
	$now_month = date('t', strtotime($s_year_and_month));
	$e_year_and_month = date("Y-").$_SESSION['month'].date("-".$now_month);
	date_default_timezone_set('Asia/Tokyo');
	$rec = false;
	$dsn='mysql:dbname=kinmuhyo;host=localhost;charset=utf8';
	$user='root';
	$password='';
	$dbh= new PDO($dsn,$user,$password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql="SELECT * FROM `TBL_ATTENDANCE` WHERE staff_number=:staff_number AND year_and_month BETWEEN :s_year_and_month AND :e_year_and_month";
	try{
		$stmt=$dbh->prepare($sql);
		$stmt->bindValue(":staff_number",$staff_number,PDO::PARAM_STR);
		$stmt->bindValue(":s_year_and_month",$s_year_and_month,PDO::PARAM_STR);
		$stmt->bindValue(":e_year_and_month",$e_year_and_month,PDO::PARAM_STR);
		$stmt->execute();
		$rec = $stmt -> fetchAll(PDO::FETCH_ASSOC);
		$dbh=null;
		return $rec;
	}
	catch(Exception $e)
{
	return $rec;
	print 'システムエラーが発生しました。';
	exit();

		}
	}
	public static function BELONGSS($work_id){
	$rec = false;

	$dsn='mysql:dbname=kinmuhyo;host=localhost;charset=utf8';
	$user='root';
	$password='';
	$dbh= new PDO($dsn,$user,$password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql="SELECT * FROM TBL_BELONGSS WHERE work_id=?";
	try{
		$stmt=$dbh->prepare($sql);
		$data[]=$work_id;
		$stmt->execute($data);
		$rec = $stmt -> fetch(PDO::FETCH_ASSOC);
		$dbh=null;
		return $rec;
	}
	catch(Exception $e)
{
	return $rec;
	print 'システムエラーが発生しました。';
	exit();

		}
	}
//勤務表テーブルに格納されている実働時間の合計を取得する
	public static function sum_total($staff_number){
	$rec = false;
	$s_year_and_month = date("Y-").$_SESSION['month'].date("-01");
	$now_month = date('t', strtotime($s_year_and_month));
	$e_year_and_month = date("Y-").$_SESSION['month'].date("-".$now_month);
	$dsn='mysql:dbname=kinmuhyo;host=localhost;charset=utf8';
	$user='root';
	$password='';
	$dbh= new PDO($dsn,$user,$password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql="SELECT sum( time_to_sec(total)) as total_sec, sec_to_time(sum( time_to_sec(total))) as total_time from TBL_ATTENDANCE WHERE staff_number=:staff_number AND year_and_month BETWEEN :s_year_and_month AND :e_year_and_month";
	try{
		$stmt=$dbh->prepare($sql);
		$stmt->bindValue(":staff_number",$staff_number,PDO::PARAM_STR);
		$stmt->bindValue(":s_year_and_month",$s_year_and_month,PDO::PARAM_STR);
		$stmt->bindValue(":e_year_and_month",$e_year_and_month,PDO::PARAM_STR);
		$stmt->execute();
		$rec = $stmt -> fetch(PDO::FETCH_ASSOC);
		$dbh=null;
		return $rec;
	}
	catch(Exception $e)
{
	return $rec;
	print 'システムエラーが発生しました。';
	exit();

		}
	}
	//勤務表テーブルに格納されている残業時間の合計を取得する
	public static function sum_overtime($staff_number){
	$rec = false;
	$s_year_and_month = date("Y-").$_SESSION['month'].date("-01");
	$now_month = date('t', strtotime($s_year_and_month));
	$e_year_and_month = date("Y-").$_SESSION['month'].date("-".$now_month);
	$dsn='mysql:dbname=kinmuhyo;host=localhost;charset=utf8';
	$user='root';
	$password='';
	$dbh= new PDO($dsn,$user,$password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql="SELECT sum( time_to_sec(overtime_normal)) as total_sec, sec_to_time(sum( time_to_sec(overtime_normal))) as total_time from TBL_ATTENDANCE WHERE staff_number=:staff_number AND year_and_month BETWEEN :s_year_and_month AND :e_year_and_month";
	try{
		$stmt=$dbh->prepare($sql);
		$stmt->bindValue(":staff_number",$staff_number,PDO::PARAM_STR);
		$stmt->bindValue(":s_year_and_month",$s_year_and_month,PDO::PARAM_STR);
		$stmt->bindValue(":e_year_and_month",$e_year_and_month,PDO::PARAM_STR);
		$stmt->execute();
		$rec = $stmt -> fetch(PDO::FETCH_ASSOC);
		$dbh=null;
		return $rec;
	}
	catch(Exception $e)
{
	return $rec;
	print 'システムエラーが発生しました。';
	exit();

		}
	}
	//勤務表テーブルに格納されている深夜残業時間の合計を取得する
	public static function sum_overtime_night($staff_number){
	$rec = false;
	$s_year_and_month = date("Y-").$_SESSION['month'].date("-01");
	$now_month = date('t', strtotime($s_year_and_month));
	$e_year_and_month = date("Y-").$_SESSION['month'].date("-".$now_month);
	$dsn='mysql:dbname=kinmuhyo;host=localhost;charset=utf8';
	$user='root';
	$password='';
	$dbh= new PDO($dsn,$user,$password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql="SELECT sum( time_to_sec(overtime_night)) as total_sec, sec_to_time(sum( time_to_sec(overtime_night))) as total_time from TBL_ATTENDANCE WHERE staff_number=:staff_number AND year_and_month BETWEEN :s_year_and_month AND :e_year_and_month";
	try{
		$stmt=$dbh->prepare($sql);
		$stmt->bindValue(":staff_number",$staff_number,PDO::PARAM_STR);
		$stmt->bindValue(":s_year_and_month",$s_year_and_month,PDO::PARAM_STR);
		$stmt->bindValue(":e_year_and_month",$e_year_and_month,PDO::PARAM_STR);
		$stmt->execute();
		$rec = $stmt -> fetch(PDO::FETCH_ASSOC);
		$dbh=null;
		return $rec;
	}
	catch(Exception $e)
{
	return $rec;
	print 'システムエラーが発生しました。';
	exit();

		}
	}
	//勤務表テーブルに格納されている不足時間の合計を取得する
	public static function sum_short($staff_number){
	$rec = false;
	$s_year_and_month = date("Y-").$_SESSION['month'].date("-01");
	$now_month = date('t', strtotime($s_year_and_month));
	$e_year_and_month = date("Y-").$_SESSION['month'].date("-".$now_month);
	$dsn='mysql:dbname=kinmuhyo;host=localhost;charset=utf8';
	$user='root';
	$password='';
	$dbh= new PDO($dsn,$user,$password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql="SELECT sum( time_to_sec(short)) as total_sec, sec_to_time(sum( time_to_sec(short))) as total_time from TBL_ATTENDANCE WHERE staff_number=:staff_number AND year_and_month BETWEEN :s_year_and_month AND :e_year_and_month";
	try{
		$stmt=$dbh->prepare($sql);
		$stmt->bindValue(":staff_number",$staff_number,PDO::PARAM_STR);
		$stmt->bindValue(":s_year_and_month",$s_year_and_month,PDO::PARAM_STR);
		$stmt->bindValue(":e_year_and_month",$e_year_and_month,PDO::PARAM_STR);
		$stmt->execute();
		$rec = $stmt -> fetch(PDO::FETCH_ASSOC);
		$dbh=null;
		return $rec;
	}
	catch(Exception $e)
{
	return $rec;
	print 'システムエラーが発生しました。';
	exit();

		}
	}
public static function INSERT_Summary($staff_number){
	$s_year_and_month = date("Y-").$_SESSION['month'].date("-01");
	try{
	$dsn='mysql:dbname=kinmuhyo;host=localhost;charset=utf8';
	$user='root';
	$password='';
	$dbh= new PDO($dsn,$user,$password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql="INSERT INTO `TBL_SUMMARY`(`staff_number`, `year_and_month`, `work_ID`, `remaining_paid_days`, `status`, `opening_hours`, `closing_hours`, `create_date`, `update_date`) VALUES (:staff_number, :year_and_month, :work_ID, :remaining_paid_days, :status, :opening_hours, :closing_hours, now(), now())on duplicate key update staff_number=:staff_number, year_and_month = :year_and_month, work_ID = :work_ID, remaining_paid_days = :remaining_paid_days, status = :status, opening_hours = :opening_hours, closing_hours = :closing_hours";
	$stmt=$dbh->prepare($sql);
 	$params =array('staff_number' => $staff_number,'year_and_month' => $s_year_and_month, 'work_ID' => $_SESSION['work_id'], 'remaining_paid_days' => $_SESSION['yukyuzan'], 'status' => '1', 'opening_hours' => $_SESSION['opening'], 'closing_hours' => $_SESSION['closong']);
		$stmt->execute($params);
		$dbh=null;
	}
	catch(Exception $e){
		var_dump($e);
	print 'システムエラーが発生しました。';
	exit();
		}

}
}

//祝日テーブルに格納されている値を取得する
class kinmu_holiday{
	public static function Holiday($q){
	$q = false;
	try{
		$dsn='mysql:dbname=kinmuhyo;host=localhost;charset=utf8';
		$user='root';
		$password='';
		$dbh= new PDO($dsn,$user,$password);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql='SELECT * FROM TBL_HOLIDAY';
		$stmt=$dbh->query($sql);
		foreach ($stmt as $val) {
    	$q[]=($val['day']);
		}
		return $q;
	}
	catch(Exception $e)
	{
	return $rec;
	print 'システムエラーが発生しました。';
	exit();

		}
	}
}
