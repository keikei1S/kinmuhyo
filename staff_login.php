<?php
session_start();
$staff_number=isset($_SESSION['staff_number']) ? $_SESSION['staff_number'] : '';
$pass=isset($_SESSION['pass']) ? $_SESSION['pass'] : '';
?>
<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/css/style.css">
<title>ログイン画面</title>
</head>
<body>
<div class="img">
<img src="/img/image_2020_4_10.png" height="100" width="100" alt="ロゴ" align="right" >
</div>
<div class="login">
	<h1 class="login_title">ログイン画面<br/></h1>
	<br/>
	<form class="login_form" method="post" action="login_check.php">
		<p class="login_name">社員番号　　
		<input type="text" name="staff_number" maxlength="4" value="<?php if(!empty($_COOKIE['st_num'])){print $_COOKIE['st_num'];}?>" style="width:200px;"><br/></p>
		<?php if(isset($_SESSION['errMsg1'])){?>
			<div class="errMsg">
				<p><?php print $_SESSION['errMsg1'];?></p>
			</div>
		<?php }?>
		<br/>

		<p class="login_name">パスワード　
		<input type="password" name="pass" value="<?php if(!empty($_COOKIE['st_num'])){print $_COOKIE['pass'];}?>" style="width:200px;"><br/></p>
		<p class="login_submenu">英数混合8文字以上<br/></p>
		<?php if(isset($_SESSION['errMsg2'])){?>
			<div class="errMsg">
				<p><?php print $_SESSION['errMsg2'];?></p>
			</div>
		<?php }?>
		<br/>
		<button class="btn" type="submit">ログイン</button>
	</form>
	<br/>
	<br/>
	<br/>
	<br/>
	<br/>
	<br/>
	<a class="re_pass" href="re_pass.php">パスワードを忘れた場合</a>
</div>
</body>
</html>
<?php session_destroy();?>
