<?php
// エラー表示を停止
//error_reporting(8192);
//セッションが開始されていなければセッションを開始する。
if(!isset($_SESSION)){
	session_start();
	session_regenerate_id(true);
}
//DB接続クラスを読み込む
ob_start();
require_once('kinmu_common.php');
ob_clean();
if($_SERVER['HTTP_REFERER']!="http://localhost:8080/kinmuhyo/list_of_members.php"){
	$_SERVER['HTTP_REFERER']="http://localhost:8080/kinmuhyo/list_of_members.php";
}
$url = $_SERVER['HTTP_REFERER']."?page_id=".$_SESSION["id"]."&".urlencode(urlencode("ステータス1"))."=".$_SESSION["status"][0]."&".urlencode(urlencode("ステータス2"))."=".$_SESSION["status"][1]."&".urlencode(urlencode("ステータス3"))."=".$_SESSION["status"][2]."&".urlencode(urlencode("ステータス4"))."=".$_SESSION["status"][3]."&".urlencode(urlencode("ステータス5"))."=".$_SESSION["status"][4];
if(isset($_POST["end_print"])){
	$_SESSION["print_err"] = "社員番号".$_SESSION['staffcode']."の勤務表を印刷しました。";
	header("Location:$url");
	exit;
}
if(!isset($_POST["start_print"])){
	if($_POST['staffcode']==NULL){
		$_SESSION["print_err"] = "社員を選択してください";
		header("Location:$url");
		exit;
	}
	$_SESSION['staffcode'] = $_POST['staffcode'];
}
if($_SESSION["thuki"]==NULL){
	$now_month1=date("Y-m");
	$now_month2=date("Y-m");
	$start_month = $now_month2."-01";
	$_SESSION["month"] = date("m");
}else{
	$now_month1=$_SESSION["thuki"];
	$now_month2=$_SESSION["thuki"];
	$start_month=$now_month2."-01";
	$_SESSION["month"] = explode("-", $_SESSION["thuki"])[1];
}
//社員テーブルを読み込み、kinmuhyo.phpの元なる情報を取得
$result= kinmu_common::staff_table($_POST['staffcode']);
//社員番号を元にサマリーテーブルを持ってくる
$summary = kinmu_common::Kinmuhyo($_POST['staffcode']);

//選択した社員の勤務表ステータスが0,1の場合はエラーとして画面遷移させない
if($summary["status"]=="0" || $summary["status"]=="1"){
	$_SESSION["print_err"] = "勤務表の入力が完了していないため、勤務表を印刷することができません。";
	header("Location:$url");
	exit;
}
//印刷ボタン押下時、テーブルをアップデートする
if(isset($_POST["start_print"])){
		try{
			$dbh = db_connect();
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql= "UPDATE TBL_SUMMARY SET status=:status,print_log=:print_log WHERE staff_number = :staff_number AND year_and_month=:year_and_month";
			$stmt=$dbh->prepare($sql);
			$params = array(':status' => 3,':print_log' => 1,':staff_number' => $_SESSION['staffcode'],':year_and_month' => $start_month);
			$stmt->execute($params);
			$dbh=null;
		}
		catch(Exception $e){
			header('Location: err_report.php');
 			exit();
		}
	$_SESSION["print_err"] = "社員番号".$_SESSION['staffcode']."の勤務表を印刷しました。";
	header("Location:$url");
	exit;
}
?>
<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/css/print.css" media="print">
<title><?php print $now_month2."月分勤務表(".$result["familyname"].$result["firstname"].")"?></title>
</head>
<body>
	<?
	require_once('kinmuhyo.php');
	require_once('kinmuhyo_summary.php');
	?>
<div class="print_btn">
	<?if($summary["status"]==2){?>
		<form method="post" action="preview.php">
			<input type="submit" name="start_print" id="print" class="print_pdf" value="印刷する">
		</form>
	<?}else{?>
		<form method="post" action="preview.php">
			<input type="submit" name="end_print" id="print" class="print_pdf" value="印刷する">
		</form>
	<?}?>
	<input type="button" id="back" class="back_pdf" value="戻る">
</div>
</body>
</html>
<!--ボタン押下で印刷させるか、いきなりプレビュー画面出すかで変わる!-->
<script type="text/javascript">
document.getElementById("print").onclick = function() {
  window.print();
};
document.getElementById("back").onclick = function() {
var link = <?php echo json_encode($url); ?>;
 location.href = link;
};
</script>
<style type="text/css">
.print_pdf{
	width: 250px;
	height: 70px;
	background-color: #a0d8ef;
}
.back_pdf{
	width: 250px;
	height: 70px;
	background-color: #eb6ea5;
}
.print_btn{
	margin-top: 180px;
	text-align: center;
}
</style>
<?

?>
