<?php
//表示月
//初期表示は今月
if(empty($_SESSION["month"])){
	$_SESSION["month"]=date('m');
}
if(isset($_POST["show"])){
	$_SESSION["show"]=$_POST["show"];
	if($_POST["show"]=="1"){
		$_SESSION["month"]=date('m');
		unset($_SESSION['check']);
		unset($_SESSION['err_msg']);
	}elseif($_POST["show"]=="2"){
		$_SESSION["month"]=date('m', strtotime('-1 month'));
		unset($_SESSION['check']);
		unset($_SESSION['err_msg']);
	}else{
		if($_SESSION["month"]==date('m')){
			$_SESSION["month"]=date('m');
			unset($_SESSION['check']);
			unset($_SESSION['err_msg']);
		}else{
		$_SESSION["month"]=date('m', strtotime('-1 month'));
		unset($_SESSION['check']);
		unset($_SESSION['err_msg']);
		}
	}
}

//時間計算
//足し算の関数
function AddVtime1($a,$b){
	$aArry=explode(":",$a);
	$bArry=explode(":",$b);

	return
	date("H:i",mktime($aArry[0]+$bArry[0],$aArry[1]+$bArry[1]));
}
//引き算の関数(シフト勤務時間調整用)
function minVtime1($a,$b){
	$aArry=explode(":",$a);
	$bArry=explode(":",$b);

	return
	date("H:i",mktime($aArry[0]-$bArry[0],$aArry[1]-$bArry[1]));
}

//引き算の関数
function MinusVtime($a,$b){
	$objDatetime1 = new DateTime(date(("Y-m-d H:i"),mktime(explode(":", $a)[0],explode(":", $a)[1])));
	$objDatetime2 = new DateTime(date(("Y-m-d H:i"),mktime(explode(":", $b)[0],explode(":", $b)[1])));
//ふたつの日付の差をあらわす DateInterval オブジェクトを返す。
	$objInterval = $objDatetime1->diff($objDatetime2);
//日跨ぎの場合
	//$day_difference = $objInterval->format('%d');
//実働時間
return
	$time_day_difference = $objInterval->format('%H:%I');
	// return array($day_difference, $time_day_difference);
	//	return[$objInterval , $time_day_difference]

}

//出退勤時間の配列
function Time_select($t){
	for ($i = 0; $i <=12; $i++) {
		for ($k = 0; $k < 5 * 12 * 12; $k += 15) {
			$start_time[] = date('H:i', strtotime("+{$k} minutes", $t));
		}
		return $start_time;
	}
}


//休憩時間の配列
function rest_time($t){
	for ($i = 0; $i <=3; $i++) {
		for ($k = 0; $k <= 5 * 12 * 4; $k += 30) {
			$rest_time[] = date('H:i', strtotime("+{$k} minutes", $t));
		}
		return $rest_time;
	}
}

//勤務時間
function work_time($t){
	for ($i = 0; $i <=3; $i++) {
		for ($k = 0; $k <= 5 * 12 * 2; $k += 30) {
			$rest_time[] = date('H:i', strtotime("+{$k} minutes", $t));
		}
		return $rest_time;
	}
}


//DB接続の関数(ローカル)
// define('DB_USERNAME', 'root');
// define('DB_PASSWORD', '');
// define('DSN', 'mysql:dbname=kinmuhyo;host=localhost;charset=utf8');

//DB接続の関数(サーバー)
define('DB_USERNAME', 'pros-service');
define('DB_PASSWORD', 'p9bkubn8pg');
define('DSN', 'mysql:dbname=pros-service_kinmu;host=mysql731.db.sakura.ne.jp;charset=utf8');

function db_connect(){
    $dbh = new PDO(DSN, DB_USERNAME, DB_PASSWORD);
    return $dbh;
}


class kinmu_common{
	/**
	*社員番号に紐づく社員テーブルの値を取得する。
	*/
	public static function staff_table($staff_number){
		$rec = false;
	try{
		$dbh = db_connect();
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql="SELECT `staff_number`, `familyname`, `firstname`, `email`,`admin_flag`, `holiday_with_pay`, `new_work_id`, `new_start_month`, `old_work_id`, `old_start_month`, `old_end_month` FROM `TBL_STAFF` WHERE staff_number=:staff_number";
		$stmt=$dbh->prepare($sql);
		$stmt->bindValue(":staff_number",$staff_number,PDO::PARAM_STR);
		$stmt->execute();
		$rec = $stmt -> fetch(PDO::FETCH_ASSOC);
		$dbh=null;
		return $rec;
	}catch(Exception $e){
		header('Location: err_report.php');
		exit();
	}
	}

