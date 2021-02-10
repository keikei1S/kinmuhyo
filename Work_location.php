<?php
//セッションが開始されていなければセッションを開始する。
if(!isset($_SESSION)){
	session_start();
	session_regenerate_id(true);
}
if (isset($_SESSION["login"])==false)
{
	header("Location: staff_login.php");
	exit();
}
require('kinmu_common.php');
if (isset($_POST['worklocation'])) {
    $_SESSION['worklocation'] = $_POST['worklocation'];
}
try {
    // //////////////////データベースの読込 S//////////////////////
	$dbh = db_connect();
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	//tbl_belongssの値を全て取得
	$belongsssql = 'select * FROM  TBL_BELONGSS';
	// $belongsssql = 'select * FROM  tbl_belongss';
	$belongssstmt = $dbh->prepare($belongsssql);
	$belongssstmt->execute();



	//tbl_belongssの値を全て取得
	$belongsssql2 = 'select * FROM  TBL_BELONGSS';
	// $belongsssql2 = 'select * FROM  tbl_belongss';
	$belongssstmt2 = $dbh->prepare($belongsssql2);
	$belongssstmt2->execute();

	//tbl_belongssの値を全て取得
	$belongsssql3 = 'select * FROM  TBL_BELONGSS';
	// $belongsssql3 = 'select * FROM  tbl_belongss';
	$belongssstmt3 = $dbh->prepare($belongsssql3);
	$belongssstmt3->execute();

	//tbl_belongssの値を全て取得
	$belongsssql4 = 'select * FROM  TBL_BELONGSS ORDER BY TBL_BELONGSS.update_date DESC';
	// $belongsssql4 = 'select * FROM  tbl_belongss';
	$belongssstmt4 = $dbh->prepare($belongsssql4);
	$belongssstmt4->execute();
	$belongssrec4 = $belongssstmt4->fetch(PDO::FETCH_ASSOC);
	$modify = $belongssrec4["last_modified"];
	$update = substr($belongssrec4["update_date"],0,10);



	$dbh = null;
	// ////////////////データベースの読込 E//////////////////////
	error_reporting(8192);
	if(isset($_POST['display'])){

	while (true) {
		//tbl_belongssから1レコード取得
		$belongssrec2 = $belongssstmt2->fetch(PDO::FETCH_ASSOC);
		if ($belongssrec2 == false) {
		   break;
	   }if($belongssrec2['work_id'] == $_POST['staff']){
			break;
	   }
	}
	$_SESSION['work_id']  = $belongssrec2['work_id'];
	$_SESSION['work_name'] = $belongssrec2['work_name'];
	$_SESSION['opening_hours'] = substr($belongssrec2['opening_hours'], 0, 5);
	$_SESSION['closing_hours'] = substr($belongssrec2['closing_hours'], 0,5);

	$work_id = $_SESSION['work_id'];
	$work_name = $_SESSION['work_name'];
	$opening_hours = $_SESSION['opening_hours'];
	$closing_hours = $_SESSION['closing_hours'];
	}
	 else{
		$work_id = $_SESSION['kinmuchi'];
		$work_name = $_SESSION['kinmuchiid'];
		$opening_hours = $_SESSION['strat'];
		$closing_hours = $_SESSION['end'];
	}
	?>

	<!DOCTYPE html>
	<html>
	<head>
	<meta charset="UTF-8">
	<title>勤務地情報編集</title>
	<link rel="stylesheet" href="/css/work_location.css">
	</head>
	<body>
	<?php
		$modify_user= kinmu_common::staff_table($modify);
		print "最終更新者:".$modify_user["familyname"].$modify_user["firstname"];
		print "</br>";
		print "最終更新日:".$update;
	?>
		<div align="center">
<?php
	print '<form method="post">';

	print '<span class="sample97">
    <img src="https://www.pros-service.co.jp/img/image_2020_4_10.png"
    alt="画像のサンプル" width="150px" height="60px">
    </span>';

	print '<h3 style="margin-top:-35px;">勤務地情報の登録</h3>';
	print '<span class="sample5">';
	print '<label><strong>勤務ID';
	print '</span>';
	print '</strong></label>';
	print '<span class="sample3">';
  if(isset($_POST['display'])){
	   print  $work_id;
		 unset($_SESSION["id_err"]);
  }elseif(isset($_SESSION["id_err"])){
		print  $work_id;
	}
	print '</span>';
	print '<br>';
	if(isset($_SESSION["id_err"])){
		print '<font color="red">';
		print $_SESSION["id_err"];
		print '</font>';
	}
	if(isset($work_id)){
		$_SESSION["kinmuchi"]=$work_id;
	}
	print '<span class="sample11">';
	print '<font color="red">';
	$belongssrec3 = $belongssstmt3->fetchall(PDO::FETCH_ASSOC);
if(empty($_POST['display'])){
  if(isset($_SESSION["up_err"])){
    print $_SESSION["up_err"];
  }
}


	print  ' </font>';
	print '</span>';
	 print '<br>';
	 print '<span class="sample6">';
	print '<label><strong>勤務地名';
	print '</strong></label>';
	print '</span>';
	print '<span class="sample4">';
	print '<input type="text"value="' . $work_name .'" name="kinmuchiid" maxlength="20" style="width:172px;height:19px;">';
	print '</span>';
	print '<br>';
	print '<span class="sample12">';
	print '<font color="red">';
  if(empty($_POST['display'])){
	if(empty($_POST['worklocation'])){
		if(empty($_POST['update'])){
			if(empty($_POST['insert'])){
		if (empty($work_name)){
			print "※入力必須項目です(勤務地名)";
		   }
		}
	}
		}
  }



		print  ' </font>';
		print '</span>';
	print '<br>';
	print '<span class="sample8">';
	print '<label><strong>始業時間&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	print '</strong></label>';
	print '</span>';
	print '<span class="sample7">';
	print '<input type="time" list="start_time" value="' .$opening_hours.'" style="width:174px;height:22px;" name="strat">';
  print '<datalist id="start_time">
        <option value="08:00"></option>
        <option value="08:30"></option>
        <option value="09:00"></option>
        <option value="09:30"></option>
        <option value="10:00"></option>
        </datalist>';
	print '</span>';
	print '<br>';
	print '<font color="red">';
  if(empty($_POST['display'])){
	if(empty($_POST['worklocation'])){
		if(empty($_POST['update'])){
			if(empty($_POST['insert'])){
		if (empty($opening_hours)){
			print "※入力必須項目です(始業時間)";
		   }elseif($opening_hours == $closing_hours){
			print "※終業時間と同一時刻は入力できません";
		}elseif( $opening_hours > $closing_hours){
			print "※終業時間より早い時刻を入力してください";
		}
	}
}
}

	}
		print  ' </font>';
		print '<br>';
	print '<span class="sample10">';
	print '<label><strong>終業時間&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	print '</strong></label>';
	print '</span>';
	print '<span class="sample9">';
	print '<input type="time" list="end_time" value="' .$closing_hours.'"style="width:174px;height:22px;" name="end">';
  print '<datalist id="end_time">
        <option value="17:00"></option>
        <option value="17:30"></option>
        <option value="18:00"></option>
        <option value="18:30"></option>
        <option value="19:00"></option>
        </datalist>';
	print '</span>';
	print '<br>';
	print '</div>';


	// ////////////////中央揃え S//////////////////////





	print '<div align="center">';
	print '<font color="red">';
  if(empty($_POST['display'])){
	if(empty($_POST['worklocation'])){
		if(empty($_POST['update'])){
			if(empty($_POST['insert'])){
		if (empty($closing_hours)){
			print "※入力必須項目です(終業時間)";
		   }elseif($opening_hours == $closing_hours){
			print "※始業時間と同一時刻は入力できません";
		}elseif( $closing_hours < $opening_hours ){
			print "※始業時間より遅い時刻を入力してください";
		}
	}
}
}

		}
	print '</font>';
	print '<h4>◆登録済み勤務地一覧&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h4>';

//テーブルにスクロール機能追加
	print '<div style="height:160px; width:500px; overflow-x:scroll;">';

print '<table border=1  cellspacing="1" cellpadding="5">';
print '<tr><td>';
print '&nbsp;&nbsp;&nbsp;&nbsp;';
print '</td>';

print '<td>';
print 'ID';
print '</td>';

print '<td>';
print '勤務地名';
print '</td>';

print '<td>';
print '始業時間';
print '</td>';

print '<td>';
print '終業時間';
print '</td>';

	while (true) {
 //tbl_belongssから1レコード取得
 $belongssrec = $belongssstmt->fetch(PDO::FETCH_ASSOC);
 if ($belongssrec == false) {
	break;
}
print '<tr><td>';

// 勤務地選択ボタンを作成
 $test = $belongssrec['work_id'];

//エラー表示停止
error_reporting(8192);
 if($flag == 0){
print '<input type="radio"name="staff"checked="checked"value="'. $test .'">';
	$flag = 1;
}
elseif(isset($test)){
	$checked = $test == $work_id ?" checked":"";
	print '<input '.$checked.' type="radio"name="staff"value="'. $test .'">';
}
else{
	print'<input type="radio"name="staff"value="'.$test.'">';
}

print '</td>';
print '<td>';
print $belongssrec['work_id'];
print '<td>';
		print $belongssrec['work_name'];

print '<td>';
		$open = (substr($belongssrec['opening_hours'], 0, 5));
		print $open;

print '<td>';
		$close = (substr($belongssrec['closing_hours'], 0, 5));
		print $close;

print '</tr>';

print '</div>';
	}
print '</table>';

}
 catch (Exception $e) {
   header('Location: err_report.php');
   exit();
}
//スクロール
print '</div>';
print '<br>';
print '<span class="button2">';
print '<input type="submit" name="tuika"value="追加"formaction="Work_location_err_check.php"
style="background-color:#99CCCC; WIDTH: 70px; HEIGHT: 30px">';
print '</span>';

print '<span class="button3">';
print '<input type="submit"name="kousin" formaction="Work_location_err_check.php"value="更新"
style="background-color:#33FF66;WIDTH: 70px; HEIGHT: 30px">';
print '</span>';

print '<span class="button">';
print '<input type="submit" value="表示" formaction="Work_location.php"name="display"
style="background-color:gold;WIDTH: 70px; HEIGHT: 30px">';
print '</span>';

print '<span class="button4">';
print '<input type="submit" value="戻る" formaction="list_of_members.php"
style="background-color:#99CCCC;WIDTH: 70px; HEIGHT: 30px">';
print '</span>';
// ////////////////中央揃え E//////////////////////

print'</form>';
print '</div>';
?>
</body>
</html>
