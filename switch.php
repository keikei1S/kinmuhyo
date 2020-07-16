<?php
session_start();
header('Expires: -1');
header('Cache-Control:');
header('Pragma:');
session_regenerate_id(true);
// ログイン状態のチェック
if (isset($_SESSION["login"])==false) 
{
	header("Location: staff_login.php");
	exit();
}
else
{
	//変数に格納
	$result=$_SESSION['result'];
	$staff_number=$result['staff_number'];
	$staff_name=$result['familyname'].$result['firstname'];
	$admin_flag=$result['admin_flag'];


	print 'No.'.$staff_number;
	print $staff_name;
	print '<br/>';
} 
?>

<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>	
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<div class="img">
<img src="/img/image_2020_4_10.png" height="100" width="100" alt="ロゴ" align="right" >
</div>
<title>ユーザー切替え画面</title>
</head>
<body>	
<br/>
<div class="wrapper">
<h1>ユーザー切替え</h1><br/>
<br/>

<button class="kinmuhyo" type=“button” onclick="location.href='kinmuhyo.php'">プロズ社員用</button>

<?php if($admin_flag==1){?>
	<button class="kanrisya" type=“button” onclick="location.href='staff_list.php'">管理者用</button>
<?php }?>
<button class="back_btn" type=“button” onclick="location.href='staff_login.php'" align="right">戻る</button>
</div>
<a href="pass_change.php" >パスワード変更</a>

</body>
</html>

