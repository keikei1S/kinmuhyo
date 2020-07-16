<?
session_start();
	$login=$_SESSION['login']=1;
	$result=$_SESSION['result'];
	$staff_number=$result['staff_number'];
	$staff_name=$result['familyname'].$result['firstname'];
	$admin_flag=$result['admin_flag'];

?>

<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>パスワード変更完了画面</title>
</head>
<body>

パスワードの変更が完了しました。<br/>
<br/>
<form method="post" action="switch.php">
<input type="hidden" name="result" value=<?print $login?>>
<input type="hidden" name="staff_number" value=<?print $staff_number?>>
<input type="hidden" name="staff_name" value=<?print $staff_name?>>
<input type="hidden" name="admin_flag" value=<?print $admin_flag?>>
<input type="submit" value="ユーザー切り替え画面へ">
</form>
</body>
</html>
