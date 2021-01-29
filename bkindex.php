<?php
//セッションが開始されていなければセッションを開始する。
if(!isset($_SESSION)){
	session_start();
	session_regenerate_id(true);
}
//ログイン情報がなければTOPページに返す
if (isset($_SESSION["login"])==false)
{
	header("Location: staff_login.php");
	exit();
}
//DB接続クラスを読む
require_once("kinmu_common.php");
//社員一覧画面に返す処理（URLを調整する）
if($_SERVER['HTTP_REFERER']!="http://localhost:8080/kinmuhyo/list_of_members.php"){
	$_SERVER['HTTP_REFERER']="http://localhost:8080/kinmuhyo/list_of_members.php";
}
// if($_SERVER['HTTP_REFERER']!="https://www.pros-service.co.jp/kinmu/list_of_members.php"){
// 	$_SERVER['HTTP_REFERER']="https://www.pros-service.co.jp/kinmu/list_of_members.php";
// }
$url = $_SERVER['HTTP_REFERER']."?page_id=".$_SESSION["id"]."&".urlencode(urlencode("ステータス1"))."=".$_SESSION["status"][0]."&".urlencode(urlencode("ステータス2"))."=".$_SESSION["status"][1]."&".urlencode(urlencode("ステータス3"))."=".$_SESSION["status"][2]."&".urlencode(urlencode("ステータス4"))."=".$_SESSION["status"][3]."&".urlencode(urlencode("ステータス5"))."=".$_SESSION["status"][4];
//methodがPOSTの場合代入
if($_SERVER["REQUEST_METHOD"] === "POST"){
	//社員が選択されていなかったら画面遷移無し。
	if (isset($_POST['hensyuu'])) {
		if(isset($_POST['staffcode'])==false){
			$_SESSION["print_err"] = "社員を選択してください。";
			header("Location: $url");
		}else{
			// list_of_membersのラジオボタンの値を$_SESSION['staffcode']に代入
				$staff_number = $_POST['staffcode'];
		}
		// 社員情報編集ボタン押下時$_POST['hensyuu']を$_SESSION['hensyuu']に代入
		$_SESSION['hensyuu'] = $_POST['hensyuu'];
	}

	// 新規ボタン押下時、$_POST['newRegister']を$_SESSION['newRegister']に代入
	if (isset($_POST['newRegister'])) {
		$_SESSION['newRegister'] = $_POST['newRegister'];
	}
}else{
	//methodがPOST以外のの場合代入
	$_POST = $_SESSION["post"];
	$err = $_SESSION["err"];
}
try {
	// //////////////////データベースの読込 S//////////////////////
	$dbh = db_connect();
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	if (isset($_POST['hensyuu'])) {
		// TBL_STAFFの値を全て取得
		$staffsql = 'select * FROM TBL_STAFF WHERE staff_number=:staff_number';
		$staffstmt=$dbh->prepare($staffsql);
		$staffstmt->bindValue(":staff_number",$staff_number,PDO::PARAM_STR);
		$staffstmt->execute();
		$staffrec = $staffstmt->fetch(PDO::FETCH_ASSOC);

		$family = $staffrec["familyname"];
		$first = $staffrec["firstname"];
		$familykana = $staffrec["familyname_kana"];
		$firstkana = $staffrec["firstname_kana"];
		$email = $staffrec["email"];
		$hire = $staffrec["hire_date"];
		$retirement = $staffrec["retirement_date"];
		$holiday = $staffrec["holiday_with_pay"];
		$admin_flag = $staffrec["admin_flag"];
		$new_work_id = $staffrec["new_work_id"];
		$new_start_month = $staffrec["new_start_month"];
		$old_work_id = $staffrec["old_work_id"];
		$old_start_month = $staffrec["old_start_month"];
		$old_end_month = $staffrec["old_end_month"];
	}elseif(isset($_POST['add']) || isset($_POST['change'])){
		$staff_number = $_POST["staff_number"];
		$family = $_POST["familyname"];
		$first = $_POST["firstname"];
		$familykana = $_POST["familyname_kana"];
		$firstkana = $_POST["firstname_kana"];
		$email = $_POST["email"];
		$hire = $_POST["hire_date"];
		if(isset($_POST['change'])){
			$retirement = $_POST["retirement_date"];
		}
		$holiday = $_POST["holiday_with_pay"];
		$admin_flag = $_POST["admin_flag"];
		$new_work_id = $_POST["new_work_id"];
		$new_start_month = $_POST["new_start_month"];
		$old_work_id = $_POST["old_work_id"];
		$old_start_month = $_POST["old_start_month"];
		$old_end_month = $_POST["old_end_month"];
	}
	// tbl_belongssの値を全て取得

	$belongsssql = 'select * FROM  TBL_BELONGSS';
	$belongssstmt = $dbh->prepare($belongsssql);
	$belongssstmt->execute();


	// tbl_belongssの値を全て取得

	$belongsssql2 = 'select * FROM  TBL_BELONGSS';
	$belongssstmt2 = $dbh->prepare($belongsssql2);
	$belongssstmt2->execute();

	// DB切断
	$dbh = null;
} catch (PDOException $e) {
	header('Location: err_report.php');
	exit();
}
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>社員情報編集画面</title>
</head>

