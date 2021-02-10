<?php
session_start();
// ログイン状態のチェック
if (isset($_SESSION["login"])==false) 
{
	header("Location: staff_login.php");
	exit();
}

//ログイン情報を変数に代入
$No = 'No.';
$result = $_SESSION['result'];
$staff_number=$result['staff_number'];
$familyname = $result['familyname'];
$firstname = $result['firstname'];
print "<strong>" .$No."</strong>";
print "<strong>".$staff_number."</strong>";
print "<strong>".$familyname."</strong>";
print "<strong>".$firstname."</strong>";
    try {
		// //////////////////データベースの読込 S//////////////////////
	
	$dsn='mysql:dbname=pros-service_kinmu;host=mysql731.db.sakura.ne.jp;charset=utf8';
    $user='pros-service';
    $password='cl6cNJs2lt5W';
		
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		//tbl_checkout_statusの値を全て取得
		//ローカル用
		// $sql = 'select * FROM  tbl_checkout_status order by year_and_month desc';
		//サーバー用
		$sql = 'select * FROM  TBL_CHECKOUT_STATUS order by year_and_month desc';
		$summarystmt = $dbh->prepare($sql);
		$summarystmt->execute();

		$dbh = null;
		// //////////////////データベースの読込 E//////////////////////
	} catch (PDOException $e) {

        print 'ただいま障害が発生しております';
        exit();
	}
	
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
span.sample1 {
	position: absolute;
	top: 320px;
	left: 800px
}
span.sample3a {
	position: absolute;
	top: 0px;
	left: 1240px
}
</style>
<title>交通費精算Status確認画面</title>
</head>
<body>
<span class="sample3a">
	<img class="img" src="/img/image_2020_4_10.png" alt="ロゴ"width="100" height="100">
</span>
<div align="center">
<?php
print '<h2>交通費精算Status確認</h2>';
?>
<table border=1　cellspacing="1" cellpadding="5">
	<tbody>
		<tr>
			<th>対象年月</th>
			<th>ステータス</th>
		</tr>
		<?php

	?>	
<?php
 for ($i = 0; $i <=100 ; $i++) {	
	$rec = $summarystmt->fetch(PDO::FETCH_ASSOC);

$y = date("Y-m",strtotime("-6 month"));

if($y < substr($rec['year_and_month'], 0,7) || $rec['checkout_visit'] == 1 || $rec['checkout_visit'] == 2){
if($staff_number == $rec['staff_number']){

print'<tr>';
		print '<td>'.substr($rec['year_and_month'], 0,4);
		print '/';
		print substr($rec['year_and_month'], 5,2);
'</td>';
		print '<td>';
		if($rec['checkout_visit'] == 1 ){
		print '途中完了';
			}elseif($rec['checkout_visit'] == 2 ){
				print '手続き中';
			}elseif($rec['checkout_visit'] == 3 ){
				print '手続き完了';
			}
		print '</td>';

		}
 	}
}
 print '</tr>';
 
?>


<span class="sample1">
<button class="back_btn" type=“button” style="background-color: #87cefa; width:115px;padding:6px;" onclick="location.href='switch.php'">戻る</button>
</span>

	</tbody>
</table>
</div>		
</body>
</html>