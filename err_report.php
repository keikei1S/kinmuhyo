<?php
if(!isset($_SESSION)){
	session_start();
session_regenerate_id(true);
}
//セッション情報の初期化
$_SESSION = array();
?>
<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>エラー画面</title>
</head>
<body>
	<img src="https://www.pros-service.co.jp/img/image_2020_4_10.png"
	alt="画像のサンプル" width="100px" height="100px" align="right">
</br>
</br>
</br>
</br>
	<h3>システムエラーが発生しました。</h3>
	<div class="notice">
		<p>システムエラーが発生しました。</br>
			申し訳ありませんがもう一度操作をやり直してください。</p>
		<a href="staff_login.php"><p class="link">ログイン画面へ</p></a>
	</div>
</body>
</html>
<style>
h3{
	color: red;
	text-align: center;
}
.notice{
	text-align: center;
	margin: 2em auto;
	padding: 1em;
	width: 51%;
	border: 3px dashed red; /*太さ・線種・色*/
	background-color: #FFF; /* 背景色 */
	border-radius: 1px; /*角の丸み*/
}
.link{
	color: blue;
	text-align: left;
}
</style>