<body>

	<div align="center">
		<form method="post" enctype="multipart/form-data">
			<h3>社員情報編集</h3>

			<span class="img_pro">
				<img src="https://www.pros-service.co.jp/img/image_2020_4_10.png"
				alt="画像のサンプル" width="20%" height="20%">
			</span>
			<table>
				<tr>
					<td><span class="staff_number"> <label><strong><span style="color: red;">※</span>社員番号</strong>
					</label>
				</span>
				 <?php
				// エラー表示停止
				error_reporting(8192);

				print '<span class="staff_number1">';
				if (isset($_POST['hensyuu']) || isset($_POST['change'])) {
					print $staff_number;
					print '<input type="hidden" name="staff_number" value="'.$staff_number.'">';
				}else{
					print '<input type="text" value="' . $staff_number . '" name="staff_number" maxlength="4"style="width: 250px; padding: 5px;">';
				}
				?>
			</span>
			<font color="red">
				<?php
				print '<span class="staff_number_err">';
				if(isset($err["staff_number"])){
					print $err["staff_number"];
				}
				print '</span>';
				?>
			</font>
		</td>
	</tr>
	<br>

	<tr>
		<td>
			<?php
			//姓
			print '<span class="fam_name"><label><strong><span style="color: red;">※</span>姓</strong></label>
			</span>';
			print '<span class="fam_name1">';
			print '<input type="text"maxlength="10" value="' . $family . '"name="familyname" style="width: 60px; padding: 5px;">';
			print '</span>';
			//名
			print '<span class="fir_name"> <label><strong><span style="color: red;">※</span>名</strong></span>
			<span class="fir_name1">
			<input type="text"maxlength="10"value="' . $first . '"name="firstname" style="width: 60px; padding: 5px;"><br>
			</span>';
			print '<font color="red"> <span class="name_err">';
			if(isset($err["family"])){
				print $err["family"];
			}
			if(isset($err["first"])){
				print $err["first"];
			}
			?>
		</span> <br>
	</td>
</tr>
</font>
<tr>
	<td><span class="fam_kana"><label><strong><span style="color: red;">※</span>姓(カナ)</strong></label>
	</span> <span class="fam_kana1"> <?php
	print '<input type="text"value="' . $familykana . '"maxlength="10" name="familyname_kana" style="width: 60px; padding: 5px;">';
	?>
</span> <span class="fir_kana"> <label><strong><span style="color: red;">※</span>名(カナ)</strong></label>
</span> <span class="fir_kana1"> <?php
print '<input type="text" value="' . $firstkana . '" name="firstname_kana"maxlength="10"style="width: 60px; padding: 5px;">';
?>
</span> <br>
<font color="red"> <span class="kana_err">
	<?php
	if(isset($err["family_kana"])){
		print $err["family_kana"];
	}

	if(isset($err["first_kana"])){
		print $err["first_kana"];
	}
	?>
