<?php
session_start();
$staff_number=isset($_SESSION['staff_number']) ? $_SESSION['staff_number'] : '';
$pass=isset($_SESSION['pass']) ? $_SESSION['pass'] : '';
$motourl = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : NULL;
?>
<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/css/style.css">
<title>パスワード変更画面</title>
</head>
<body>
<div class="img">
<img src="/img/image_2020_4_10.png" height="100" width="100" alt="ロゴ" align="right" >
</div>
<div class="login">
	<h1 class="login_title">パスワード変更画面<br/></h1>
	<br/>
	<form class="pass_change" method="post" action="pass_chamge_check.php">
	<input type="hidden" name="motourl" value=<?print $motourl?>>
		<p class="login_name">社員番号　　
		<input type="text" name="staff_number" maxlength="4" value="" style="width:200px;"><br/></p>
		<? if(isset($_SESSION['errMsg1'])){?>
			<div class="errMsg">
				<p><?php print $_SESSION['errMsg1'];?></p>
			</div>
		<?php }?>
		<br/>

		<p class="login_name">新パスワード　
		<input type="password" name="pass" value="" style="width:200px;"><br/></p>
		<p class="login_submenu">英数混合8文字以上<br/></p>
		<?php if(isset($_SESSION['errMsg2'])){?>
			<div class="errMsg">
				<p><?php print $_SESSION['errMsg2'];?></p>
			</div>
		<?php }?>
		<p class="login_name">パスワード(再)	　
		<input type="password" name="pass2" value="" style="width:200px;"><br/></p>
		<p class="login_submenu">英数混合8文字以上<br/></p>
		<br/>
		<button class="btn" type="submit">変更</button>
	</form>
	<? if($motourl!=NULL){?>
	<button type=“button” onclick="history.back()">キャンセル</button>
	<?}else{?>
		<button type=“button” onclick="location.href='staff_login.php'">キャンセル</button>
	<?}?>
</div>

</head>
</html>
<?php session_destroy();?>