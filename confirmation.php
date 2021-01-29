<?php
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
$_SESSION["staffcode"] = $_POST["staffcode"];
if($_SERVER['HTTP_REFERER']!="http://localhost:8080/kinmuhyo/list_of_members.php"){
	$_SERVER['HTTP_REFERER']="http://localhost:8080/kinmuhyo/list_of_members.php";
}
$url = $_SERVER['HTTP_REFERER']."?page_id=".$_SESSION["id"]."&".urlencode(urlencode("ステータス1"))."=".$_SESSION["status"][0]."&".urlencode(urlencode("ステータス2"))."=".$_SESSION["status"][1]."&".urlencode(urlencode("ステータス3"))."=".$_SESSION["status"][2]."&".urlencode(urlencode("ステータス4"))."=".$_SESSION["status"][3]."&".urlencode(urlencode("ステータス5"))."=".$_SESSION["status"][4];
if($_POST['staffcode']==NULL){
  $_SESSION["print_err"] = "社員を選択してください";
  header("Location:$url");
  exit;
}
require_once("kinmu_common.php");

	$attendance= kinmu_common::Attendance($_POST['staffcode']);
	$staff_remaining= kinmu_common::Kinmuhyo($_POST['staffcode']);
if($staff_remaining["status"]!=3){
  $_SESSION["print_err"] = "管理者確認が可能なステータスは印刷完了のみです";
  header("Location:$url");
  exit;
}

	if(isset($attendance)){
		foreach ($attendance as $key => $value) {
			$holiday[$key]=$value['vacation'];
		}
		$kyuuka_nissuu=0;
		$zenhan=0;
		$kouhan=0;
		foreach($holiday as $key =>$val){
			if(stristr($holiday[$key],"1") !== false){
				$kyuuka_nissuu++;
			}
			if(stristr($holiday[$key],"4") !== false){
				$zenhan++;

			}
			if(stristr($holiday[$key],"5") !== false){
				$kouhan++;
			}
		}

		if($zenhan!=0 || $kouhan!=0){
			$yuukyu_syoka = $kyuuka_nissuu+($zenhan/2)+($kouhan/2);
		}else{
			$yuukyu_syoka=$kyuuka_nissuu;
		}
    if($yuukyu_syoka!=0){
      (float)$zan = $staff_remaining["remaining_paid_days"] - $yuukyu_syoka;
    }else{
      $zan = $staff_remaining["remaining_paid_days"];
    }
	$s_year_and_month = $_SESSION["year_month"];
	try{
		$dbh = db_connect();
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "UPDATE TBL_SUMMARY SET remaining_paid_days=:remaining_paid_days, status=:status  WHERE staff_number=:staff_number AND year_and_month=:year_and_month";
		$stmt=$dbh->prepare($sql);
		$params =array('remaining_paid_days' => $zan,'status' => "4",'staff_number' =>$_POST["staffcode"],'year_and_month' => $s_year_and_month);
		$stmt->execute($params);
		$dbh=null;
	}catch (Exception $e) {
		print 'ただいま障害が発生しております';
		exit();
	}
	try{
		$dbh = db_connect();
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "UPDATE TBL_STAFF SET holiday_with_pay=:holiday_with_pay WHERE staff_number=:staff_number";
		$stmt=$dbh->prepare($sql);
		$params =array('holiday_with_pay' => $zan,'staff_number' =>$_POST["staffcode"]);
		$stmt->execute($params);
		$dbh=null;
	}catch (Exception $e) {
		print 'ただいま障害が発生しております';
		exit();
	}
  try{
    $new_date = date("Y-m-d",strtotime($_SESSION["year_month"] . "+1 month"));
		$dbh = db_connect();
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql="INSERT INTO `TBL_SUMMARY`(`staff_number`, `year_and_month`, `remaining_paid_days`, `status`,`print_log`,`send_date`,`create_date`, `update_date`) VALUES (:staff_number, :year_and_month, :remaining_paid_days, :status,:print_log,:send_date, now(), now())on duplicate key update staff_number=:staff_number, year_and_month = :year_and_month, remaining_paid_days = :remaining_paid_days, status = :status,print_log = :print_log,send_date = :send_date";
  	$stmt=$dbh->prepare($sql);
   	$params =array('staff_number' => $_POST["staffcode"],'year_and_month' => $new_date,'remaining_paid_days' => $zan, 'status' => '0','print_log' => '0','send_date' => $new_date);
  	$stmt->execute($params);
  	$dbh=null;
	}catch (Exception $e) {
		header('Location: err_report.php');
  exit();
	}
  $_SESSION["print_err"]="管理者確認が完了しました。";
  header("Location:$url");
  exit;
}
?>
