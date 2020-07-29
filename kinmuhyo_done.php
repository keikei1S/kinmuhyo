<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
print 'No.'.$staff_number.$staff_name;
?> 
<div class="img">
<img src="/img/image_2020_4_10.png" height="100" width="100" alt="ロゴ" align="right" >
</div>
<h2>最終日まで入力しましたか？</h2>

<form method="post" action="input_completed.php">
<button class="done" type="submit">はい</button>
</form>	

<form method="post" action="kinmuhyo.php">
<input type="button" onclick="history.back()" value="修正する">
</form>

</body>
</html>
