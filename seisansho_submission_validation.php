<?php
//セッションを開始する
session_start();
//ログイン情報を変数に代入
$result = $_SESSION['result'];
$staff_number=$result['staff_number'];
$familyname = $result['familyname'];
$firstname = $result['firstname'];
$No = 'No.';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
span.img {
	position: absolute;
	top: 0%;
	right: 0%;
}
</style>
<title>交通費精算確認画面</title>
</head>
<body>
<?php

ob_start();
include("seisansho.php");
ob_clean();
try {
//文字の最大値
$limit = 20;
	print '<span style="font-weight:bold;">'.'No.'.$staff_number.'</span>';
//姓
	if(mb_strlen($result['familyname']) < $limit) {
	print '<span style="font-weight:bold;">'.$result['familyname'].'</span>';
	}else{
	print '<span style="font-weight:bold;">'.mb_substr($result['familyname'],0,20).'</span>';
	}
//名
if(mb_strlen($result['firstname']) < $limit) {
	print '<span style="font-weight:bold;">'.$result['firstname'].'</span>';
	}else{
	print '<span style="font-weight:bold;">'.mb_substr($result['firstname'],0,20).'</span>';
	}
	print '<br/>';
?>
<!-- 会社のロゴ -->
<span class="img">
<img class="img"src="/img/imgs_logo.PNG" alt="ロゴ" width="150" height="60">
</span>

<div align="center">
<!-- 画面タイトル -->
<br><br><br>
<h1>最終日まで入力しましたか？</h1>
<br><br><br>
<form method="post">
<!-- はいボタン -->
<input type="submit"style="background-color: #87cefa; width: 250px; padding: 15px;font-size:20px;" formaction="admin_submission.php" value="はい">
<!-- 修正するボタン -->
<input type="submit"style="background-color: #87cefa; width: 250px; padding: 15px;font-size:20px;" formaction="seisansho.php" value="修正する">

</div>
</form>
<?php 
$dbh = null;
} catch (Exception $e) {
  print 'システムエラーが発生しました';
  exit();
}
?>
</body>
</html>