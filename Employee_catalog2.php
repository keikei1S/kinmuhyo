<?php
//新規社員の追加
error_reporting(8192);
date_default_timezone_set('Asia/Tokyo');
//セッションが開始されていなければセッションを開始する。
if(!isset($_SESSION)){
	session_start();
	session_regenerate_id(true);
}
if (isset($_SESSION["login"])==false)
{
	header("Location: staff_login.php");
	exit();
}
require_once("kinmu_common.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
</head>
<body>
	<?php
	if(isset($_SESSION["post"])){
		$modify = $_SESSION["result"]["staff_number"];
		$staff = $_SESSION["post"]['staff_number'];
		$username = $_SESSION["post"]['familyname'];
		$username3=$_SESSION["post"]['familyname_kana'];
		$username2=$_SESSION["post"]['firstname'];
		$username4=$_SESSION["post"]['firstname_kana'];
		$email=$_SESSION["post"]['email'];
		$nyuusha=$_SESSION["post"]['hire_date'];
		$taisha="9999-12-31";
		$yuukyuu=$_SESSION["post"]['holiday_with_pay'];
		$admin_flag = $_SESSION["post"]['admin_flag'];
		$new_work_id = $_SESSION["post"]['new_work_id'];
		$new_start_month = $_SESSION["post"]['new_start_month'];
		$new_end_month = "9999-12-31";
		if($_SESSION["post"]['old_work_id']!=""){
			$old_work_id = $_SESSION["post"]['old_work_id'];
		}else{
			$old_work_id = "";
		}
		$old_work_id=htmlspecialchars($old_work_id,ENT_QUOTES,'UTF-8');
		if($_SESSION["post"]['old_start_month']!=""){
		  $old_start_month = $_SESSION["post"]['old_start_month'];
		}else{
			$old_start_month="";
		}
		$old_start_month=htmlspecialchars($old_start_month,ENT_QUOTES,'UTF-8');
		if($_SESSION["post"]['old_end_month']!=""){
			$old_end_month = $_SESSION["post"]['old_end_month'];
		}else{
			$old_end_month="";
		}
		$old_end_month=htmlspecialchars($old_end_month,ENT_QUOTES,'UTF-8');
		$passwords = "abc12345678";
		$passwords = password_hash($passwords, PASSWORD_DEFAULT);
		$_SESSION["newRegister"] = $_SESSION["post"]["add"];
	}else{
		// header('Location: err_report.php');
		// exit();
	}
	unset($_SESSION["post"]);
	$modify=htmlspecialchars($modify,ENT_QUOTES,'UTF-8');
	$staff=htmlspecialchars($staff,ENT_QUOTES,'UTF-8');
	$username=htmlspecialchars($username,ENT_QUOTES,'UTF-8');
	$username2=htmlspecialchars($username2,ENT_QUOTES,'UTF-8');
	$username3=htmlspecialchars($username3,ENT_QUOTES,'UTF-8');
	$username4=htmlspecialchars($username4,ENT_QUOTES,'UTF-8');
	$email=htmlspecialchars($email,ENT_QUOTES,'UTF-8');
	$nyuusha=htmlspecialchars($nyuusha,ENT_QUOTES,'UTF-8');
	$taisha=htmlspecialchars($taisha,ENT_QUOTES,'UTF-8');
	$yuukyuu=htmlspecialchars($yuukyuu,ENT_QUOTES,'UTF-8');
	$admin_flag=htmlspecialchars($admin_flag,ENT_QUOTES,'UTF-8');
	$new_work_id=htmlspecialchars($new_work_id,ENT_QUOTES,'UTF-8');
	$new_start_month=htmlspecialchars($new_start_month,ENT_QUOTES,'UTF-8');
	$new_end_month=htmlspecialchars($new_end_month,ENT_QUOTES,'UTF-8');
	$passwords=htmlspecialchars($passwords,ENT_QUOTES,'UTF-8');
	$s_year_and_month = $s_year_and_month = date("Y-").$_SESSION['month'].date("-01");

	try
	{
		$dbh = db_connect();
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		if($old_work_id!=""){
			$sql='INSERT INTO TBL_STAFF(staff_number,familyname,familyname_kana,firstname,firstname_kana,email,hire_date,retirement_date,admin_flag,holiday_with_pay,new_work_id,new_start_month,new_end_month,old_work_id,old_start_month,old_end_month,last_modified,password )VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
			$stmt = $dbh->prepare($sql);
			$data[] = $staff;
			$data[] = $username;
			$data[] = $username3;
			$data[] = $username2;
			$data[] = $username4;
			$data[] = $email;
			$data[] = $nyuusha;
			$data[] = $taisha;
			$data[] = $admin_flag;
			$data[] = $yuukyuu;
			$data[] = $new_work_id;
			$data[] = $new_start_month;
			$data[] = $new_end_month;
			$data[] = $old_work_id;
			$data[] = $old_start_month;
			$data[] = $old_end_month;
			$data[] = $modify;
			$data[] = $passwords;
		}else{
			$sql='INSERT INTO TBL_STAFF(staff_number,familyname,familyname_kana,firstname,firstname_kana,email,hire_date,retirement_date,admin_flag,holiday_with_pay,new_work_id,new_start_month,new_end_month,last_modified,password)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
			$stmt = $dbh->prepare($sql);
			$data[] = $staff;
			$data[] = $username;
			$data[] = $username3;
			$data[] = $username2;
			$data[] = $username4;
			$data[] = $email;
			$data[] = $nyuusha;
			$data[] = $taisha;
			$data[] = $admin_flag;
			$data[] = $yuukyuu;
			$data[] = $new_work_id;
			$data[] = $new_start_month;
			$data[] = $new_end_month;
			$data[] = $modify;
			$data[] = $passwords;
		}
		$stmt->execute($data);

		$sql = "SELECT * FROM TBL_STAFF WHERE staff_number = :staff_number";
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(":staff_number",$staff,PDO::PARAM_STR);
		$stmt->execute();
		$rec = $stmt->fetch(PDO::FETCH_ASSOC);
		if(isset($rec)){
			//サマリーテーブルに新規社員の当月データを追加する
			$sql="INSERT INTO `TBL_SUMMARY`(`staff_number`, `year_and_month`, `remaining_paid_days`, `status`,`print_log`,`send_date`,`create_date`, `update_date`) VALUES (:staff_number, :year_and_month, :remaining_paid_days, :status,:print_log,:send_date, now(), now())";
			$stmt=$dbh->prepare($sql);
			$params =array('staff_number' => $staff,'year_and_month' => $s_year_and_month,'remaining_paid_days' => $yuukyuu, 'status' => '0','print_log' => '0','send_date' => $s_year_and_month);
			$stmt->execute($params);
		}
		$dbh = null;
	} catch (Exception $e)
	{
		var_dump($e);
		// header('Location: err_report.php');
		exit();
	}
	?>
	<form method="post" action="unset_session.php">
		<div class="entry">
			<h2>新規社員を追加しました。</h2>
			<input type="submit" name="continue" value="更に追加する">
			<input type="submit" name="back" value="戻る">
		</div>
	</form>

</body>
</html>
<style>

.entry{
	width: 100%;
	margin-top: 50px;
	display: block;
	text-align: center;
}
input{
	margin: 30px;
	background : #87cefa;
	width: 150px;
	height: 40px;
}
</style>