	/**
	*ログインしたユーザーの社員番号に紐づく勤務表サマリーテーブルを取得する。
	*/
	public static function Kinmuhyo($staff_number){
	$rec = false;
	if(isset($_SESSION["thuki"])){
		$select_month = $_SESSION["thuki"].date("-01");
	}else{
		$select_month = date("Y-").$_SESSION["month"].date("-01");
		if(date("m")=="01"){
			if($select_month > date("Y-m-d")){
				$day = new DateTime($select_month);
				$select_month = $day->modify('-1 year')->format('Y-m-d');
			}
		}
	}

	$dbh = db_connect();
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql="SELECT * FROM TBL_SUMMARY WHERE staff_number=:staff_number AND year_and_month=:year_and_month ";
	try{
		$stmt=$dbh->prepare($sql);
		$stmt->bindValue(':staff_number' , $staff_number , PDO::PARAM_INT);
		$stmt->bindValue(':year_and_month' , $select_month , PDO::PARAM_STR);

		$stmt->execute();
		$rec = $stmt -> fetch(PDO::FETCH_ASSOC);

		}catch(Exception $e){
			return $rec;
			header('Location: err_report.php');
		exit();
		}
		if($rec==false){
			$select_month = date('Y-m-d', strtotime('first day of previous month'));
			if(date("m")=="01"){
				if($select_month > date("Y-m-d")){
					$day = new DateTime($select_month);
					$select_month = $day->modify('-1 year')->format('Y-m-d');
				}
			}
			$sql="SELECT * FROM TBL_SUMMARY WHERE staff_number=:staff_number AND year_and_month=:year_and_month ";
			try{
				$stmt=$dbh->prepare($sql);
				$stmt->bindValue(':staff_number' , $staff_number , PDO::PARAM_INT);
				$stmt->bindValue(':year_and_month' , $select_month , PDO::PARAM_STR);

				$stmt->execute();
				$rec = $stmt -> fetch(PDO::FETCH_ASSOC);
			}catch(Exception $e){
				return $rec;
				header('Location: err_report.php');
		exit();
			}
		}
		$dbh=null;
		return $rec;
	}
	public static function work_tbl($staff_number){
	$rec = false;

	$dbh = db_connect();
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
	header('Location: err_report.php');
		exit();

		}
}

	//勤務表テーブルの取得する
	public static function Attendance($staff_number){
	if(isset($_SESSION["thuki"])){
		$s_year_and_month = $_SESSION["thuki"].date("-01");
		$now_month = date('t', strtotime($s_year_and_month));
		$e_year_and_month = $_SESSION["thuki"].date("-".$now_month);
	}else{
		$s_year_and_month = date("Y-").$_SESSION['month'].date("-01");
		if(date("m")=="01"){
			if($s_year_and_month > date("Y-m-01")){
				$day = new DateTime($s_year_and_month);
				$s_year_and_month = $day->modify('-1 year')->format('Y-m-d');
			}
		}
		$now_month = date('t', strtotime($s_year_and_month));
		$e_year_and_month = date("Y-").$_SESSION['month'].date("-".$now_month);
		if(date("m")=="01"){
			if($e_year_and_month > date("Y-m-31")){
				$day = new DateTime($e_year_and_month);
				$e_year_and_month = $day->modify('-1 year')->format('Y-m-d');
			}
		}
	}
	date_default_timezone_set('Asia/Tokyo');
	$rec = false;
	$dbh = db_connect();
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
	header('Location: err_report.php');
		exit();

		}
	}
	//勤務地テーブルを取得
	public static function BELONGSS($work_id){
	$rec = false;

	$dbh = db_connect();
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql="SELECT * FROM TBL_BELONGSS WHERE work_id=?";
	try{
		$stmt=$dbh->prepare($sql);
		$data[]=$_SESSION['work_id'];
		$stmt->execute($data);
		$rec = $stmt -> fetch(PDO::FETCH_ASSOC);
		$dbh=null;
		return $rec;
	}
	catch(Exception $e)
{
	return $rec;
	header('Location: err_report.php');
		exit();

		}
	}