</font>
</span> <br></td>
</tr>

<tr>
	<td><span class="email"><label><strong><span style="color: red;">※</span>メールアドレス</strong></label>
	</span> <span class="email1"> <?php
	print '<input type="text"id="email"value="' . $email . '"name="email" maxlength="30" style="width: 250px; padding: 5px;">';
	?>
</span> <br>
<font color="red">
	<?php
	print '<span class="email_err">';
	?> <?php
	if(isset($err["email"])){
		print $err["email"];
	}
	?>
</font>
<br>
</span>
</td>
</tr>
</span></td>
</tr>
<!-- 現在の勤務地ID -->
<font color="red">
	<?php
	print '<span class="new_id_err">';
	if(isset($err["new_work_id"])){
		print $err["new_work_id"];
	}?>
</span>
</font>
<tr>
	<td><span class="new_id"> <label><strong><span style="color: red;">※</span>現在の勤務地</strong></label>
	</span><?php
	print '<span class="new_id1">';
	print'<select style=" padding: 3px;"" name="new_work_id">';
	print '<option selected = "selected"></option>';
	while(true){
		$belongssrec = $belongssstmt->fetch(PDO::FETCH_ASSOC);

		if($belongssrec == false) {
			break;
		}
		$new_work_ids  = $belongssrec['work_id'];
		$new_work_name = $belongssrec['work_name'];
			$selected= $new_work_ids == $new_work_id ?" selected":"";
			print '<option value= "'.$new_work_ids."\"{$selected}>$new_work_name </option>'";
	}
	print '</span>';
	?>

	<!-- 開始日 -->
	<tr>
		<td><span class="new_start"> <label><strong><span style="color: red;">※</span>開始日</strong></label>
		</span> <span class="new_start1"> <?php
		print '<input type="date" value="' . $new_start_month . '"  name="new_start_month" style="width: 250px; padding:3px;">';
		?>
	</span></td>
</tr>
<font color="red">
	<?php
	print '<span class="new_start_err">';
	if(isset($err["new_start_month"])){
		print $err["new_start_month"];
	}
	?>
</span>
</font>
<!-- 1世代前の勤務地ID -->
<tr>
	<td>
		<?php
		print '<span class="old_id"> <label><strong>1世代前の勤務地</strong></label>
		</span>';
		?>
		<font color="red">
			<?
			print '<span class="old_id_err">';
			if(isset($err["old_work_id"])){
				print $err["old_work_id"];
			}
			?>
		</span>
		</font>

		<span class="old_id1"> <?php
		print'<select style=" padding: 3px;"" name="old_work_id">';
		print '<option value=""></option>';
		while(true){
			$belongssrec2 = $belongssstmt2->fetch(PDO::FETCH_ASSOC);
			if($belongssrec2 == false) {
				break;
			}
			$old_work_ids  = $belongssrec2['work_id'];
			$old_work_name = $belongssrec2['work_name'];
			if(!isset($_POST["newRegister"])){
			$selected= $old_work_ids == $old_work_id ?" selected":"";
		}else{
			$selected= "";
		}
			print '<option value= "'.$old_work_ids."\"{$selected}>$old_work_name </option>'";
		}
		print '</span>';
		?>
	</td>
</tr>

<!-- 開始日 -->
<tr>
	<td>
			<span class="old_start"> <label><strong>開始日</strong></label>
			</span>
		<span class="old_start1">
	<? print '<input type="date" value="' . $old_start_month . '"  name="old_start_month" style="width: 250px; padding: 3px;">';
		?>
	</span></td>
</tr>

<font color="red">
	<?php
	print '<span class="old_start_err">';
	if(isset($err["old_start_month"])){
		print $err["old_start_month"];
	}
	?>
</span>
</font>
<!-- 終了日 -->
<tr>
	<td>
		<span class="old_end"> <label><strong>終了日</strong></label>
		</span> <span class="old_end1"> <?php
		print '<input type="date"value="' . $old_end_month . '"  name="old_end_month" style="width: 250px; padding: 3px;">';
		?>
	</span>
