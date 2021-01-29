<?php
if(!isset($_SESSION)){
session_start();

unset($_SESSION['delete']);

}
if(empty($_SESSION['err'])){
	$_SESSION['err']=2;
}


ob_start();
include("kinmu_common.php");
ob_clean();

// エラー表示を停止
error_reporting(8192);

//selectboxの月の値を代入
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
/////////////////////////////////////////////errチェックs////////////////////////////////////////////

print '<font color="red">';
print'<span class="errmsg1">';
if(isset($_SESSION['errmsg1'])){
print $_SESSION['errmsg1'];
}
if(isset($_SESSION['errmsg2'])){
print $_SESSION['errmsg2'];
}
if(isset($_SESSION['errmsg3'])){
print $_SESSION['errmsg3'];
}
if(isset($_SESSION['errmsg4'])){
print $_SESSION['errmsg4'];
}
if(isset($_SESSION['errmsg5'])){
print $_SESSION['errmsg5'];
}
if(isset($_SESSION['errmsg6'])){
print $_SESSION['errmsg6'];
}
if(isset($_SESSION['errmsg7'])){
print $_SESSION['errmsg7'];
}
print'</span>';


print'</font>';
/////////////////////////////////////////////errチェックe////////////////////////////////////////////
// ログイン状態のチェック
if (isset($_SESSION["login"])==false) 
{
	header("Location: staff_login.php");
	exit();
}
try {
	//ログイン情報を変数に代入
	$result = $_SESSION['result'];

	if(isset($_POST['staffcode'])==false){
	$staff_number=$result['staff_number'];
	$familyname = $result['familyname'];
	$firstname = $result['firstname'];
	$No = 'No.';
	}elseif(isset($_POST['staffcode'])){
	$staff_number=$_POST['staffcode'];
	$familyname = $_SESSION['familyname'];
	$firstname = $_SESSION['firstname'];
	$No = 'No.';
}
if(empty($_SESSION['worklocation'])){
	print "<strong>" .$No."</strong>";
	print "<strong>".$staff_number."</strong>";
	print "<strong>".$familyname."</strong>";
	print "<strong>".$firstname."</strong>";
}
	// //////////////////データベースの読込 S//////////////////////
	if(isset($month)){
		$s_year_and_month = $month.date("-01");
		$now_month = date('t', strtotime($s_year_and_month));
		$e_year_and_month = $month.date("-".$now_month);
	}
	date_default_timezone_set('Asia/Tokyo');
	$rec = false;

	$dsn='mysql:dbname=pros-service_kinmu;host=mysql731.db.sakura.ne.jp;charset=utf8';
    $user='pros-service';
	$password='cl6cNJs2lt5W';
	
	$dbh = new PDO($dsn, $user, $password);

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
		
	//ローカル用
	// $staffsql3 = 'select SUM(Settlement_amount),staff_number as staff_number,year_and_month from tbl_checkout  GROUP BY staff_number,DATE_FORMAT(year_and_month, "%Y%m");';
	//サーバー用
	$staffsql3 = 'select SUM(Settlement_amount),staff_number as staff_number,year_and_month from TBL_CHECKOUT  GROUP BY staff_number,DATE_FORMAT(year_and_month, "%Y%m");';
	$staffstmt3 = $dbh->prepare($staffsql3);
	$staffstmt3->execute();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
span.sample2 {
	position: absolute;
	top: 162px;
	left: 850px
}

span.sample3 {
	position: absolute;
	top: 62px;
	left: 850px
}

span.sample4 {
	position: absolute;
	top: 112px;
	left: 850px
}

span.sample10 {
	position: absolute;
	top: 212px;
	left: 850px
}

span.sample5 {
	position: absolute;
	top: 262px;
	left: 850px
}

span.sample6 {
	position: absolute;
	top: 6px;
	left: 870px
}

span.sample7 {
	position: absolute;
	top: 25px;
	left: 850px
}

span.sample8 {
	position: absolute;
	top: 10px;
	left: 840px
}

span.seisansho {
	position: absolute;
	top: 100px;
	left: 150px
}

span.seisansho2 {
	position: absolute;
	top: 100px;
	left: 100px
}

span.errmsg1 {
	position: absolute;
	top: 370px;
	left: 1000px
}

span.sample {
	position: absolute;
	top: -90px;
	left: 1070px
}
span.tables {
	position: absolute;
	top: 402px;
	left: 0px;
}
.table_sticky {
    display: block;
    overflow-y: scroll;
    height: 361px;
    border:1px solid;
}
.table_sticky thead th {
    position: sticky;
    top: 0;
    z-index: 1;
    background: White;
    border-top:black;
}




</style>
<title>交通費精算画面</title>
</head>
<body>
<?php if(empty($_SESSION['worklocation'])){ ?>
	<div align="center">
		<h2>交通費精算書</h2>
	</div>
<?php } ?>


	<!-- $_SESSION['worklocation']が空の時の処理 -->
	<?php if(empty($_SESSION['worklocation'])){?>
	<span class="seisansho"> <?php } ?> <!-- $_SESSION['worklocation']に値が入っている時の処理 -->
	
	<?php if(empty($_SESSION['worklocation'])){ ?>
		 <span class="sample"> <img class="img"
			src="/img/image_2020_4_10.png" alt="ロゴ" width="100" height="100">
	</span>
	<?php } ?>

<?php if(empty($_SESSION['worklocation'])){ ?>
		<form method="GET" action="#">
			<input type="button" value="テーブルに行を追加" onClick="AddTableRows();" />
		</form>
		
		<?php }	?> 


<?php

?>
		<form method="post">
			<div align="center">
	<!-- テーブル作成 -->
				<?php if(empty($_SESSION['worklocation'])){?>
				<table border="1" class="table_sticky" cellspacing="0" cellpadding="5"
					bordercolor="#333333" id="table1"c>
				<?php }?>
				
				<?php if(isset($_SESSION['worklocation'])){?>
				<table border="1" cellspacing="0" cellpadding="5"
					bordercolor="#333333" id="table1"c>
				<?php }?>
				<?php
?>
					<thead>
					<tr>
						<?php if(empty($_SESSION['worklocation'])){?>
						<!-- <th></th> -->
						<th>月日</th>
						<th>訪問先</th>
						<th>経路</th>
						<th>金額</th>
						<?php }?>
						<?php if(isset($_SESSION['worklocation'])){?>
						<!-- <th></th> -->
						<th>月日</th>
						<th width="520">訪問先</th>
						<th width="520">経路</th>
						<th>金額</th>
						<?php }?>

						<?php if(empty($_SESSION['worklocation'])){?>
						<th></th>
						<?php }?>
					</tr>
					</thead>
					<?php
print '<tbody>';
print '<tr>';

print '</nobr>';
print '</span>';




//精算書データを昇順に表示する処理
if(isset($_SESSION['worklocation'])){
$sql="SELECT * FROM tbl_checkout WHERE staff_number=:staff_number AND year_and_month BETWEEN :s_year_and_month AND :e_year_and_month ORDER BY year_and_month asc";
$stmt=$dbh->prepare($sql);
$stmt->bindValue(":staff_number",$staff_number,PDO::PARAM_STR);
$stmt->bindValue(":s_year_and_month",$s_year_and_month,PDO::PARAM_STR);
$stmt->bindValue(":e_year_and_month",$e_year_and_month,PDO::PARAM_STR);
$stmt->execute();
$rec = $stmt -> fetchAll(PDO::FETCH_ASSOC);
foreach($rec as $A => $B){
	$no[$A] = $B['No'];
	if(isset($visit)){
		$visit[$A] = $B['visit'];
	}
	$year_and_month[$A] = $B['year_and_month'];
	$Point_of_departure[$A] = $B['Point_of_departure'];
	$Checkout_flag[$A] = $B['Checkout_flag'];
	$Point_of_Arrival[$A] = $B['Point_of_Arrival'];
	$Settlement_amount[$A] = $B['Settlement_amount'];
	
	}
}
//空文字を変数に代入
if($visit == null){
	$visit[] = '';
}
?>
					<?php ///////////////////////////////////////////////////// テーブルを追加する /////////////////////////////////////////////////////////?>
					<script type="text/javascript">
						var phpSession = <?php echo json_encode($_SESSION['houmon1']);?>;
						if (phpSession != null) {
							var s1 = phpSession.slice();
							var session = []
							for (var i = 0; i < phpSession.length; i++) {
								session[i] = s1[i];
							}
						} else {
							var session = []
						}
						var counter = 0;
						function AddTableRows() {
							if (session[counter] === undefined) {
								session[counter] = "";
							}

							var Session = <?php echo json_encode($_SESSION['houmon']);?>;
							var phpSession = <?php echo json_encode($visit);?>;
							phpSession.length;
							if (Session === null) {
								if(phpSession.length <= 9){
								var table1 = document.getElementById("table1");
								var row1 = table1.insertRow(counter + 10);
								console.log('if');
								console.log(counter + 10);
								}
								else{
								var table1 = document.getElementById("table1");
								var row1 = table1.insertRow(counter + phpSession.length + 1);
								console.log('else');
								console.log(counter + phpSession.length + 1);
								}

								}
							 else {
								Session.length;
								var table1 = document.getElementById("table1");
								var row1 = table1.insertRow(counter + Session.length + 1);
								console.log(counter + Session.length + 1);
							}
							

							// if(phpSession.length > 0 ){

							// }

							var cell1 = row1.insertCell(-1);
							var cell2 = row1.insertCell(-1);
							var cell3 = row1.insertCell(-1);
							var cell4 = row1.insertCell(-1);
							var cell5 = row1.insertCell(-1);

							var HTML1 = '<input type="date" name="date1[]" value="' + session[counter] + '" maxlength="20" />';
							var HTML2 = '<input type="text" name="houmon1[]" value="' + session[counter] + '" maxlength="20" />';
							var HTML3 = '<input type="text" name="keiro1[]" value="" size="10" maxlength="20" /> <select name="check1[]" style="width: 50px; padding: 1px;"><option value=""><option value = 2>⇔</option><option value = 1>→</option> </option></select> <input type="text" name="keiros1[]" value="" size="10" maxlength="20" />';
							var HTML4 = '<input type="text" name="kingaku1[]" value="" size="10" maxlength="20" />';
							var HTML5 = '';

							// document.getElementById('dates').value = phpSession;

							cell1.innerHTML = HTML1;
							cell2.innerHTML = HTML2;
							cell3.innerHTML = HTML3;
							cell4.innerHTML = HTML4;
							cell5.innerHTML = HTML5;
							// カウンタを回す

							counter++;
							// document.write(counter);
						}
					</script>

					<?php
// var_dump(count($_SESSION['houmon']));
$_SESSION['hyouji'] = $_POST['hyouji'];
 if($_POST['hyouji'] == true){
	header('Location: unset.php');
 }

$count = count($rec);
if($count == null){
	if(isset($_SESSION['houmon1'])){
		$count = 1 + count($_SESSION['houmon1'] + $_SESSION['houmon']);
	}elseif(isset($_SESSION['houmon'])){
		$count = count($_SESSION['houmon']);
	}
	else{
		$count = 1;
	}

}
else{
	if(isset($_SESSION['houmon1'])){
		print 'if';
		$count = $count + count($_SESSION['houmon1']);
}elseif(isset($_SESSION['houmon'])){
	// print 'elseif';
	$count = count($_SESSION['houmon']);
}
	}

	$_SESSION['hyouji'] = $_POST['hyouji'];
	if(isset($_SESSION['hyouji'])){
		if(count($visit) > 0){
			$count = count($visit);
		}
		else{
			$count = count($visit) + 1;
		}
	}



/////////////////////////////////////////////////////////////////input type s///////////////////////////////////////////////////////////////////////////
$X = count($rec) + count($_SESSION['houmon']);
if($X > 9 || isset($_SESSION['worklocation'])){
for ($i = 0; $i < $count ; $i++){
if(isset($_SESSION['date'][$i])){
	$date = substr($_SESSION['date'][$i], 0,7); 
}
?>

					<!-- DBの作成日の非表示データを定義-->
					<input type="hidden" name="sno[]"
						value="<?php print $rec['s_No']?>">

					<input type="hidden" name="No[]" value="<?php print $i?>">

					<!-- <td>
<?php 
// print $i+1 
?>
</td> -->
					<!-- ////////////////////////////////////////////////////////精算年月//////////////////////////////////////////////////////////////////// -->
					<!-- $_SESSION['worklocation']が空の時の処理 -->
					<?php if(empty($_SESSION['worklocation'])){
	if($_SESSION['err'] == 2){
		?>
					<td><input type="date"
						value="<?php print $year_and_month[$i]?>" name="date[]"
						maxlength="20"></td>
					<?php
	}
	else{
		if(empty($_SESSION['date'][$i])){
			if($_SESSION['date'][$i] == "" && $_SESSION['houmon'][$i] == "" && $_SESSION['keiro'][$i] == "" && $_SESSION['check'][$i] == "" && $_SESSION['keiros'][$i] == "" && $_SESSION['kingaku'][$i] == ""){
			?>
					<td><input type="date"
						value="<?php print $_SESSION['date'][$i]?>" name="date[]"
						maxlength="20"></td>
					<?php
		}
		else{?>
					<td><input type="date"
						value="<?php print $_SESSION['date'][$i]?>" name="date[]"
						maxlength="20"
						style="background-color: #FADBDA; border-color: red;"></td>
					<?php
		}
	}
	elseif($month !== $date){?>
					<td><input type="date"
						value="<?php print $_SESSION['date'][$i]?>" name="date[]"
						maxlength="20"
						style="background-color: #FADBDA; border-color: red;"></td>
					<?php 
	}
	else{?>
					<td><input type="date"
						value="<?php print $_SESSION['date'][$i]?>" name="date[]"
						maxlength="20"></td>
					<?php
	}


		}
			}
			?>
					<!-- $_SESSION['worklocation']が入っている時の処理 -->
					<?php if(isset($_SESSION['worklocation'])){?>
					<td><div align="center">
							<?php
		
		print substr($year_and_month[$i], 5,-3)
		.'/'
		. substr($year_and_month[$i], 8)
		?>
						</div></td>
					<?php }?>

					<!-- /////////////////////////////////////////////////////////////////////訪問先///////////////////////////////////////////////////////////////// -->
					<!-- $_SESSION['worklocation']が空の時の処理 -->
					<?php if(empty($_SESSION['worklocation'])){	
		if($_SESSION['err'] == 2){
		?>
					<td><input type="text"
						value="<?php if(isset($visit[$i])){print $visit[$i];} ?>"
						name="houmon[]" maxlength="20"></td>
					<?php }
		else{
			if(empty($_SESSION['houmon'][$i])){
				if($_SESSION['date'][$i] == "" && $_SESSION['houmon'][$i] == "" && $_SESSION['keiro'][$i] == "" && $_SESSION['check'][$i] == "" && $_SESSION['keiros'][$i] == "" && $_SESSION['kingaku'][$i] == ""){
				?>
					<td><input type="text"
						value="<?php print $_SESSION['houmon'][$i]?>" name="houmon[]"
						maxlength="20"></td>
					<?php
			}
			else{?>
					<td><input type="text"
						value="<?php print $_SESSION['houmon'][$i]?>" name="houmon[]"
						maxlength="20"
						style="background-color: #FADBDA; border-color: red;"></td>
					<?php
			}
		}
		else{?>
					<td><input type="text"
						value="<?php print $_SESSION['houmon'][$i]?>" name="houmon[]"
						maxlength="20"></td>
					<?php
		}
			}
				}?>


					<?php if(isset($_SESSION['worklocation'])){?>
					<td><div align="center">
							<?php 
		print $visit[$i]
		?>
						</div></td>
					<?php }?>


					<!-- ///////////////////////////////////////////////////////////////経路//////////////////////////////////////////////////////////////////////// -->
					<?php if(empty($_SESSION['worklocation'])){
		if($_SESSION['err'] == 2){?>
					<td><input type="text"
						value="<?php if(isset($Point_of_departure[$i])){print $Point_of_departure[$i];}?>"
						name="keiro[]" size="10" maxlength="20"> <?php } 
else{
	if(empty($_SESSION['keiro'][$i])){
		if($_SESSION['date'][$i] == "" && $_SESSION['houmon'][$i] == "" && $_SESSION['keiro'][$i] == "" && $_SESSION['check'][$i] == "" && $_SESSION['keiros'][$i] == "" && $_SESSION['kingaku'][$i] == ""){
		?>
					<td><input type="text"
						value="<?php print $_SESSION['keiro'][$i]?>" name="keiro[]"
						size="10" maxlength="20"> <?php
	}
	else{?>
					<td><input type="text"
						value="<?php print $_SESSION['keiro'][$i]?>" name="keiro[]"
						size="10" maxlength="20"
						style="background-color: #FADBDA; border-color: red;"> <?php
	}
}
else{?>
					<td><input type="text"
						value="<?php print $_SESSION['keiro'][$i]?>" name="keiro[]"
						size="10" maxlength="20"> <?php
}
	}
		}
		
?> <?php	 ?> <!-- ///////////////////////////////////////////////////////出発地///////////////////////////////////////////////////	 -->
						<?php 
if(empty($_SESSION['worklocation'])){
	if($_SESSION['err'] == 2){?> <select name="check[]"
						style="width: 50px; padding: 1px;">
							<option value=""></option>
							<?php
		if($Checkout_flag[$i] == 2){
	print '<option value = 2 selected>⇔</option>
	<option value = 1>→</option>';
		}elseif($Checkout_flag[$i] == 1){
	print '<option value = 2 >⇔</option>
	<option value = 1 selected>→</option>';
		}else{
			print '<option value = 2>⇔</option>
			<option value = 1>→</option>';
		}
	print '</select>'; 
}
else{
	if(empty($_SESSION['check'][$i])){
		if($_SESSION['date'][$i] == "" && $_SESSION['houmon'][$i] == "" && $_SESSION['keiro'][$i] == "" && $_SESSION['check'][$i] == "" && $_SESSION['keiros'][$i] == "" && $_SESSION['kingaku'][$i] == ""){
		?>
							<select name="check[]" style="width: 50px; padding: 1px;">
								<option value=""></option>
								<?php
			if($_SESSION['check'][$i] == 2){
		print '<option value = 2 selected>⇔</option>
		<option value = 1>→</option>';
			}elseif($_SESSION['check'][$i] == 1){
		print '<option value = 2 >⇔</option>
		<option value = 1 selected>→</option>';
			}else{
				print '<option value = 2>⇔</option>
				<option value = 1>→</option>';
			}
		print'</select>'; 
		}else{
			?>
								<select name="check[]"
								style="width: 50px; padding: 1px; background-color: #FADBDA; border-color: red; border: 2px solid red">
									<option value="" style="background-color: white"></option>
									<?php
			if($_SESSION['check'][$i] == 2){
		print '<option value = 2 selected style="background-color:white">⇔</option>
		<option value = 1 style="background-color:white">→</option>';
			}elseif($_SESSION['check'][$i] == 1){
		print '<option value = 2 style="background-color:white">⇔</option>
		<option value = 1 selected style="background-color:white">→</option>';
			}else{
				print '<option value = 2 style="background-color:white">⇔</option>
				<option value = 1 style="background-color:white">→</option>';
			}
		print'</select>';
		}
	}
	else{
		?>
									<select name="check[]" style="width: 50px; padding: 1px;">
										<option value=""></option>
										<?php
		if($_SESSION['check'][$i] == 2){
	print '<option value = 2 selected>⇔</option>
	<option value = 1>→</option>';
		}elseif($_SESSION['check'][$i] == 1){
	print '<option value = 2 >⇔</option>
	<option value = 1 selected>→</option>';
		}else{
			print '<option value = 2>⇔</option>
			<option value = 1>→</option>';
		}
	print'</select>'; 
	}
	} 
}
?>
										<!-- //印刷 -->
										<?php if(isset($_SESSION['worklocation'])){?>
										<td nowrap><div align="center">
												<?php print $Point_of_departure[$i];?>
												<?php
		if($Checkout_flag[$i] == 2){
	print '⇔';
		}elseif($Checkout_flag[$i] == 1){
	print '→';
		}
?>
												<?php print $Point_of_Arrival[$i]?>
											</div></td>
										<?php }?>
										<!-- ///////////////////////////////////////////////////////////到着地//////////////////////////////////////////////////////// -->
										<?php if(empty($_SESSION['worklocation'])){
		if($_SESSION['err'] == 2){?>
										<input type="text"
										value="<?php if(isset($Point_of_Arrival[$i])){ print $Point_of_Arrival[$i]; } ?>"
										name="keiros[]" size="10" maxlength="20">
										<?php } 
else{
	if(empty($_SESSION['keiros'][$i])){
		if($_SESSION['date'][$i] == "" && $_SESSION['houmon'][$i] == "" && $_SESSION['keiro'][$i] == "" && $_SESSION['check'][$i] == "" && $_SESSION['keiros'][$i] == "" && $_SESSION['kingaku'][$i] == ""){
		?>
										<input type="text"
										value="<?php print $_SESSION['keiros'][$i]?>" name="keiros[]"
										size="10" maxlength="20">
										<?php
	}
	else{?>
										<input type="text"
										value="<?php print $_SESSION['keiros'][$i]?>" name="keiros[]"
										size="10" maxlength="20"
										style="background-color: #FADBDA; border-color: red;">
										<?php
	}
}
else{?>
										<input type="text"
										value="<?php print $_SESSION['keiros'][$i]?>" name="keiros[]"
										size="10" maxlength="20">
										<?php
}
	}
		}
?></td>
					<!-- ////////////////////////////////////////////////////////金額/////////////////////////////////////////////////// -->
					<?php $en = '￥';?>
					<?php if(empty($_SESSION['worklocation'])){
		if($_SESSION['err'] == 2){?>
					<td><input type="text"
						value="<?php if(isset($Settlement_amount[$i])){print $Settlement_amount[$i];} ?>"
						name="kingaku[]" size="10" maxlength="20"></td>
					<?php } 
else{
	if(empty($_SESSION['kingaku'][$i])){
		if($_SESSION['date'][$i] == "" && $_SESSION['houmon'][$i] == "" && $_SESSION['keiro'][$i] == "" && $_SESSION['check'][$i] == "" && $_SESSION['keiros'][$i] == "" && $_SESSION['kingaku'][$i] == ""){
		?>
					<td><input type="text"
						value="<?php print $_SESSION['kingaku'][$i]?>" name="kingaku[]"
						size="10" maxlength="20"></td>
					<?php
	}
	else{?>
					<td><input type="text"
						value="<?php print $_SESSION['kingaku'][$i]?>" name="kingaku[]"
						size="10" maxlength="20"
						style="background-color: #FADBDA; border-color: red;"></td>
					<?php
	}
}
elseif(preg_match("/^[0-9]+$/", $_SESSION['kingaku'][$i])==false) {
	?>
					<td><input type="text"
						value="<?php print $_SESSION['kingaku'][$i]?>" name="kingaku[]"
						size="10" maxlength="20"
						style="background-color: #FADBDA; border-color: red;"></td>
					<?php

}
else{?>
					<td><input type="text"
						value="<?php print $_SESSION['kingaku'][$i]?>" name="kingaku[]"
						size="10" maxlength="20"></td>
					<?php
}
	}
		}
		 ?>

					<?php if(isset($_SESSION['worklocation'])){?>
					<td><div align="right">
							<?php  print  $en . $Settlement_amount[$i]?>

						</div></td>
					<?php } ?>

					<!-- //チェックボックス -->
					<?php if(empty($_SESSION['worklocation'])){
			if(isset($year_and_month[$i])){
		?>
					<td><input type="checkbox" name="sentaku[]"
						value="<?php print $i?>"></td>
					<?php 
			}else
			{
				print'<td>&nbsp;&nbsp;&nbsp;</td>';
			}
		}
?>
					<?php
	print '</tr>';
	print '</tbody>';
?>

					<?php
}
	}else{

		for ($i = 0; $i < 9 ; $i++){
			if(isset($_SESSION['date'][$i])){
				$date = substr($_SESSION['date'][$i], 0,7); 
			}
			?>
			
								<!-- DBの作成日の非表示データを定義-->
								<input type="hidden" name="sno[]"
									value="<?php print $rec['s_No']?>">
			
								<input type="hidden" name="No[]" value="<?php print $i?>">
			
								<!-- <td>
			<?php 
			// print $i+1 
			?>
			</td> -->
								<!-- ////////////////////////////////////////////////////////精算年月//////////////////////////////////////////////////////////////////// -->
								<!-- $_SESSION['worklocation']が空の時の処理 -->
								<?php if(empty($_SESSION['worklocation'])){
				if($_SESSION['err'] == 2){
					?>
								<td><input type="date"
									value="<?php print $year_and_month[$i]?>" name="date[]"
									maxlength="20"></td>
								<?php
				}
				else{
					if(empty($_SESSION['date'][$i])){
						if($_SESSION['date'][$i] == "" && $_SESSION['houmon'][$i] == "" && $_SESSION['keiro'][$i] == "" && $_SESSION['check'][$i] == "" && $_SESSION['keiros'][$i] == "" && $_SESSION['kingaku'][$i] == ""){
						?>
								<td><input type="date"
									value="<?php print $_SESSION['date'][$i]?>" name="date[]"
									maxlength="20"></td>
								<?php
					}
					else{?>
								<td><input type="date"
									value="<?php print $_SESSION['date'][$i]?>" name="date[]"
									maxlength="20"
									style="background-color: #FADBDA; border-color: red;"></td>
								<?php
					}
				}
				elseif($month !== $date){?>
								<td><input type="date"
									value="<?php print $_SESSION['date'][$i]?>" name="date[]"
									maxlength="20"
									style="background-color: #FADBDA; border-color: red;"></td>
								<?php 
				}
				else{?>
								<td><input type="date"
									value="<?php print $_SESSION['date'][$i]?>" name="date[]"
									maxlength="20"></td>
								<?php
				}
			
			
					}
						}
						?>
								<!-- $_SESSION['worklocation']が入っている時の処理 -->
								<?php if(isset($_SESSION['worklocation'])){?>
								<td><div align="center">
										<?php
					
					print substr($year_and_month[$i], 5,-3)
					.'/'
					. substr($year_and_month[$i], 8)
					?>
									</div></td>
								<?php }?>
			
								<!-- /////////////////////////////////////////////////////////////////////訪問先///////////////////////////////////////////////////////////////// -->
								<!-- $_SESSION['worklocation']が空の時の処理 -->
								<?php if(empty($_SESSION['worklocation'])){	
					if($_SESSION['err'] == 2){
					?>
								<td><input type="text"
									value="<?php if(isset($visit[$i])){print $visit[$i];} ?>"
									name="houmon[]" maxlength="20"></td>
								<?php }
					else{
						if(empty($_SESSION['houmon'][$i])){
							if($_SESSION['date'][$i] == "" && $_SESSION['houmon'][$i] == "" && $_SESSION['keiro'][$i] == "" && $_SESSION['check'][$i] == "" && $_SESSION['keiros'][$i] == "" && $_SESSION['kingaku'][$i] == ""){
							?>
								<td><input type="text"
									value="<?php print $_SESSION['houmon'][$i]?>" name="houmon[]"
									maxlength="20"></td>
								<?php
						}
						else{?>
								<td><input type="text"
									value="<?php print $_SESSION['houmon'][$i]?>" name="houmon[]"
									maxlength="20"
									style="background-color: #FADBDA; border-color: red;"></td>
								<?php
						}
					}
					else{?>
								<td><input type="text"
									value="<?php print $_SESSION['houmon'][$i]?>" name="houmon[]"
									maxlength="20"></td>
								<?php
					}
						}
							}?>
			
			
								<?php if(isset($_SESSION['worklocation'])){?>
								<td><div align="center">
										<?php 
					print $visit[$i]
					?>
									</div></td>
								<?php }?>
			
			
								<!-- ///////////////////////////////////////////////////////////////経路//////////////////////////////////////////////////////////////////////// -->
								<?php if(empty($_SESSION['worklocation'])){
					if($_SESSION['err'] == 2){?>
								<td><input type="text"
									value="<?php if(isset($Point_of_departure[$i])){print $Point_of_departure[$i];}?>"
									name="keiro[]" size="10" maxlength="20"> <?php } 
			else{
				if(empty($_SESSION['keiro'][$i])){
					if($_SESSION['date'][$i] == "" && $_SESSION['houmon'][$i] == "" && $_SESSION['keiro'][$i] == "" && $_SESSION['check'][$i] == "" && $_SESSION['keiros'][$i] == "" && $_SESSION['kingaku'][$i] == ""){
					?>
								<td><input type="text"
									value="<?php print $_SESSION['keiro'][$i]?>" name="keiro[]"
									size="10" maxlength="20"> <?php
				}
				else{?>
								<td><input type="text"
									value="<?php print $_SESSION['keiro'][$i]?>" name="keiro[]"
									size="10" maxlength="20"
									style="background-color: #FADBDA; border-color: red;"> <?php
				}
			}
			else{?>
								<td><input type="text"
									value="<?php print $_SESSION['keiro'][$i]?>" name="keiro[]"
									size="10" maxlength="20"> <?php
			}
				}
					}
					
			?> <?php	 ?> <!-- ///////////////////////////////////////////////////////出発地///////////////////////////////////////////////////	 -->
									<?php 
			if(empty($_SESSION['worklocation'])){
				if($_SESSION['err'] == 2){?> <select name="check[]"
									style="width: 50px; padding: 1px;">
										<option value=""></option>
										<?php
					if($Checkout_flag[$i] == 2){
				print '<option value = 2 selected>⇔</option>
				<option value = 1>→</option>';
					}elseif($Checkout_flag[$i] == 1){
				print '<option value = 2 >⇔</option>
				<option value = 1 selected>→</option>';
					}else{
						print '<option value = 2>⇔</option>
						<option value = 1>→</option>';
					}
				print '</select>'; 
			}
			else{
				if(empty($_SESSION['check'][$i])){
					if($_SESSION['date'][$i] == "" && $_SESSION['houmon'][$i] == "" && $_SESSION['keiro'][$i] == "" && $_SESSION['check'][$i] == "" && $_SESSION['keiros'][$i] == "" && $_SESSION['kingaku'][$i] == ""){
					?>
										<select name="check[]" style="width: 50px; padding: 1px;">
											<option value=""></option>
											<?php
						if($_SESSION['check'][$i] == 2){
					print '<option value = 2 selected>⇔</option>
					<option value = 1>→</option>';
						}elseif($_SESSION['check'][$i] == 1){
					print '<option value = 2 >⇔</option>
					<option value = 1 selected>→</option>';
						}else{
							print '<option value = 2>⇔</option>
							<option value = 1>→</option>';
						}
					print'</select>'; 
					}else{
						?>
											<select name="check[]"
											style="width: 50px; padding: 1px; background-color: #FADBDA; border-color: red; border: 2px solid red">
												<option value="" style="background-color: white"></option>
												<?php
						if($_SESSION['check'][$i] == 2){
					print '<option value = 2 selected style="background-color:white">⇔</option>
					<option value = 1 style="background-color:white">→</option>';
						}elseif($_SESSION['check'][$i] == 1){
					print '<option value = 2 style="background-color:white">⇔</option>
					<option value = 1 selected style="background-color:white">→</option>';
						}else{
							print '<option value = 2 style="background-color:white">⇔</option>
							<option value = 1 style="background-color:white">→</option>';
						}
					print'</select>';
					}
				}
				else{
					?>
												<select name="check[]" style="width: 50px; padding: 1px;">
													<option value=""></option>
													<?php
					if($_SESSION['check'][$i] == 2){
				print '<option value = 2 selected>⇔</option>
				<option value = 1>→</option>';
					}elseif($_SESSION['check'][$i] == 1){
				print '<option value = 2 >⇔</option>
				<option value = 1 selected>→</option>';
					}else{
						print '<option value = 2>⇔</option>
						<option value = 1>→</option>';
					}
				print'</select>'; 
				}
				} 
			}
			?>
													<!-- //印刷 -->
													<?php if(isset($_SESSION['worklocation'])){?>
													<td nowrap><div align="center">
															<?php print $Point_of_departure[$i];?>
															<?php
					if($Checkout_flag[$i] == 2){
				print '⇔';
					}elseif($Checkout_flag[$i] == 1){
				print '→';
					}
			?>
															<?php print $Point_of_Arrival[$i]?>
														</div></td>
													<?php }?>
													<!-- ///////////////////////////////////////////////////////////到着地//////////////////////////////////////////////////////// -->
													<?php if(empty($_SESSION['worklocation'])){
					if($_SESSION['err'] == 2){?>
													<input type="text"
													value="<?php if(isset($Point_of_Arrival[$i])){ print $Point_of_Arrival[$i]; } ?>"
													name="keiros[]" size="10" maxlength="20">
													<?php } 
			else{
				if(empty($_SESSION['keiros'][$i])){
					if($_SESSION['date'][$i] == "" && $_SESSION['houmon'][$i] == "" && $_SESSION['keiro'][$i] == "" && $_SESSION['check'][$i] == "" && $_SESSION['keiros'][$i] == "" && $_SESSION['kingaku'][$i] == ""){
					?>
													<input type="text"
													value="<?php print $_SESSION['keiros'][$i]?>" name="keiros[]"
													size="10" maxlength="20">
													<?php
				}
				else{?>
													<input type="text"
													value="<?php print $_SESSION['keiros'][$i]?>" name="keiros[]"
													size="10" maxlength="20"
													style="background-color: #FADBDA; border-color: red;">
													<?php
				}
			}
			else{?>
													<input type="text"
													value="<?php print $_SESSION['keiros'][$i]?>" name="keiros[]"
													size="10" maxlength="20">
													<?php
			}
				}
					}
			?></td>
								<!-- ////////////////////////////////////////////////////////金額/////////////////////////////////////////////////// -->
								<?php $en = '￥';?>
								<?php if(empty($_SESSION['worklocation'])){
					if($_SESSION['err'] == 2){?>
								<td><input type="text"
									value="<?php if(isset($Settlement_amount[$i])){print $Settlement_amount[$i];} ?>"
									name="kingaku[]" size="10" maxlength="20"></td>
								<?php } 
			else{
				if(empty($_SESSION['kingaku'][$i])){
					if($_SESSION['date'][$i] == "" && $_SESSION['houmon'][$i] == "" && $_SESSION['keiro'][$i] == "" && $_SESSION['check'][$i] == "" && $_SESSION['keiros'][$i] == "" && $_SESSION['kingaku'][$i] == ""){
					?>
								<td><input type="text"
									value="<?php print $_SESSION['kingaku'][$i]?>" name="kingaku[]"
									size="10" maxlength="20"></td>
								<?php
				}
				else{?>
								<td><input type="text"
									value="<?php print $_SESSION['kingaku'][$i]?>" name="kingaku[]"
									size="10" maxlength="20"
									style="background-color: #FADBDA; border-color: red;"></td>
								<?php
				}
			}
			elseif(preg_match("/^[0-9]+$/", $_SESSION['kingaku'][$i])==false) {
				?>
								<td><input type="text"
									value="<?php print $_SESSION['kingaku'][$i]?>" name="kingaku[]"
									size="10" maxlength="20"
									style="background-color: #FADBDA; border-color: red;"></td>
								<?php
			
			}
			else{?>
								<td><input type="text"
									value="<?php print $_SESSION['kingaku'][$i]?>" name="kingaku[]"
									size="10" maxlength="20"></td>
								<?php
			}
				}
					}
					 ?>
			
								<?php if(isset($_SESSION['worklocation'])){?>
								<td><div align="right">
										<?php  print  $en . $Settlement_amount[$i]?>
			
									</div></td>
								<?php } ?>
			
								<!-- //チェックボックス -->
								<?php if(empty($_SESSION['worklocation'])){
						if(isset($year_and_month[$i])){
					?>
								<td><input type="checkbox" name="sentaku[]"
									value="<?php print $i?>"></td>
								<?php 
						}else
						{
							print'<td>&nbsp;&nbsp;&nbsp;</td>';
						}
					}
			?>
								<?php
				print '</tr>';
				print '</tbody>';
			?>
			
								<?php
			}

	}?>
<?php/////////////////////////////////////////////////////////////////input type e///////////////////////////////////////////////////////////////////////////?>

<?php if(isset($_SESSION['worklocation'])){?>
	<tr>
					<!-- //月日   -->
					<td width="141"></td>
					<!-- //訪問先 -->
					<td width="177"></td>
					<!-- //経路 -->
					<td width="275"><div align="center">計</div></td>
					<!-- //金額 -->
					<td width="107"><div align="right">
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
</td>
</tr>
<?php } ?>





<?php if(empty($_SESSION['worklocation'])){?>
<?php if($rec == true){ ?>
</table>
<span class="tables">
<table border="1" cellspacing="0" cellpadding="5"
					bordercolor="#333333" id="table1"c>
<tr>
					<!-- //月日   -->
					<td width="141"></td>
					<!-- //訪問先 -->
					<td width="177"></td>
					<!-- //経路 -->
					<td width="275"><div align="center">計</div></td>
					<!-- //金額 -->
					<td width="107"><div align="right">
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
<td width="19"></td>
</td>
</table>
</span>
<?php }

elseif($rec == false){
	print '</table>';
	print'<span class="tables">
	<table border="1" cellspacing="0" cellpadding="5"
						bordercolor="#333333" id="table1"c>
	<tr>
						<!-- //月日   -->
						<td width="141"></td>
						<!-- //訪問先 -->
						<td width="177"></td>
						<!-- //経路 -->
						<td width="275"><div align="center">計</div></td>
						<!-- //金額 -->
						<td width="107"><div align="right">';
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
	<td width="16"></td>
	</td>

	</table>
	</span>
<?php } 
}
?>

				<span class="sample2"> <?php

				 if(empty($_SESSION['worklocation'])){
					?> <input type="submit"
					style="background-color: #87cefa; width: 200px; padding: 8px;"
					value="戻る" formaction="switch.php">
				</span> <span class="sample3"> <input type="submit" name="save"
					style="background-color: #87cefa; width: 200px; padding: 8px;"
					value="保存" formaction="seisansho_check.php"></span> <span
					class="sample10"><input type="submit"
					style="background-color: #87cefa; width: 200px; padding: 8px;"
					value="管理者へ提出" formaction="admin_submission.php"> </span>
				<?php
				if(isset($year_and_month)==false){?>
				<span class="sample4"> <input type="submit" name="delete"
					style="background-color: #87cefa; width: 200px; padding: 8px;"
					value="削除" formaction="seisansho_check.php" disabled>
				</span>
				<?php
				}else
				{?></span>
	<span class="sample4"> <input type="submit" name="delete"
		style="background-color: #87cefa; width: 200px; padding: 8px;"
		value="削除" formaction="seisansho_check.php">
	</span>
	<?php
				}
				 }
				?>
	</form> 

	<form method="post" action="/kinmu/seisansho.php">
	<!-- 月を選択するセレクトボックス -->
	<?php if(empty($_SESSION['worklocation'])){?>
	<span class="sample7"> <nobr>
			<?php print '表示する月:';?>
			<select name="select1">
		</nobr> <?php 
					print '<option value=""></option>';


  for ($i = 0; $i <=6 ; $i++) {
   if(isset($month)){
       $selected=(date("Y-m",strtotime(date('Y-m-01')."-$i month"))==$month ?" selected":"");
   }
    print "<option value=\"".date("Y-m",strtotime(date('Y-m-01')."-$i month"))."\"{$selected}>".date("Y-m",strtotime(date('Y-m-01')."-$i month"))."</option>";
 }
 
?> <?php if(isset($_SESSION['errmsg1']) || isset($_SESSION['errmsg2']) || isset($_SESSION['errmsg3']) || isset($_SESSION['errmsg4']) || isset($_SESSION['errmsg5']) || isset($_SESSION['errmsg6'])){ ?>
		<input type="submit" name="hyouji" value="表示" disabled> <?php 
	}else{?> <input type="submit" name="hyouji" value="表示">
	</form>
	<?php
	}
		} 
		
// DB切断
		$dbh = null;
	?>

		</div>

		<table style="border: none" cellspacing="0" cellpadding="1"
			bordercolor="#333333" " align="left">
			<!-- <tr>
				<td width="60px" align="center">凡例</td>
			</tr>
			<tr>
				<td width="60px" align="center">往復</td>
				<td width="60px" align="center" style="border: none">⇔</td>
			</tr>
			<tr>
				<td width="60px" align="center">片道</td>
				<td width="60px" align="center" style="border: none">→</td>
			</tr> -->
		</table>
		
		
		<?php

} catch (PDOException $e) {
	print 'システムエラーが発生しました';
	exit();
}
//印刷ボタンの変数をクリア
if(isset($_SESSION['worklocation'])){
	unset($_SESSION['worklocation']);
 }
?>
</body>
</html>
<?php

unset($_SESSION['errmsg1']);
unset($_SESSION['errmsg2']);
unset($_SESSION['errmsg3']);
unset($_SESSION['errmsg4']);
unset($_SESSION['errmsg5']);
unset($_SESSION['errmsg6']);
unset($_SESSION['errmsg7']);
?>
<?php
	//改ページする処理
// if(count($rec) >= 15 && count($rec) < 23){
// 	print'<div style="page-break-after: always;"></div>';
// 	}
// 	if(count($rec) >= 44 && count($rec) <= 50){
// 		print'<div style="page-break-after: always;"></div>';
// 	}

?>