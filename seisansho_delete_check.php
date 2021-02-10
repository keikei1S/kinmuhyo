<?php
//セッションスタート
session_start();

if(isset($_POST['sentaku'])){
$_SESSION['sentaku'] = $_POST['sentaku'];
}

// エラー表示を停止
error_reporting(8192);
// ログイン状態のチェック
if (isset($_SESSION["login"])==false) 
{
	header("Location: staff_login.php");
	exit();
}
//ログイン情報を変数に代入
$result = $_SESSION['result'];
$staff_number=$result['staff_number'];
$familyname = $result['familyname'];
$firstname = $result['firstname'];
$No = 'No.';
print "<strong>" .$No."</strong>";
print "<strong>".$staff_number."</strong>";
print "<strong>".$familyname."</strong>";
print "<strong>".$firstname."</strong>";

//POSTで受け取ったデータを変数に代入
$_SESSION['No'] = $_POST['No'];

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>削除確認</title>
</head>
<body>
<form method="post">
<br><br><br><br><br><br>
<div align="center">
<h1>本当に削除してもよろしいですか?</h1>
<input type="submit"style="background-color: #87cefa; width: 200px; padding: 8px;"value="はい" formaction="seisansho_delete.php">
<input type="submit"style="background-color: #87cefa; width: 200px; padding: 8px;"value="キャンセル" formaction="seisansho.php">
</form>
</div>
</body>
</html>
