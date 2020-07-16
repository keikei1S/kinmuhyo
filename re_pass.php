<?php
session_start();
?>

<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/css/re_pass.css">
<title>パスワード再登録画面</title>
</head>
<body>
<div class="img">
<img src="/img/image_2020_4_10.png" height="100" width="100" alt="ロゴ" align="right" >
</div>
<div class="repass">
<h1 class="repass_title">パスワード再登録</h1>
<br/>
<br/>
  <form class="login_form" action="re_pass_check.php" method="post">
  <input type="hidden" name="token" value="<?=$token?>">
  	<p class="login_name">社員番号<input type="text" name="staff_number" maxlength="4" value="" style="width:200px;"><br/></p>
		<?php if(isset($_SESSION['errMsg1'])){?>
			<div class="errMsg">
				<p><?php print $_SESSION['errMsg1'];?></p>
			</div>
		<?php }?>

    <p class="repass_name">メールアドレス<input type="text" name="email" style="width:300px"></p>
    <?php if(isset($_SESSION['errMsg'])){?>
			<div class="errMsg">
				<p><?php print $_SESSION['errMsg'];?></p>
			</div>
		<?php }?>
	
    <button class="mail_btn" type="submit">メール送信</button>
  </br>
 </form>
 </br>
 <div class="back">
 <button class="back_btn" type=“button” onclick="location.href='staff_login.php'" align="left">戻る</button>
 </div>
 <div class="notice">
 	<p>メールが届かない場合は、以下を確認してください。</br>
	・迷惑メールアドレスに振り分けられている可能性があります。</br>
	・メールアドレスに誤りがある可能性があります。再度正しいメールアドレスを入力してください。
	</p>
 </div>
</div>
</body>
</html>
</body>
</html>
<?php
session_destroy();
?>