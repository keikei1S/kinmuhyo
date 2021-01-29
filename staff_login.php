<?php
if(isset($_COOKIE['st_num'])){
	$staff_number = $_COOKIE["st_num"];
}
if(isset($_COOKIE['pass'])){
	$password = $_COOKIE["pass"];
}
if(!isset($_SESSION)){
	session_start();
}
//ログイン画面に遷移した段階でセッションを初期化する
if(isset($_SESSION['login'])){
	unset($_SESSION['login']);
}
if(isset($_SESSION['result'])){
	unset($_SESSION['result']);
}
if($_SERVER["REQUEST_METHOD"] !== "POST"){
	if(isset($_SESSION["err"])){
		$err = $_SESSION["err"];
		unset($_SESSION["err"]);
	}
	if(isset($_SESSION["st_num"])){
		$staff_number = $_SESSION["st_num"];
	}
	if(isset($_SESSION["password"])){
		$password = $_SESSION["password"];
	}
}
?>
<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<title>ログイン画面</title>
</head>
<body>
<div class="img">
<img src="/img/image_2020_4_10.png" height="60" width="150" alt="ロゴ" align="right" >
</div>
	<h1 class="login_title">ログイン画面<br/></h1>
	<br/>

		<div class="pw-form">
				<form method="post" action="login_check.php" class="pw-form-container">
        <p>社員番号
					<input type="text" name="staff_number" maxlength="4" value="<?php print $staff_number;?>" style="width:200px;">
				</p>
			</br>
			<?if(isset($err["staff_number"])){
				print "<div class='errMsg'>";
					print "<p>$err[staff_number]</p>";
				print "</div>";
			}?>
			</br>
        <p>パスワード
					<input type="password" name="pass" value="<?php print $password;?>" style="width:200px;">
          <span class="field-icon">
            <i toggle="#password-field" class="zmdi zmdi-eye toggle-password"></i>
          </span>
        </p>
				<?if(isset($err["pass"])){
					print "<div class='errMsg1'>";
						print "<p>$err[pass]</p>";
					print "</div>";
				}?>
				<p class="login_submenu">英数混合8文字以上<br/></p>
			</br>
			</br>
			</br>
			</br>
			<button class="btn" type="submit">ログイン</button>
    </form>
</div>
</br>
</br>
</br>
<a class="re_pass" href="re_pass.php">パスワードを忘れた場合</a>
</body>
</html>
<script type="text/javascript">
// パスワードの表示・非表示切替
$(".toggle-password").click(function() {
  // iconの切り替え
  $(this).toggleClass("zmdi-eye zmdi-eye-off");

  // 入力フォームの取得
  var input = $(this).parent().prev("input");
  // type切替
  if (input.attr("type") == "password") {
    input.attr("type", "text");
  } else {
    input.attr("type", "password");
  }
});
</script>