</td>
</tr>
<font color="red">
	<?php
	print '<span class="old_end_err">';
	if(isset($err["old_end_month"])){
		print $err["old_end_month"];
	}
	print '</span>';
	?>
</font>

<tr>
	<td>
		<span class="hire"><label><strong><span style="color: red;">※</span>入社日</strong></label>
	</span> <span class="hire1"> <?php
		print '<input type="date" value="' . $hire . '"
		name="hire_date" style="width: 254px; padding: 3px;">';
		?>
	</span>
<font color="red">
	<?php
	print '<span class="hire_err">';
	if(isset($err["nyuusha"])){
		print $err["nyuusha"];
	}

	?></span> </font> <br></td>
</tr>

<tr>
	<td>
			<span class="retire"><label><strong><span style="color: red;">※</span>退職日</strong></label>
		</span> <span class="retire1">
			<?php
		if(isset($_POST['hensyuu']) || isset($_POST['change'])){
			print '<input type="date" value="' . $retirement . '"
			name="retirement_date" style="width: 254px; padding: 3px;">';
		}else{
			print "9999-12-31";
		}
		?>
	</span>
	<font color="red">
		<?php
		print '<span class="retire_err">';
		if(isset($err["taisha"])){
			print $err["taisha"];
		}
		?>
	</font>
</span>

</td>
</tr>

<tr>
	<td>
		<span class="holidyay"><label><strong><span style="color: red;">※</span>有給残日数</strong></label>
	</span> <span class="holidyay1"> <?php
		print '<input type="text" maxlength="4" value="' . $holiday . '"name="holiday_with_pay" style="width: 250px; padding: 5px;">';
		?>
	</span>
	<span class="holidyay_err"> <font color="red"> <?php
	if(isset($err["yuukyuu"])){
		print $err["yuukyuu"];
	}
	?>

</font>
</span></td>
</tr>
<?if(isset($_POST['hensyuu'])){?>
	<span style="position: absolute; top: 820px; left: 440px"><label><strong>有給休暇付与日数</strong></label>
		<span class="paid_grant" style="position: absolute; left: 150px">
			<select name="paid_grant" style="width: 50px; height: 30px;" >
				<?php
				$number = array (
					"",
					"3",
					"5",
					"11",
					"12",
					"14",
					"16",
					"18",
					"20",
				);
				foreach ($number as $value) {
					echo '<option value="', $value, '">', $value, '</option>';
				}
				?>
			</select>
		</span>
		<?}?>
		<tr>
			<td>
				<? if(isset($_POST['newRegister']) || isset($_POST['add'])){?>
				<span class="admin"><label><strong>権限</strong></label>
				<?}else{?>
					<span class="admin1"><label><strong>権限</strong></label>
			<?php	}

					//admin_flagが0の時の処理
					if(empty($admin_flag == 0)==false){
						print'<input type="hidden" name="admin_flag" value="0" />';
						print'<input name="admin_flag" type="checkbox" value="1" />';
						//admin_flagがo以外の時の処理
					}else{
						print'<input type="hidden" name="admin_flag" value="0" />';
						print'<input name="admin_flag" type="checkbox"checked="checked" value="1" />';
					}
					?> </span></td>
				</tr>

			</table>
			<br> <br>

			<table>

				<tr>
					<span class="toggle_btn">
						<?php
						if (isset($_POST['hensyuu']) || isset($_POST['change'])) {
							print '<input type="submit" value="変更" name="change" formaction="err_check.php"style="background-color:#99CCCC;WIDTH: 70px; HEIGHT: 30px">';
						}

						if (isset($_POST['newRegister']) || isset($_POST['add'])) {
							print '<input type="submit" value="追加"name="add"formaction="err_check.php"style="background-color:#99CCCC;WIDTH: 70px; HEIGHT: 30px">';
						}

						?>
					</span>

					<span class="toggle_btn1"> <input type="submit" value="キャンセル"
						formaction=<?print $url?>
						style="background-color: #99CCCC; WIDTH: 85px; HEIGHT: 30px">
					</span>
				</td>
			</tr>

		</table>

		<br>
	</form>
