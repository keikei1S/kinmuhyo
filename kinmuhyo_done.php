<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/css/done.css">
<title>勤務表提出確認</title>
</head>
<body>
<?php
if(!isset($_SESSION)){
		session_start();
	}
	ob_start();
	include("kinmuhyo.php");
	ob_clean();
print '<span style="font-weight:bold;">'.'No.'.$staff_number.$staff_name.'</span>';
?>
<div class="img_class">
	<img class="img" src="/img/image_2020_4_10.png" alt="ロゴ">
</div>
<br/>
<div class="wrapper">
	<?if(in_array('NG', $check_result)){?>
	<h2>入力内容に誤りがあります。</br>入力内容を修正し、再度保存してください。</h2>
	<?}else{?>
		<h1>勤務表を保存しました。</h1>
		<?}?>
	<!--勤務表画面に戻る!-->
	<div class="btn_class">
		<button class="back_btn" type=“button” onclick="location.href='kinmuhyo.php'">戻る</button>
	</div>
	<!--管理者へメール送信!-->
	<?if(!in_array('NG', $check_result)){?>
		<div class="btn_class">
			<form method="post" action="input_completed.php">
				<button class="done" type="submit">管理者へ提出</button>
			</form>
		</div>
</div>
<!--ログアウトする!-->
	<form method="post" action="rogaut.php">
		<button class="rogaut" type="submit">ログアウト</button>
	</form>
	<?}?>
</body>
</html>
