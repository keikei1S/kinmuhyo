<?php
if(!isset($_SESSION)){
	session_start();
}
// ログイン状態のチェック
if (isset($_SESSION["login"])==false)
{
	if(isset($_SESSION["result"]["staff_number"])){
		require'kinmu_common.php';
		$staff_result= kinmu_common::staff_table($_SESSION["result"]["staff_number"]);
	}else{
		header("Location: staff_login.php");
		exit();
	}
}
if(!isset($staff_result)){
	$login = $_SESSION["login"];
	$result = $_SESSION["result"];
}else{
	$login = 1;
	$result = $staff_result;
}
$_SESSION = array();
$_SESSION["month"] = date('m');
$_SESSION["login"] = $login;
$_SESSION["result"]= $result;
header('Expires: -1');
header('Cache-Control:');
header('Pragma:');
session_regenerate_id(true);

//変数に格納
$result=$_SESSION['result'];
$staff_number=$result['staff_number'];
$staff_name=$result['familyname'].$result['firstname'];
$admin_flag=$result['admin_flag'];

print '<span style="font-weight:bold;">'.'No.'.$staff_number.'</span>';
print '<span style="font-weight:bold;">'.$staff_name.'</span>';
print '<br/>';
?>

<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<style>
	.img{
		height: 30px;
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

<title>メニュー一覧画面</title>
</head>
<body>
	<form method="post">

		<div class="img">
			<img src="/img/image_2020_4_10.png" height="60" width="150" alt="ロゴ" align="right" >
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
				<?php }?>
				<br>
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
