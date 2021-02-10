<?php
session_start();

//ログイン情報を変数に代入
$result = $_SESSION['result'];
$staff_number=$result['staff_number'];
$familyname = $result['familyname'];
$firstname = $result['firstname'];
$No = 'No.';
//月の情報を変数に代入
$month = $_SESSION["select1"];

ob_start();
include("kinmu_common.php");
ob_clean();

try
{
	$dbh = db_connect();
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
    if(isset($month)){
		$s_year_and_month = $month.date("-01");
		$now_month = date('t', strtotime($s_year_and_month));
		$e_year_and_month = $month.date("-".$now_month);
	}

$dbh = null;
}catch (Exception $e){
    // var_dump($e);
        print 'システムエラーが発生しました';
        exit();
    }
// ログイン状態のチェック
if (isset($_SESSION["login"])==false) 
{
	header("Location: staff_login.php");
	exit();
}
// エラー表示を停止
error_reporting(8192);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
span.sample3a {
	position: absolute;
	top: 0px;
	left: 1240px
}
</style>
<?php
print "<strong>" .$No."</strong>";
print "<strong>".$staff_number."</strong>";
print "<strong>".$familyname."</strong>";
print "<strong>".$firstname."</strong>";
?>

<title>交通費精算書を保存しました</title>
</head>
<body>

<span class="sample3a">
	<img class="img" src="/img/image_2020_4_10.png" alt="ロゴ"width="100" height="100">
</span>

<form method="post">
<br><br><br><br><br><br>
<div align="center">
<h1>交通費精算書を保存しました。</h1>
<br><br>
<input type="submit"style="background-color: #87cefa; width: 250px; padding: 20px;"value="もどる" formaction="seisansho.php">
&nbsp;&nbsp;&nbsp;
<!-- <input type="submit"style="background-color: #87cefa; width: 250px; padding: 20px;"value="管理者へ提出" formaction="admin_submission.php">
</form> -->


<?php
//変数を初期化
unset ($_SESSION['errmsg1']);
unset ($_SESSION['errmsg2']);
unset ($_SESSION['errmsg3']);
unset ($_SESSION['errmsg4']);
unset ($_SESSION['errmsg5']);
unset ($_SESSION['errmsg6']);
unset($_SESSION['staffcode']);
unset ($_SESSION['date']);
unset ($_SESSION['err']);
unset ($_SESSION['delete']);
unset ($_SESSION['save']);

 ?>
</div>
</body>
</html>