</body>
</html>
<style>
select {
	font-size: 15px;
}

span.sample97 {
	position: absolute;
	top: 5px;
	right: 0px
}
span.sample1 {
	position: absolute;
	top: 85px;
	left: 440px
}
span.sample2 {
	position: absolute;
	top: 85px;
	left: 590px
}
span.sample3 {
	position: absolute;
	top: 117px;
	left: 590px
}
span.sample8 {
	position: absolute;
	top: 145px;
	left: 440px
}

span.sample9 {
	position: absolute;
	top: 145px;
	left: 590px
}
span.sample50 {
	position: absolute;
	top: 147px;
	left: 700px
}

span.sample51 {
	position: absolute;
	top: 147px;
	left: 780px
}
span.sample53 {
	position: absolute;
	top: 177px;
	left: 590px
}
span.sample54 {
	position: absolute;
	top: 207px;
	left: 440px
}

span.sample55 {
	position: absolute;
	top: 207px;
	left: 590px
}

span.sample56 {
	position: absolute;
	top: 207px;
	left: 700px
}

span.sample57 {
	position: absolute;
	top: 207px;
	left: 780px
}

span.sample58 {
	position: absolute;
	top: 237px;
	left: 590px
}
span.sample59 {
	position: absolute;
	top: 267px;
	left: 440px
}

span.sample60 {
	position: absolute;
	top: 267px;
	left: 590px
}
span.sample61 {
	position: absolute;
	top: 297px;
	left: 590px
}
span.sample62 {
	position: absolute;
	top: 327px;
	left: 440px
}
span.sample63 {
	position: absolute;
	top: 327px;
	left: 590px
}
span.sample64 {
	position: absolute;
	top: 357px;
	left: 590px
}
span.sample66 {
	position: absolute;
	top: 387px;
	left: 440px
}
span.sample67 {
	position: absolute;
	top: 383px;
	left: 590px
}
span.sample68 {
	position: absolute;
	top: 415px;
	left: 590px
}
span.sample70 {
	position: absolute;
	top: 447px;
	left: 590px
}
span.sample71 {
	position: absolute;
	top: 447px;
	left: 440px
}

span.sample72 {
	position: absolute;
	top: 507px;
	left: 440px
}
span.sample73 {
	position: absolute;
	top: 504px;
	left: 590px
}
span.sample77 {
	position: absolute;
	top: 566px;
	left: 440px
}
span.sample78 {
	position: absolute;
	top: 566px;
	left: 590px
}
span.sample79 {
	position: absolute;
	top: 597px;
	left: 590px
}
span.sample81 {
	position: absolute;
	top: 629px;
	left: 440px
}
span.sample82 {
	position: absolute;
	top: 627px;
	left: 590px
}
span.sample84 {
	position: absolute;
	top: 693px;
	left: 440px
}
span.sample85 {
	position: absolute;
	top: 693px;
	left: 590px
}
span.sample100 {
	position: absolute;
	top: 658px;
	left: 590px
}
span.sample106 {
	position: absolute;
	top: 726px;
	left: 590px
}
span.sample87 {
	position: absolute;
	top: 755px;
	left: 440px
}
span.sample88 {
	position: absolute;
	top: 755px;
	left: 590px
}
span.sample109 {
	position: absolute;
	top: 790px;
	left: 590px
}
span.sample110 {
	position: absolute;
	top: 880px;
	left: 440px
}
span.sample111 {
	position: absolute;
	top: 820px;
	left: 440px
}
span.sample91 {
	position: absolute;
	top: 930px;
	left: 620px
}
span.sample92 {
	position: absolute;
	top: 930px;
	left: 700px
}
span.sample200 {
	position: absolute;
	top: 538px;
	left: 590px
}
span.sample201 {
	position: absolute;
	top: 475px;
	left: 590px
}
</style>
