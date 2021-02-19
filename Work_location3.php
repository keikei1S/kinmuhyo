<?php
session_start();
unset($_SESSION["up_err"]); 
unset($_SESSION['kinmuchi']); 
unset($_SESSION['kinmuchiid']); 
unset($_SESSION['strat']); 
unset($_SESSION['end']); 
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
</head>
<body>
	<form method="post">
	<div align="center">
		勤務地情報を追加しました。 <br><br><br> <input type="submit"
			formaction="Work_location.php" name="insert" value="戻る">
	</div>
	</form>
</body>
</html>