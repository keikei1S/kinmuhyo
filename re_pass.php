<?php
if(!isset($_SESSION)){
	session_start();
}
if(isset($_SESSION["err"])){
	$err = $_SESSION["err"];
}
?>
<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/css/re_pass.css">
<title>パスワード再登録画面</title>
</head>
<body>
<div class="img_class">
	<img class="img" src="/img/image_2020_4_10.png" alt="ロゴ">
</div>
<div class="wrapper">
	<h1>パスワード再登録</h1>
 	<form class="login_form" action="re_pass_check.php" method="post">
	  	<ul>
	   		<li>
	      		<label><span>社員番号</span>
	     			 <input type="text" name="staff_number" class="txtfiled" maxlength="4" value="<?php if(isset($_SESSION['staff_number'])){print $_SESSION['staff_number'];}?>">
	      		</label>
	    	</li>
	    	<?php if(isset($err['staff'])){?>
					<div class="errMsg">
						<p><?php print $err['staff'];?></p>
					</div>
				<?php }?>
	   		<li>
	      		<label><span>メールアドレス</span>
	      			<input type="email" name="email" class="txtfiled" value="<?php if(isset($_SESSION['email'])){print $_SESSION['email'];}?>">
	      		</label>
	    	</li>
	    	<?php if(isset($err['email'])){?>
			<div class="errMsg">
				<p><?php print $err['email'];?></p>
			</div>
			<?php }?>
	    </ul>
		</br>
		</br>
	    <button class="mail_btn" type="submit">メール送信</button>
    </form>



 	<div class="back">
 		<button class="back_btn" type=“button” onclick="location.href='staff_login.php'" align="left">戻る</button>
 	</div>
 	<br/>
 	<div class="notice">
 		<p>メールが届かない場合は、以下を確認してください。</br>
		・迷惑メールアドレスに振り分けられている可能性があります。</br>
		・メールアドレスに誤りがある可能性があります。再度正しいメールアドレスを入力してください。
		</p>
 	</div>
</div>
</body>
</html>
<style>
.errMsg{
		font-size: 10px;
    border: dashed 0.1px #ff0000;
    color:#ff0000;
		margin-top: -20px;
		margin-left: 607px;
    width:250px;
}
</style>
