<?php
session_start();

// セッションの変数のクリア
$_SESSION = array();

// セッションクリア
@session_destroy();
?>

<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/css/rogout.css">
<title>ログアウト画面</title>
</head>
<body>
	<div class="img_class">
		<img class="img" src="/img/image_2020_4_10.png" alt="ロゴ">
	</div>
	<div class="wrapper">
        <h2>ログアウトしました。</h2>
        <button class="back_btn" type=“button” onclick="location.href='staff_login.php'">ログイン画面に戻る</button>
    </div>
</body>
</html>