//勤務表テーブルに格納されている実働時間の合計を取得する
	public static function sum_total($staff_number){
	$rec = false;
	$s_year_and_month = date("Y-").$_SESSION['month'].date("-01");
	if(date("m")=="01"){
		if($s_year_and_month > date("Y-m-01")){
			$day = new DateTime($s_year_and_month);
			$s_year_and_month = $day->modify('-1 year')->format('Y-m-d');
		}
	}
	$now_month = date('t', strtotime($s_year_and_month));
	$e_year_and_month = date("Y-").$_SESSION['month'].date("-".$now_month);
	if(date("m")=="01"){
		if($e_year_and_month > date("Y-m-31")){
			$day = new DateTime($e_year_and_month);
			$e_year_and_month = $day->modify('-1 year')->format('Y-m-d');
		}
	}
	$dbh = db_connect();
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
	header('Location: err_report.php');
		exit();
		}
	}
	//勤務表テーブルに格納されている残業時間の合計を取得する
	public static function sum_overtime($staff_number){
	$rec = false;
	$s_year_and_month = date("Y-").$_SESSION['month'].date("-01");
	if(date("m")=="01"){
		if($s_year_and_month > date("Y-m-01")){
			$day = new DateTime($s_year_and_month);
			$s_year_and_month = $day->modify('-1 year')->format('Y-m-d');
		}
	}
	$now_month = date('t', strtotime($s_year_and_month));
	$e_year_and_month = date("Y-").$_SESSION['month'].date("-".$now_month);
	if(date("m")=="01"){
		if($e_year_and_month > date("Y-m-31")){
			$day = new DateTime($e_year_and_month);
			$e_year_and_month = $day->modify('-1 year')->format('Y-m-d');
		}
	}
	$dbh = db_connect();
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
	header('Location: err_report.php');
		exit();

		}
	}
	//勤務表テーブルに格納されている深夜残業時間の合計を取得する
	public static function sum_overtime_night($staff_number){
	$rec = false;
	$s_year_and_month = date("Y-").$_SESSION['month'].date("-01");
	if(date("m")=="01"){
		if($s_year_and_month > date("Y-m-01")){
			$day = new DateTime($s_year_and_month);
			$s_year_and_month = $day->modify('-1 year')->format('Y-m-d');
		}
	}
	$now_month = date('t', strtotime($s_year_and_month));
	$e_year_and_month = date("Y-").$_SESSION['month'].date("-".$now_month);
	if(date("m")=="01"){
		if($e_year_and_month > date("Y-m-31")){
			$day = new DateTime($e_year_and_month);
			$e_year_and_month = $day->modify('-1 year')->format('Y-m-d');
		}
	}
	$dbh = db_connect();
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
	header('Location: err_report.php');
		exit();

		}
	}
	//勤務表テーブルに格納されている不足時間の合計を取得する
	public static function sum_short($staff_number){
	$rec = false;
	$s_year_and_month = date("Y-").$_SESSION['month'].date("-01");
	if(date("m")=="01"){
		if($s_year_and_month > date("Y-m-01")){
			$day = new DateTime($s_year_and_month);
			$s_year_and_month = $day->modify('-1 year')->format('Y-m-d');
		}
	}
	$now_month = date('t', strtotime($s_year_and_month));
	$e_year_and_month = date("Y-").$_SESSION['month'].date("-".$now_month);
	if(date("m")=="01"){
		if($e_year_and_month > date("Y-m-31")){
			$day = new DateTime($e_year_and_month);
			$e_year_and_month = $day->modify('-1 year')->format('Y-m-d');
		}
	}
	$dbh = db_connect();
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
	header('Location: err_report.php');
		exit();

		}
	}
public static function INSERT_Summary($staff_number){
	$s_year_and_month = date("Y-").$_SESSION['month'].date("-01");
	if(date("m")=="01"){
		if($s_year_and_month > date("Y-m-01")){
			$day = new DateTime($s_year_and_month);
			$s_year_and_month = $day->modify('-1 year')->format('Y-m-d');
		}
	}
	try{
	$dbh = db_connect();
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql="INSERT INTO `TBL_SUMMARY`(`staff_number`, `year_and_month`, `remaining_paid_days`, `status`,`print_log`,`send_date`,`create_date`, `update_date`) VALUES (:staff_number, :year_and_month, :remaining_paid_days, :status,:print_log,:send_date, now(), now())on duplicate key update staff_number=:staff_number, year_and_month = :year_and_month, remaining_paid_days = :remaining_paid_days, status = :status,print_log = :print_log,send_date = :send_date";
	$stmt=$dbh->prepare($sql);
 	$params =array('staff_number' => $staff_number,'year_and_month' => $s_year_and_month,'remaining_paid_days' => $_SESSION["yukyu"], 'status' => '1','print_log' => '0','send_date' => $s_year_and_month);
		$stmt->execute($params);
		$dbh=null;
	}
	catch(Exception $e){
		header('Location: err_report.php');
		exit();
		}

}
}

//祝日テーブルに格納されている値を取得する
class kinmu_holiday{
	public static function Holiday($q){
	$q = false;
	try{
	$dbh = db_connect();
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
	header('Location: err_report.php');
	exit();

		}
	}
}
