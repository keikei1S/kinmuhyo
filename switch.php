<?php
session_start();
$_SESSION["month"] = date('m');

ob_start();
include("kinmu_common.php");
ob_clean();

unset($_SESSION['route']);
unset($_SESSION['price']);
unset($_SESSION['departure']);
unset($_SESSION['Arrival']);
unset($_SESSION["print_err"]);
unset($_SESSION["thuki"]);

unset($_SESSION["errMsg"]);
unset($_SESSION['errMsg1']);
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
unset($_SESSION['houmon']);

header('Expires: -1');
header('Cache-Control:');
header('Pragma:');
session_regenerate_id(true);
// ログイン状態のチェック
if (isset($_SESSION["login"])==false)
{
	if(isset($_POST["login"])==false){
		header("Location: staff_login.php");
		exit();
	}
}
	//セッション情報の有無判定
	//自分の社員情報を上書かない場合
	if(isset($_SESSION['rec'])){
		$staff_number = $_SESSION['rec'];
		//自分の社員情報を上書いた場合
	}elseif(isset($_SESSION["result"])){
		$staff_number = $_SESSION['result']["staff_number"];
		//セッションに社員番号が無い時
	}else{
		header("Location: staff_login.php");
		exit();
	}
		//社員テーブルを読み込む
		$tbl_staff= kinmu_common::staff_table($staff_number);
		//変数に直す
		$staff_number = $tbl_staff["staff_number"];
		$family_name = $tbl_staff["familyname"];
		$first_name = $tbl_staff["firstname"];
		$admin_flag = $tbl_staff["admin_flag"];
		//セッションに入れる
		$_SESSION["result"] = $tbl_staff;
?>
<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>メニュー一覧画面</title>
</head>
<body>
<?php

if(mb_strlen($staff_number) === 4){
	print '<span style="font-weight:bold;">'.'No.'.$staff_number.'</span>';
}elseif(mb_strlen($staff_number) >= 5){
	print '<span style="font-weight:bold;">'.'No.'.substr($staff_number, -4).'</span>';
}else{
	print '<span style="font-weight:bold;">'.'No.'.$staff_number.'</span>';
}
//文字の最大値
$limit = 20;
//姓
	if(mb_strlen($family_name) < $limit) {
	print '<span style="font-weight:bold;">'.$family_name.'</span>';
	}else{
	print '<span style="font-weight:bold;">'.mb_substr($family_name,0,20).'</span>';
	}
//名
if(mb_strlen($first_name) < $limit) {
	print '<span style="font-weight:bold;">'.$first_name.'</span>';
	}else{
	print '<span style="font-weight:bold;">'.mb_substr($first_name,0,20).'</span>';
	}
	print '<br/>';
	?>

<form method="post">
<div class="img">
	<img class="img"src="/img/imgs_logo.PNG" alt="ロゴ" width="150" height="60">
</div>
<div class="user">

	<h2 class="title">メニュー一覧</h2>
<div align="center">
<input  type="submit" class="btn1" formaction="kinmuhyo.php" name="kinmuhyou" style="background-color: #87cefa; width:230px;padding:10px;"value="勤務表">
<br/>
<br>
<input  type="submit" formaction="seisansho.php" name="koutuuhi" style="background-color: #87cefa; width:230px;padding:10px;"value="交通費精算">
<br>
<br>
<input  type="submit" formaction="pass_price.php" name="teikiken" style="background-color: #87cefa; width:230px;padding:10px;"value="定期代申請">
<br>
<br>
<input  type="submit" formaction="confirm_status.php" name="status" style="background-color: #87cefa; width:230px;padding:10px;"value="交通費精算Status確認">
<br>
<br>
	<?php if($admin_flag==1){?>
		<div class="menu">
			<label for="menu_bar01">管理者用</label>
		<input type="checkbox" id="menu_bar01" class="accordion" />
			<ul id="links01">
			<li><input type="submit" formaction="list_of_members.php" name="admin" style="background-color: #ffffff; width:230px;padding:10px;" value="勤務表メニュー"></li>
			<li><input type="submit" formaction="seisansho_output.php" name="worklocation" style="background-color: #ffffff; width:230px;padding:10px;" value="交通費精算メニュー"></li>
			<li><input type="submit" formaction="pass_price_output.php" name="pass_price" style="background-color: #ffffff; width:230px;padding:10px;" value="定期代申請メニュー"></li>
			</ul>
		</div>
	<?php } ?>
<br>
</div>
<br>
<div align="left">
<a  href="pass_change.php">パスワード変更</a>
</div>

<div align="right">
<input  type="submit" formaction="staff_login.php" name="admin" style="background-color: #87cefa; width:115px;padding:6px;"value="戻る">
</div>
</form>
</body>
</html>
<style>
.img{
	position: absolute;
	top: 0%;
	right: 0%;
}
.user{
  margin:10px auto;
  width:600px;
}
.title{
	text-align: center;
	font-size: 24px;
	font-weight: bold;
}
a {
	color: red;
}
.menu {
		max-width: 230px;
}

label {
		display: block;
		margin: 0 0 0 0;
		padding : 5px;
		text-align: center;
		line-height: 2;
		color :#000;
		background : #87cefa;
		cursor :pointer;
		border: 2px solid #696969;
}

.accordion {
		display: none;
}

.menu ul {
		margin: 0;
		padding: 0;
		background :#f4f4f4;
		list-style: none;
}

.menu li {
		height: 0;
		text-align: center;
		overflow: hidden;
		-webkit-transition: all 0.5s;
		-moz-transition: all 0.5s;
		-ms-transition: all 0.5s;
		-o-transition: all 0.5s;
		transition: all 0.5s;
}

#menu_bar01:checked ~ #links01 li{
		padding-top: 20px;
		height: 50px;
		opacity: 1;
}
</style>
