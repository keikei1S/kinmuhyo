<script language="JavaScript">
//URLが手打ちされた場合に画面をログイン画面に返す
var refinfo = document.referrer;
if (!refinfo) {
    window.location.href = 'https://www.pros-service.co.jp/kinmu/staff_login.php';
}
</script>
<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/css/done.css">
<title>メール送信完了画面</title>
</head>
<body>
<div class="img_class">
	<img class="img" src="/img/image_2020_4_10.png" alt="ロゴ">
</div>
<br/>
<div class="wrapper">
	<h1 style="text-align: center; font-size: 24px;">メール送信が完了しました。</h1>
<!--ログイン画面へ!-->
	<form method="post" action="staff_login.php">
		<button class="done" type="submit">ログイン画面へ</button>
	</form>
</div>
</body>
</html>
