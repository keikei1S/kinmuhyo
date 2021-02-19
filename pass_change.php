<?php
session_start();
if(isset($_SESSION["rec"])){
	$staff_number = $_SESSION["rec"];
}elseif(isset($_SESSION["result"]["staff_number"])){
	$staff_number = $_SESSION["result"]["staff_number"];
}
if(isset($_SERVER['HTTP_REFERER'])){
	$motourl = $_SERVER['HTTP_REFERER'];
}else{
		if(isset($_GET['p'])) {
			$staff_number = openssl_decrypt($_GET['p'], 'AES-256-CBC', '社員番号');
			if($staff_number==false){
				$staff_number = openssl_decrypt($_GET['p'], 'AES-128-CBC', '社員番号');
			}
		}
		if(isset($_GET['t'])) {
			$token = $_GET['t'];
			$start = mb_strpos($token,'"');
			$end = mb_strpos($token,'_');
			$mojiretu = mb_substr($token, $start, $end-$start);
		}
		if(!isset($mojiretu)){
			print "URLの期限が切れています。最初からやり直してください。";
			exit();
		}elseif((date('Y-m-d H:i:s',$mojiretu)) < date("Y-m-d H:i:s")){
				print "URLの期限が切れています。最初からやり直してください。";
			exit();
		}
		$_SESSION["firtst_login"] = "1";
}
if(isset($_SESSION['err'])){
	$err = 	$_SESSION['err'];
}

?>
<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="/css/re_pass.css">
<title>パスワード変更画面</title>
</head>
<body>
<div class="img_class">
	<img class="img" src="/img/image_2020_4_10.png" alt="ロゴ">
</div>
<div class="wrapper">
	<h1>パスワード変更画面<br/></h1>
	<br/>
	<form class="login_form" method="post" action="pass_change_check.php">
		<ul>
	   		<li>
	      		<label><span>社員番号</span></label>
	      		<input type="text" class="txtfiled" value="<? print $staff_number?>" readonly>
	      		<input type="hidden" name="number" value="<?php echo $staff_number; ?>">
	      	</li>
			  <?php if(isset($err['staff_number'])){?>
					<div class="errMsg">
						<p><?php print $err['staff_number'];?></p>
					</div>
				<?php }?>
	   		<li>
				</br>
	      		<label>新パスワード</label>
	     			 <input type="password" name="pass" class="txtfiled" value="<?php if(isset($_SESSION['pass'])){print $_SESSION['pass'];}?>">
						<span class="field-icon">
	            <i toggle="#password-field" class="zmdi zmdi-eye toggle-password"></i>
	          </span>
	      		<p class="login_submenu">英数混合8文字以上<br/></p>
	    	</li>
	    	<?php if(isset($err['pass1'])){?>
					<div class="errMsg">
						<p><?php print $err['pass1'];?></p>
					</div>
				<?php }?>
	   		<li>
	      		<label>パスワード(再)</label>
	      			<input type="password" name="pass2" class="txtfiled" value="<?php if(isset($_SESSION['pass2'])){print $_SESSION['pass2'];}?>">
						<span class="field-icon">
	            <i toggle="#password-field" class="zmdi zmdi-eye toggle-password2"></i>
	          </span>
	      		<p class="login_submenu">英数混合8文字以上<br/></p>
	    	</li>
				<?php if(isset($err['pass2'])){?>
					<div class="errMsg">
						<p><?php print $err['pass2'];?></p>
					</div>
				<?php }?>
	    </ul>
	    <br/>
		<button class="btn" type="submit" a href="pass_change_check.php">変更</a></button>
	</form>
	<?if(!isset($_SESSION["firtst_login"])){?>
		<button type=“button” class="btn_back" onclick="location.href='switch.php'">キャンセル</button>
	<?}?>
	</div>
</body>
</head>
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
$(".toggle-password2").click(function() {
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
<style>
.errMsg{
		font-size: 11px;
    border: dashed 0.1px #ff0000;
    color:#ff0000;
		margin-top: -35px;
    width:270px;
		position: absolute;
		left:580px
}
label{
	position: absolute;
	left:450px;
}
input{
	position: absolute;
	left:580px;
}
.field-icon {
 color: #555;
 margin-right: 15px;
 margin-left: 335px;
 position: relative;
 z-index: 2;
}
</style>
