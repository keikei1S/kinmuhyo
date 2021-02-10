<?php
//セッションスタート
session_start();

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
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>削除完了</title>
</head>
<body>
<form method="post">
<br><br><br><br><br><br>
<div align="center">
<h1>レコードを<?php print count($_SESSION['sentaku'])?>件削除しました</h1>

<input type="submit"style="background-color: #87cefa; width: 200px; padding: 8px;"value="戻る" formaction="seisansho.php">
<?php
unset ($_SESSION['errmsg1']);
unset ($_SESSION['errmsg2']);
unset ($_SESSION['errmsg3']);
unset ($_SESSION['errmsg4']);
unset ($_SESSION['errmsg5']);
unset ($_SESSION['errmsg6']);
unset($_SESSION['staffcode']);
unset ($_SESSION['date']);
unset ($_SESSION['err']);
unset ($_SESSION['delete']);
?>
</form>
</body>
</html>