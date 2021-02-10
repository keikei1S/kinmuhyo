<?php
ob_start();
include("kinmu_common.php");
ob_clean();

session_start();
// ログイン状態のチェック
if (isset($_SESSION["login"])==false) 
{
	header("Location: staff_login.php");
	exit();
}
// エラー表示を停止
error_reporting(8192);

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
span.sample1 { position:absolute; top:130px; left:960px }
span.sample2 { position:absolute; top:105px; left:915px }
span.button4 { position:absolute; top:233px; left:960px } 
span.button { position:absolute; top:270px; left:960px } 
span.button2 { position:absolute; top:307px; left:960px }
span.button3 { position:absolute; top:344px; left:960px }
span.sample99 {position: absolute;top: 150px;right: 535px}
span.sample3 {position: absolute;top: 0px;left: 1250px}
</style>
<title>交通費精算画面</title>
</head>
<body>
<span class="sample3">
	<img class="img" src="/img/image_2020_4_10.png" alt="ロゴ"width="100" height="100">
</span>
<?php
//formを指定
print'<form method="post">';
//印刷ボタン作成
print'<span class="button4">';
print '<input type="submit" name="worklocationss"
style="background-color: #87cefa; width: 120px; height: 35px"
value="印刷">';
print '</span>';
//PDFボタン作成
print'<span class="button">';
print '<input type="submit" formaction="seisansho_output_check.php"name="worklocation"
style="background-color: #87cefa; width: 120px; height: 35px"
value="PDF">';
print '</span>';
//Statusを戻すボタン作成
print'<span class="button2">';
print '<input type="submit" formaction="status_back_update.php"name="statusback"
style="background-color: #87cefa; width: 120px; height: 35px"
value="Statusを戻す">';
print '</span>';
//戻るボタン作成
print'<span class="button3">';
print '<input type="submit" formaction="list_of_members.php"name="back"
style="background-color: #87cefa; width: 120px; height: 35px"
value="戻る">';
print '</span>';

?>
	<div align="center">
		<h3>交通費精算出力</h3>
		<?php
try {
    // //////////////////データベースの読込 S//////////////////////
	
    $dsn='mysql:dbname=pros-service_kinmu;host=mysql731.db.sakura.ne.jp;charset=utf8';
    $user='pros-service';
	$password='cl6cNJs2lt5W';

	$dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	//tbl_staffの値を全て取得
	//ローカル用
	// $staffsql = 'select * FROM  tbl_staff';
	//サーバー用
	$staffsql = 'select * FROM  TBL_STAFF';
    $staffstmt = $dbh->prepare($staffsql);
    $staffstmt->execute();
	
	//tbl_checkoutの値を全て取得
	//ローカル用
	// $checkoutsql = 'select * FROM  tbl_checkout';
	//サーバー用
	$checkoutsql = 'select * FROM  TBL_CHECKOUT';
    $checkoutstmt = $dbh->prepare($checkoutsql);
    $checkoutstmt->execute();

	//tbl_checkout_statusの値を全て取得
	//ローカル用
	// $statussql = 'select * FROM  tbl_checkout_status';
	//サーバー用
    $statussql = 'select * FROM  TBL_CHECKOUT_STATUS';
    $statusstmt = $dbh->prepare($statussql);
    $statusstmt->execute();

    $dbh = null;
	// ////////////////データベースの読込 E//////////////////////

//プルダウンの値を変数に代入
if (isset($_POST["select1"])) {
	$_SESSION["select1"] = $_POST["select1"];
	 $month = $_SESSION["select1"];
}
elseif(isset($_SESSION["select1"])){
	$month = $_SESSION["select1"];
}
else {
    //当月を表示
    $month = date("Y-m",strtotime("0 month"));
}
?>
			<span class="sample1">
			<?php print '表示する月:'?>
			<select name="select1">
				<?php
 print '<option value=""></option>';
  for ($i = 0; $i <=11 ; $i++) {
   if(isset($month)){
       $selected=(date("Y-m",strtotime(date('Y-m-01')."-$i month"))==$month ?" selected":"");
   }
    print "<option value=\"".date("Y-m",strtotime(date('Y-m-01')."-$i month"))."\"{$selected}>".date("Y-m",strtotime(date('Y-m-01')."-$i month"))."</option>";
 }

 print '</span>';
  ?>
			</select> <input type="submit" name="submit" value="表示" />
			
			
			<!-- <div id="output"></div> -->
	</div>


	<?php
$_SESSION["thuki"]=$_POST["select1"];


print '<span class="sample99">';
print '<table border=1  cellspacing="1" cellpadding="5">';

print '<tr><td>';
print '選択';
print '</td>';

print '<td>';
print '社員番号';
print '</td>';

print '<td>';
print '氏名';
print '</td>';

print '<td>';
print 'ステータス';
print '</td>';


$staffrec = $staffstmt->fetchAll(PDO::FETCH_ASSOC);
while (true) {
	// tbl_summaryから1レコード取得
	//$checkoutrec = $checkoutstmt->fetch(PDO::FETCH_ASSOC);
	$statusrec = $statusstmt->fetch(PDO::FETCH_ASSOC);
	//tbl_summaryの値がなくなったら繰り返し処理終了
	if ($statusrec == false) {
		break;
	}
	//// 精算書の日にちをYYYY-MMに変換
	$year = (substr($statusrec['year_and_month'], 0, 7));
	// 選択した年月日と一致した年月日を判定させる
	if ($year == $month) {
		print '<tr><td>';
	// 社員選択ボタンを作成
	$staffselect = $statusrec['staff_number'];

	if($flag == 0){
		print '<input type="radio"name="staffcode" checked="checked"value="'.$staffselect.'">';
		$flag = 1;
	}
	elseif($staffselect == $_SESSION['staffcode']){
		print '<input type="radio"name="staffcode" checked="checked"value="'.$staffselect.'">';
	}
	else{
		print '<input type="radio"name="staffcode" value="'.$staffselect.'">';
	}
	print '<td>';
	// 選択された日付をもとにスタッフナンバーを入れる
	print $statusrec['staff_number'];
	print '<td>';
	foreach($staffrec as $staffre){

		// print $staffre['staff_number'];
		// print $summaryrec['staff_number'];

				if ($staffre['staff_number'] == $statusrec['staff_number']) {
					print $staffre['familyname'];
					print $staffre['firstname'];
				}
			}
				print '<td>';
				$status = $statusrec['checkout_visit'];
				if ($status == '1') {
					print ' 途中完了';
				} else if ($status == '2') {
					print '手続き中';
				} else if ($status == '3') {
					print '手続き完了';
				}
				print '</td>
				</tr>';
		}
}
print '</span>';
print '</table>';


} catch (Exception $e) {
    print 'システムエラーが発生しました';
    exit();
}
?>
</form>
</body>
</html>