<script language="JavaScript">
//URLが下手が記された場合に画面をログイン画面に返す
var refinfo=document.referrer;
if (!refinfo){
　window.location.href = 'https://www.pros-service.co.jp/kinmu/staff_login.php';
}
</script>
<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/css/rogout.css">
<title>パスワード変更完了画面</title>
</head>
<body>
  <div class="img_class">
  	<img class="img" src="/img/image_2020_4_10.png" alt="ロゴ">
  </div>
<div class="wrapper">
<h2>パスワードの変更が完了しました。</h2><br/>
<br/>
<form method="post" action="switch.php">
<?setcookie("st_num", "", time() - 30);
setcookie("pass", "", time() - 30)?>
<input type="submit" value="メニュー一覧へ" class="back_btn">
</form>
</div>
</body>
</html>
