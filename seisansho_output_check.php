<?php
//印刷ボタン押下時、社員が選択されていなければseisansho_output.phpに遷移
if(empty($_POST['staffcode'])){
	header('Location:seisansho_output.php');
}
//セッションが開始されていなければセッションを開始する。
if(!isset($_SESSION)){
	session_start();
	session_regenerate_id(true);
}
$_SESSION['worklocation'] = $_POST['worklocation'];

//ラジオボタンの値を変数に代入, エラー表示を停止
error_reporting(8192);
$_SESSION['staffcode'] = $_POST['staffcode'];
$staff_number = $_SESSION['staffcode'];


if($_SESSION["thuki"]==NULL){
	$now_month1=date("Y/m");
}else{
	$now_month1=$_SESSION["thuki"];
}
// ログイン状態のチェック
if (isset($_SESSION["login"])==false) 
{
	header("Location: staff_login.php");
	exit();
}

if (isset($_POST["select1"])) {
	$_SESSION["select1"] = $_POST["select1"];
	$month = $_SESSION["select1"];
}
elseif(isset($_SESSION["select1"])){
	$month = $_SESSION["select1"];
}
else {
    //当月を表示
	$_SESSION["select1"] = date("Y-m",strtotime("0 month"));
	$month = $_SESSION["select1"];
}
try
{
	//DB接続
	// $dsn = 'mysql:dbname=kinmuhyo;host=localhost;charset=utf8';
	// $user = 'root';
	// $password = '';

	$dsn='mysql:dbname=pros-service_kinmu;host=mysql731.db.sakura.ne.jp;charset=utf8';
	$user='pros-service';
	$password='cl6cNJs2lt5W';

	$dbh= new PDO($dsn,$user,$password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sql="SELECT * FROM TBL_STAFF WHERE staff_number=?";
	$stmt=$dbh->prepare($sql);
	$data[] = $staff_number;
	$stmt->execute($data);
	$result = $stmt -> fetch(PDO::FETCH_ASSOC);
	$_SESSION['familyname'] = $result['familyname'];
	$_SESSION['firstname'] = $result['firstname'];

	//精算金額の合計金額をDBから取得
	//ローカル用
	// $staffsql3 = 'select SUM(Settlement_amount),staff_number as staff_number,year_and_month from tbl_checkout  GROUP BY staff_number,DATE_FORMAT(year_and_month, "%Y%m");';
	//サーバー用
	$staffsql3 = 'select SUM(Settlement_amount),staff_number as staff_number,year_and_month from TBL_CHECKOUT  GROUP BY staff_number,DATE_FORMAT(year_and_month, "%Y%m");';
	$staffstmt3 = $dbh->prepare($staffsql3);
	$staffstmt3->execute();

	//tbl_checkout_statusの値を全て取得
	//ローカル用
	$sql="SELECT * FROM TBL_CHECKOUT_STATUS WHERE staff_number=:staff_number and year_and_month=:year_and_month";
	$stmt=$dbh->prepare($sql);
	$stmt->bindValue(":staff_number",$staff_number,PDO::PARAM_STR);
	$stmt->bindValue(":year_and_month",$_POST['select1'],PDO::PARAM_STR);
	$stmt->execute();
	$statusrec = $stmt -> fetchAll(PDO::FETCH_ASSOC);
	foreach($statusrec as $A => $B){
		if(isset($B['No'])){
			$no[$A] = $B['No'];
		}
		$Applicationdate[$A] = $B['Application date'];
	}


	if(isset($month)){
		$s_year_and_month = $month.date("-01");
		$now_month = date('t', strtotime($s_year_and_month));
		$e_year_and_month = $month.date("-".$now_month);
	}
	date_default_timezone_set('Asia/Tokyo');
	$rec = false;

	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql="SELECT * FROM TBL_CHECKOUT WHERE staff_number=:staff_number AND year_and_month BETWEEN :s_year_and_month AND :e_year_and_month ORDER BY year_and_month ASC";
		$stmt=$dbh->prepare($sql);
		$stmt->bindValue(":staff_number",$staff_number,PDO::PARAM_STR);
		$stmt->bindValue(":s_year_and_month",$s_year_and_month,PDO::PARAM_STR);
		$stmt->bindValue(":e_year_and_month",$e_year_and_month,PDO::PARAM_STR);
		$stmt->execute();
		$rec = $stmt -> fetchAll(PDO::FETCH_ASSOC);

		foreach($rec as $A => $B){
			if(isset($B['No'])){
				$no[$A] = $B['No'];
			}
			$visit[$A] = $B['visit'];
			$year_and_month[$A] = $B['year_and_month'];
			$Point_of_departure[$A] = $B['Point_of_departure'];
			$Checkout_flag[$A] = $B['Checkout_flag'];
			$Point_of_Arrival[$A] = $B['Point_of_Arrival'];
			$Settlement_amount[$A] = $B['Settlement_amount'];
		}
	$dbh=null;

}catch(Exception $e){
	print 'システムエラーが発生しました。';
	exit();
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style type="text/css">	
.print_pdf{
	width: 250px;
	height: 70px;
	background-color: #a0d8ef;
}
.back_pdf{
	width: 250px;
	height: 70px;
	background-color: #a0d8ef;
}
.print_btn{
	margin-top: 180px;
	text-align: center;
}
@media print {
  .hoge{
    display: none;
  }
}
span.sample8 {
page-break-before: always;
}

@media print {
.visibility_test {
    display:  inline-block;
    vertical-align: top;
}
/* 印刷時、非表示にする */
.visibility_test2 {
    display:  none;
    visibility:  hidden;
	}
}
@media screen {
.screenhidden {
display:  none;
	}
}
</style>
<title>印刷画面</title>
</head>
<body>

<h2 align="center"><?php print substr($_POST['select1'],0,4)?>/<?php print substr($_POST['select1'],5)?>月分交通費精算</h2>

<table border="1" width="300" cellspacing="0" cellpadding="5" bordercolor="#333333" id="infield" align="left">
<thead>
<tr>
<th>申請日</th>
<th><?php print substr($Applicationdate[0], 0,4).'年'. substr($Applicationdate[0],5,-3).'月'.substr($Applicationdate[0],8).'日';?></th>
</tr>
</thead>
<tbody>
<tr>
<th>申請者</th>
<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php print $_SESSION['familyname'] . $_SESSION['firstname'];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;印</th>
</tr>
</tbody>
</table>
<br><br><br><br>
	<!-- ////////////////////////////////////////////////////////////////////初期表示S///////////////////////////////////////////////////////////////////////////// -->
<form method="post">
<?php if(count($rec) >= 15){ ?>
<div class="visibility_test2">
<table border="1" cellspacing="0" cellpadding="5"bordercolor="#333333" id="table1" align="center">
<tr>
<th width="143">月日</th>
<th width="500">訪問先</th>
<th width="500">経路</th>
<th width="143">金額</th>
</tr>
<?php for ($i = 0; $i < count($rec) ; $i++){ ?>
<tr>
<!-- //月日 -->
<td><div align="center"><nobr><?php print substr($year_and_month[$i], 5,-3).'/'.substr($year_and_month[$i], 8)?></nobr></div></td>
<!-- //訪問先 -->
<td><div align="center"><nobr><?php print $visit[$i];?></nobr></div></td>
<!-- //訪問先 -->
<td><div align="center"><?php print $Point_of_departure[$i];?>&nbsp;&nbsp;&nbsp;&nbsp;<?php
if($Checkout_flag[$i] == 2){
	print '⇔';
}elseif($Checkout_flag[$i] == 1){
	print '→';
	}?>
&nbsp;&nbsp;&nbsp;&nbsp;<?php print $Point_of_Arrival[$i]?></div></td>
<!-- //金額 -->
<td><div align="right"><nobr><?php print '¥' . $Settlement_amount[$i];?></nobr></div></td>
</tr>
<?php } 
?>
<tr>
<td></td>
<td></td>
<td><div align="center">計</div></td>
<td><div align="right"> 
<?php
//データベースからyear_and_month,staff_numberごとの生産金額の合計を取得
while(true){
	$rec3 = $staffstmt3 -> fetch(PDO::FETCH_ASSOC);
	 $years = (substr($rec3['year_and_month'], 0, 7));

if($rec3 == false){
	break;
}
if($month == $years){
	if($rec3['staff_number'] == $staff_number){
	print'￥'.$rec3["SUM(Settlement_amount)"];
		}
	}
}
?>
</div></td>
</tr>
</table>
</div>
<?php }
else{
	?>
<div class="visibility_test2">
<table border="1" cellspacing="0" cellpadding="5"bordercolor="#333333" id="table1" align="center">
<tr>
<th width="143">月日</th>
<th width="500">訪問先</th>
<th width="500">経路</th>
<th width="143">金額</th>
</tr>
	
<?php for ($i = 0; $i < 15 ; $i++){ ?>
<tr>
<!-- //月日 -->
<td><div align="center"><nobr><?php print substr($year_and_month[$i], 5,-3).'/'.substr($year_and_month[$i], 8)?></nobr></div></td>
<!-- //訪問先 -->
<td><div align="center"><nobr><?php print $visit[$i];?></nobr></div></td>
<!-- //訪問先 -->
<td><div align="center"><?php print $Point_of_departure[$i]?>&nbsp;&nbsp;&nbsp;&nbsp;<?php
if($Checkout_flag[$i] == 2){
	print '⇔';
}elseif($Checkout_flag[$i] == 1){
	print '→';
	}?>
&nbsp;&nbsp;&nbsp;&nbsp;<?php print $Point_of_Arrival[$i]?></div></td>
<!-- //金額 -->
<td><div align="right"><nobr><?php print '¥' . $Settlement_amount[$i];?></nobr></div></td>
</tr>
<?php } ?>
<tr>
<td></td>
<td></td>
<td><div align="center">計</div></td>
<td><div align="right"> 
<?php
//データベースからyear_and_month,staff_numberごとの生産金額の合計を取得
while(true){
	$rec3 = $staffstmt3 -> fetch(PDO::FETCH_ASSOC);
	$years = (substr($rec3['year_and_month'], 0, 7));
	
	if($rec3 == false){
		break;
	}
	if($month == $years){
		if($rec3['staff_number'] == $staff_number){
		print'￥'.$rec3["SUM(Settlement_amount)"];
			}
		}
	}
?>
</div></td>
</tr>
</table>
</div>
<?php
}
?>

<!-- ////////////////////////////////////////////////////////////////////印刷画面///////////////////////////////////////////////////////////////////////////// -->
<div class="screenhidden">
<table border="1" cellspacing="0" cellpadding="5"bordercolor="#333333" id="table1" align="center">
<tr>
<th><nobr>月日</nobr></th>
<th width="520">訪問先</th>
<th width="520">経路</th>
<th width="140"><nobr>金額</nobr></th>
</tr>
<?php for ($i = 0; $i < 15 ; $i++){ ?>
<tr>
<!-- //月日 -->
<td><div align="center"><nobr><?php print substr($year_and_month[$i], 5,-3).'/'.substr($year_and_month[$i], 8)?></nobr></div></td>
<!-- //訪問先 -->
<td><div align="center"><nobr><?php print $visit[$i];?></nobr></div></td>
<!-- //訪問先 -->
<td><div align="center"><?php print $Point_of_departure[$i];?>&nbsp;&nbsp;&nbsp;&nbsp;<?php
if($Checkout_flag[$i] == 2){
	print '⇔';
}elseif($Checkout_flag[$i] == 1){
	print '→';
	}?>
&nbsp;&nbsp;&nbsp;&nbsp;<?php print $Point_of_Arrival[$i]?></div></td>
<!-- //金額 -->
<td><div align="right"><nobr><?php print '¥' . $Settlement_amount[$i];?></nobr></div></td>
</tr>
<?php } 
?>
<tr>
<td></td>
<td></td>
<td><div align="center">計</div></td>
<td><div align="right">
<?php 	$a = $Settlement_amount[0] + $Settlement_amount[1] + $Settlement_amount[2] + $Settlement_amount[3] + $Settlement_amount[4] + $Settlement_amount[5] + $Settlement_amount[6] + $Settlement_amount[7] + $Settlement_amount[8] + $Settlement_amount[9] + $Settlement_amount[10] + $Settlement_amount[11] + $Settlement_amount[12] + $Settlement_amount[13] + $Settlement_amount[14];
print '￥' . $a;
?>
</div>
</td>
</tr>
</table>
</div>
<br><br>
<table style="border: none" cellspacing="1" cellpadding="15"
			bordercolor="#333333"align="left">
			<tr>
				<td width="60px" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;凡例</strong></td>
			</tr>
			<tr>
				<td width="60px" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;往復</strong></td>
				<td width="60px" align="center" style="border: none"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;⇔</strong></td>
			</tr>
			<tr>
				<td width="60px" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;片道</strong></td>
				<td width="60px" align="center" style="border: none"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;→</strong></td>
			</tr>
</table>
<table border="1" id = "pitcher" width="300" cellspacing="0" cellpadding="5" bordercolor="#333333"align="right">
<tr>
<th>責任者確認印</th>
<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;担当印&nbsp;&nbsp;&nbsp;&nbsp;</th>
</tr>

<tr>
<th><br><br><br><br></th>
<th></th>
</tr>
</table>

<div class="print_btn">
<div  class="hoge">
	<input type="submit" name="insatu" id="print" class="print_pdf" value="印刷する">
	<input type="submit" id="back" class="back_pdf" value="戻る">
</div>
</div>
<!--ボタン押下で印刷させるか、いきなりプレビュー画面出すかで変わる!-->
<script type="text/javascript">
//いきなりプレビュー
document.getElementById("print").onclick = function() {
window.print();
};
document.getElementById("back").onclick = function() {
 location.href = "seisansho_output.php";
};
</script>
<!-- ////////////////////////////////////////////////////////////////////初期表示E///////////////////////////////////////////////////////////////////////////// -->

<!-- //count($rec)が16以上の時の処理 -->
<div class = "screenhidden">
<?php if(count($rec) >= 16){ ?>
<div style="page-break-after: always;"></div>
<!-- //改ページ -->
<h2 align="center"><?php print substr($_POST['select1'],0,4)?>/<?php print substr($_POST['select1'],5)?>月分交通費精算</h2>
<table border="1" width="300" cellspacing="0" cellpadding="5" bordercolor="#333333" id="infield" align="left">
<thead>
<tr>
<th>申請日</th>
<th><?php print substr($Applicationdate[0], 0,4).'年'. substr($Applicationdate[0],5,-3).'月'.substr($Applicationdate[0],8).'日';?></th>
</tr>
</thead>
<tbody>
<tr>
<th>申請者</th>
<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php print $_SESSION['familyname'] . $_SESSION['firstname'];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;印</th>

</tr>
</tbody>
</table>

<br><br><br><br>


<div class="screenhidden">
<table border="1" cellspacing="0" cellpadding="5"bordercolor="#333333" id="table1" align="center">
<tr>
<th>月日</th>
<th width="520">訪問先</th>
<th width="520">経路</th>
<th width="143">金額</th>
</tr>
<?php for ($i = 15; $i < 30 ; $i++){ ?>
<tr>
<!-- //月日 -->
<td><div align="center"><nobr><?php print substr($year_and_month[$i], 5,-3).'/'.substr($year_and_month[$i], 8)?></nobr></div></td>
<!-- //訪問先 -->
<td><div align="center"><nobr><?php print $visit[$i];?></nobr></div></td>
<!-- //訪問先 -->
<td><div align="center"><?php print $Point_of_departure[$i];?>&nbsp;&nbsp;&nbsp;&nbsp;<?php
if($Checkout_flag[$i] == 2){
	print '⇔';
}elseif($Checkout_flag[$i] == 1){
	print '→';
	}?>
&nbsp;&nbsp;&nbsp;&nbsp;<?php print $Point_of_Arrival[$i]?></div></td>
<!-- //金額 -->
<td><div align="right"><nobr><?php print '¥' . $Settlement_amount[$i];?></nobr></div></td>
</tr>
<?php } 
?>
<tr>
<td></td>
<td></td>
<td><div align="center">計</div></td>
<td><div align="right">
<?php $a = $Settlement_amount[15] + $Settlement_amount[16] + $Settlement_amount[17] + $Settlement_amount[18] + $Settlement_amount[19] + $Settlement_amount[20] + $Settlement_amount[21] + $Settlement_amount[22] + $Settlement_amount[23] + $Settlement_amount[24] + $Settlement_amount[25] + $Settlement_amount[26] + $Settlement_amount[27] + $Settlement_amount[28] + $Settlement_amount[29];
print '￥' . $a;
?>
</div></td>
</tr>
</table>
</div>
<br><br>
<table style="border: none" cellspacing="1" cellpadding="15"
			bordercolor="#333333"align="left">
			<tr>
				<td width="60px" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;凡例</strong></td>
			</tr>
			<tr>
				<td width="60px" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;往復</strong></td>
				<td width="60px" align="center" style="border: none"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;⇔</strong></td>
			</tr>
			<tr>
				<td width="60px" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;片道</strong></td>
				<td width="60px" align="center" style="border: none"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;→</strong></td>
			</tr>
</table>
<table border="1" id = "pitcher" width="300" cellspacing="0" cellpadding="5" bordercolor="#333333"align="right">
<tr>
<th>責任者確認印</th>
<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;担当印&nbsp;&nbsp;&nbsp;&nbsp;</th>
</tr>

<tr>
<th><br><br><br><br></th>
<th></th>
</tr>
</table>

<?php } ?>
</div>

<!-- //count($rec)が31以上の時の処理 -->
<div class = "screenhidden">
<?php if(count($rec) >= 31){ ?>
<div style="page-break-after: always;"></div>
<!-- //改ページ -->
<h2 align="center"><?php print substr($_POST['select1'],0,4)?>/<?php print substr($_POST['select1'],5)?>月分交通費精算</h2>
<table border="1" width="300" cellspacing="0" cellpadding="5" bordercolor="#333333" id="infield" align="left">
<thead>
<tr>
<th>申請日</th>
<th><?php print substr($Applicationdate[0], 0,4).'年'. substr($Applicationdate[0],5,-3).'月'.substr($Applicationdate[0],8).'日';?></th>
</tr>
</thead>
<tbody>
<tr>
<th>申請者</th>
<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php print $_SESSION['familyname'] . $_SESSION['firstname'];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;印</th>
</tr>
</tbody>
</table>

<br><br><br><br>

<div class="screenhidden">
<table border="1" cellspacing="0" cellpadding="5"bordercolor="#333333" id="table1" align="center">
<tr>
<th>月日</th>
<th width="520">訪問先</th>
<th width="520">経路</th>
<th width="143">金額</th>
</tr>
<?php for ($i = 30; $i < 45 ; $i++){ ?>
<tr>
<!-- //月日 -->
<td><div align="center"><nobr><?php print substr($year_and_month[$i], 5,-3).'/'.substr($year_and_month[$i], 8)?></nobr></div></td>
<!-- //訪問先 -->
<td><div align="center"><nobr><?php print $visit[$i];?></nobr></div></td>
<!-- //訪問先 -->
<td><div align="center"><?php print $Point_of_departure[$i];?>&nbsp;&nbsp;&nbsp;&nbsp;<?php
if($Checkout_flag[$i] == 2){
	print '⇔';
}elseif($Checkout_flag[$i] == 1){
	print '→';
	}?>
&nbsp;&nbsp;&nbsp;&nbsp;<?php print $Point_of_Arrival[$i]?></div></td>
<!-- //金額 -->
<td><div align="right"><nobr><?php print '¥' . $Settlement_amount[$i];?></nobr></div></td>
</tr>
<?php } 
?>
<tr>
<td></td>
<td></td>
<td><div align="center">計</div></td>
<td><div align="right"> 
<?php 	$a = $Settlement_amount[30] + $Settlement_amount[31] + $Settlement_amount[32] + $Settlement_amount[33] + $Settlement_amount[34] + $Settlement_amount[35] + $Settlement_amount[36] + $Settlement_amount[37] + $Settlement_amount[38] + $Settlement_amount[39] + $Settlement_amount[40] + $Settlement_amount[41] + $Settlement_amount[42] + $Settlement_amount[43] + $Settlement_amount[44];
print '￥' . $a;
?>
</div></td>
</tr>
</table>
</div>
<br><br>
<table style="border: none" cellspacing="1" cellpadding="15"
			bordercolor="#333333"align="left">
			<tr>
				<td width="60px" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;凡例</strong></td>
			</tr>
			<tr>
				<td width="60px" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;往復</strong></td>
				<td width="60px" align="center" style="border: none"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;⇔</strong></td>
			</tr>
			<tr>
				<td width="60px" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;片道</strong></td>
				<td width="60px" align="center" style="border: none"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;→</strong></td>
			</tr>
</table>
<table border="1" id = "pitcher" width="300" cellspacing="0" cellpadding="5" bordercolor="#333333"align="right">
<tr>
<th>責任者確認印</th>
<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;担当印&nbsp;&nbsp;&nbsp;&nbsp;</th>
</tr>

<tr>
<th><br><br><br><br></th>
<th></th>
</tr>
</table>

<?php } ?>
</div>


<div class = "screenhidden">
<?php if(count($rec) >= 46){ ?>
<div style="page-break-after: always;"></div>
<!-- //改ページ -->
<h2 align="center"><?php print substr($_POST['select1'],0,4)?>/<?php print substr($_POST['select1'],5)?>月分交通費精算</h2>
<table border="1" width="300" cellspacing="0" cellpadding="5" bordercolor="#333333" id="infield" align="left">
<thead>
<tr>
<th>申請日</th>
<th><?php print substr($Applicationdate[0], 0,4).'年'. substr($Applicationdate[0],5,-3).'月'.substr($Applicationdate[0],8).'日';?></th>
</tr>
</thead>
<tbody>
<tr>
<th>申請者</th>
<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php print $_SESSION['familyname'] . $_SESSION['firstname'];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;印</th>
</tr>
</tbody>
</table>

<br><br><br><br>

<div class="screenhidden">
<table border="1" cellspacing="0" cellpadding="5"bordercolor="#333333" id="table1" align="center">
<tr>
<th>月日</th>
<th width="520">訪問先</th>
<th width="520">経路</th>
<th width="143">金額</th>
</tr>
<?php for ($i = 45; $i < 60; $i++){ ?>
<tr>
<!-- //月日 -->
<td><div align="center"><nobr><?php print substr($year_and_month[$i], 5,-3).'/'.substr($year_and_month[$i], 8)?></nobr></div></td>
<!-- //訪問先 -->
<td><div align="center"><nobr><?php print $visit[$i];?></nobr></div></td>
<!-- //訪問先 -->
<td><div align="center"><?php print $Point_of_departure[$i];?>&nbsp;&nbsp;&nbsp;&nbsp;<?php
if($Checkout_flag[$i] == 2){
	print '⇔';
}elseif($Checkout_flag[$i] == 1){
	print '→';
	}?>
&nbsp;&nbsp;&nbsp;&nbsp;<?php print $Point_of_Arrival[$i]?></div></td>
<!-- //金額 -->
<td><div align="right"><nobr><?php print '¥' . $Settlement_amount[$i];?></nobr></div></td>
</tr>
<?php } 
?>
<tr>
<td></td>
<td></td>
<td><div align="center">計</div></td>
<td><div align="right"> 
<?php 	$a = $Settlement_amount[45] + $Settlement_amount[46] + $Settlement_amount[47] + $Settlement_amount[48] + $Settlement_amount[49] + $Settlement_amount[50] + $Settlement_amount[51] + $Settlement_amount[52] + $Settlement_amount[53] + $Settlement_amount[54] + $Settlement_amount[55] + $Settlement_amount[56] + $Settlement_amount[57] + $Settlement_amount[58] + $Settlement_amount[59];
print '￥' . $a;
?>
</div></td>
</tr>
</table>
</div>
<br><br>
<table style="border: none" cellspacing="1" cellpadding="15"
			bordercolor="#333333"align="left">
			<tr>
				<td width="60px" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;凡例</strong></td>
			</tr>
			<tr>
				<td width="60px" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;往復</strong></td>
				<td width="60px" align="center" style="border: none"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;⇔</strong></td>
			</tr>
			<tr>
				<td width="60px" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;片道</strong></td>
				<td width="60px" align="center" style="border: none"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;→</strong></td>
			</tr>
</table>
<table border="1" id = "pitcher" width="300" cellspacing="0" cellpadding="5" bordercolor="#333333"align="right">
<tr>
<th>責任者確認印</th>
<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;担当印&nbsp;&nbsp;&nbsp;&nbsp;</th>
</tr>
<tr>
<th><br><br><br><br></th>
<th></th>
</tr>
</table>
<?php } ?>
</div>


<div class = "screenhidden">
<?php if(count($rec) >= 61){ ?>
<div style="page-break-after: always;"></div>
<!-- //改ページ -->
<h2 align="center"><?php print substr($_POST['select1'],0,4)?>/<?php print substr($_POST['select1'],5)?>月分交通費精算</h2>
<table border="1" width="300" cellspacing="0" cellpadding="5" bordercolor="#333333" id="infield" align="left">
<thead>
<tr>
<th>申請日</th>
<th><?php print substr($Applicationdate[0], 0,4).'年'. substr($Applicationdate[0],5,-3).'月'.substr($Applicationdate[0],8).'日';?></th>
</tr>
</thead>
<tbody>
<tr>
<th>申請者</th>
<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php print $_SESSION['familyname'] . $_SESSION['firstname'];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;印</th>
</tr>
</tbody>
</table>

<br><br><br><br>

<div class="screenhidden">
<table border="1" cellspacing="0" cellpadding="5"bordercolor="#333333" id="table1" align="center">
<tr>
<th>月日</th>
<th width="520">訪問先</th>
<th width="520">経路</th>
<th>金額</th>
</tr>
<?php for ($i = 60; $i < 75; $i++){ ?>
<tr>
<!-- //月日 -->
<td><div align="center"><nobr><?php print substr($year_and_month[$i], 5,-3).'/'.substr($year_and_month[$i], 8)?></nobr></div></td>
<!-- //訪問先 -->
<td><div align="center"><nobr><?php print $visit[$i];?></nobr></div></td>
<!-- //訪問先 -->
<td><div align="center"><?php print $Point_of_departure[$i];?>&nbsp;&nbsp;&nbsp;&nbsp;<?php
if($Checkout_flag[$i] == 2){
	print '⇔';
}elseif($Checkout_flag[$i] == 1){
	print '→';
	}?>
&nbsp;&nbsp;&nbsp;&nbsp;<?php print $Point_of_Arrival[$i]?></div></td>
<!-- //金額 -->
<td><div align="right"><nobr><?php print '¥' . $Settlement_amount[$i];?></nobr></div></td>
</tr>
<?php } 
?>
<tr>
<td></td>
<td></td>
<td><div align="center">計</div></td>
<td><div align="right"> 
<?php 	$a = $Settlement_amount[60] + $Settlement_amount[61] + $Settlement_amount[62] + $Settlement_amount[63] + $Settlement_amount[64] + $Settlement_amount[65] + $Settlement_amount[66] + $Settlement_amount[67] + $Settlement_amount[68] + $Settlement_amount[69] + $Settlement_amount[70] + $Settlement_amount[71] + $Settlement_amount[72] + $Settlement_amount[73] + $Settlement_amount[74];
print '￥' . $a;
?>
</div></td>
</tr>
</table>
</div>
<br><br>
<table style="border: none" cellspacing="1" cellpadding="15"
			bordercolor="#333333"align="left">
			<tr>
				<td width="60px" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;凡例</strong></td>
			</tr>
			<tr>
				<td width="60px" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;往復</strong></td>
				<td width="60px" align="center" style="border: none"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;⇔</strong></td>
			</tr>
			<tr>
				<td width="60px" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;片道</strong></td>
				<td width="60px" align="center" style="border: none"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;→</strong></td>
			</tr>
</table>
<table border="1" id = "pitcher" width="300" cellspacing="0" cellpadding="5" bordercolor="#333333"align="right">
<tr>
<th>責任者確認印</th>
<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;担当印&nbsp;&nbsp;&nbsp;&nbsp;</th>
</tr>

<tr>
<th><br><br><br><br></th>
<th></th>
</tr>
</table>

<?php } ?>
</div>

<div class = "screenhidden">
<?php if(count($rec) >= 76){ ?>
<div style="page-break-after: always;"></div>
<!-- //改ページ -->
<h2 align="center"><?php print substr($_POST['select1'],0,4)?>/<?php print substr($_POST['select1'],5)?>月分交通費精算</h2>
<table border="1" width="300" cellspacing="0" cellpadding="5" bordercolor="#333333" id="infield" align="left">
<thead>
<tr>
<th>申請日</th>
<th><?php print substr($Applicationdate[0], 0,4).'年'. substr($Applicationdate[0],5,-3).'月'.substr($Applicationdate[0],8).'日';?></th>
</tr>
</thead>
<tbody>
<tr>
<th>申請者</th>
<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php print $_SESSION['familyname'] . $_SESSION['firstname'];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;印</th>
</tr>
</tbody>
</table>

<br><br><br><br>

<div class="screenhidden">
<table border="1" cellspacing="0" cellpadding="5"bordercolor="#333333" id="table1" align="center">
<tr>
<th>月日</th>
<th width="520">訪問先</th>
<th width="520">経路</th>
<th>金額</th>
</tr>
<?php for ($i = 75; $i < 90; $i++){ ?>
<tr>
<!-- //月日 -->
<td><div align="center"><nobr><?php print substr($year_and_month[$i], 5,-3).'/'.substr($year_and_month[$i], 8)?></nobr></div></td>
<!-- //訪問先 -->
<td><div align="center"><nobr><?php print $visit[$i];?></nobr></div></td>
<!-- //訪問先 -->
<td><div align="center"><?php print $Point_of_departure[$i];?><?php
if($Checkout_flag[$i] == 2){
	print '⇔';
}elseif($Checkout_flag[$i] == 1){
	print '→';
	}?>
<?php print $Point_of_Arrival[$i]?></div></td>
<!-- //金額 -->
<td><div align="right"><nobr><?php print '¥' . $Settlement_amount[$i];?></nobr></div></td>
</tr>
<?php } 
?>
<tr>
<td></td>
<td></td>
<td><div align="center">計</div></td>
<td> 
<?php 	$a = $Settlement_amount[75] + $Settlement_amount[76] + $Settlement_amount[77] + $Settlement_amount[78] + $Settlement_amount[79] + $Settlement_amount[80] + $Settlement_amount[81] + $Settlement_amount[82] + $Settlement_amount[83] + $Settlement_amount[84] + $Settlement_amount[85] + $Settlement_amount[86] + $Settlement_amount[87] + $Settlement_amount[88] + $Settlement_amount[89];
print '￥' . $a;
?>
</td>
</tr>
</table>
</div>
<br><br>
<table style="border: none" cellspacing="1" cellpadding="15"
			bordercolor="#333333"align="left">
			<tr>
				<td width="60px" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;凡例</strong></td>
			</tr>
			<tr>
				<td width="60px" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;往復</strong></td>
				<td width="60px" align="center" style="border: none"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;⇔</strong></td>
			</tr>
			<tr>
				<td width="60px" align="center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;片道</strong></td>
				<td width="60px" align="center" style="border: none"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;→</strong></td>
			</tr>
</table>
<table border="1" id = "pitcher" width="300" cellspacing="0" cellpadding="5" bordercolor="#333333"align="right">
<tr>
<th>責任者確認印</th>
<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;担当印&nbsp;&nbsp;&nbsp;&nbsp;</th>
</tr>

<tr>
<th><br><br><br><br></th>
<th></th>
</tr>
</table>

<?php } ?>
</div>
</body>
</html>