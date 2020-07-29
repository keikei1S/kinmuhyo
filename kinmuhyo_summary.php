<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>勤務表</title>
</head>
<body>
<?php
//セッションが開始されていなければセッションを開始する。
if(!isset($_SESSION)){
	session_start();
}
//ファイル読み込み	(勤務表)
ob_start();
include("kinmuhyo.php");
ob_clean();

//先月か今月の判定
if(isset($_POST['show'])==""){?>
	<h3><?php print $now_month?>月分勤務表</h3>
<?}
elseif($_POST['show']==1){?>
 	<h3><?php print $now_month?>月分勤務表</h3><?php }
 		else{?>
			<h3><?php print $now_month?>月分勤務表</h3>
		<?}
print'<table border="1">';
	print'<tr>';
		print'<th>　氏名　';
		print'<td>';
		print $staff_name;
		print'</td>';
		print'</th>';
	print'</tr>';
print'</table>';

//先月か今月を選択するプルダウン生成S//
$select['show']=array_fill(1,2,"");
$show=filter_input(INPUT_POST,"show");
$select["show"][$show]="selected";

print <<<eof
<form method="post" action="kinmuhyo_summary.php">
表示する月　
<select name="show" width:50px>
	//<td>の中に変数入れる
	<option value="1"{$select["show"][1]}>今月</option>
	<option value="2"{$select["show"][2]}>先月</option>
</select>
<input name="nengetshu" type="submit" value="表示">
</form>
eof;
//先月か今月を選択するプルダウン生成E//

//休暇日数の計算
//初期値は0
 $kyuuka_nissuu=0;
 if(isset($holiday)){
	foreach($holiday as $key =>$val){
  		if(stristr($holiday[$key],"1") !== false){
      		$kyuuka_nissuu++;
   		}
    	if(stristr($holiday[$key],"2") !== false){
      		$kyuuka_nissuu++;
   		}
   		if(stristr($holiday[$key],"3") !== false){
      		$kyuuka_nissuu++;
   		}
	}
}
if($kyuuka_nissuu!=""){
$syukkin_nissuu=$syukkin_nissuu-$kyuuka_nissuu;
}
//有給日数の計算
$syoka=0;
$transfer_holiday=0;
$special_holiday=0;
$zenhan=0;
$kouhan=0;
if(isset($holiday)){
	foreach($holiday as $key =>$val){
		//有給休暇の日数
   		if(stristr($holiday[$key],"1") !== false){
			$syoka++;
   		}elseif(stristr($holiday[$key],"2")!==false){
   		//振休日数の取得
   			$transfer_holiday++;
   		}elseif(stristr($holiday[$key],"3")!==false){
   		//特別休暇日数の取得
   			$special_holiday++;
   		}elseif(stristr($holiday[$key],"4") !== false){
		//前半休の日数
			$zenhan++;
   		}elseif(stristr($holiday[$key],"5") !== false){
   		//後半休の日数
			$kouhan++;
		}
   	//半休は0.5日のため２で割る
	//有給残数を求める
	$yukyuzan=$yukyu-$syoka-($zenhan/2)-($kouhan/2);
	}
}

//欠勤日数の取得
if(isset($syukkin_nissuu)){
	$kekkin = $eigyoubi - $syukkin_nissuu - $syoka - $transfer_holiday - $special_holiday;
	if($kekkin < 0){
		$kekkin=0;
	}
}


//遅刻/早退回数の日数
$tikoku=0;
$soutai=0;
if(isset($bikou)){
	foreach($bikou as $key =>$val){
   		if(stristr($bikou[$key],"遅刻") !== false){
   		//遅刻回数の日数
      		$tikoku++;
   		}elseif(stristr($bikou[$key],"早退") !== false){
   		//早退回数の日数
      		$soutai++;
   		}
	}
}

//遅刻・早退を足して３回以上(３の倍数)の時、欠勤とする。
if(isset($tikoku)|| isset($soutai)){
	$bassoku=$tikoku+$soutai;
	if($bassoku >= 3){
		if($bassoku % 3 === 0){
			$kekkin++;
		}
	}
}

//不足時間の計算
//勤務地ごとの所定労働時間
$date1 = date("2020-01-01". $opening);
$date2 = date("2020-01-01". $closong);
$diff_hour = (strtotime($date2) - strtotime($date1)) / 3600;

//勤務地の休憩時間
$date3 = date("2020-01-01". $break_start);
$date4 = date("2020-01-01". $break_end);
$diff_hour1 = (strtotime($date4) - strtotime($date3)) / 3600;

//休憩時間を引く
$diff_hour2=$diff_hour-$diff_hour1;

//求まった勤務時間から営業日数をかけ１ヶ月の所定労働時間を算出する
$fusoku=$diff_hour2*$eigyoubi;

//不足時間の合計
if($syoka==0 && $transfer_holiday==0 && $special_holiday==0){
	$fusoku=$diff_hour2*$eigyoubi;
}else{
	$fusoku=$diff_hour2*$eigyoubi - ($syoka * $diff_hour2) -($transfer_holiday * $diff_hour2) - ($special_holiday * $diff_hour2);
}

?>

<table border="1">
	<tr>
		<th>執務日数</th>
		<td><?if(!empty($eigyoubi)){
		print $eigyoubi;
		}else{?>
			0<?}?>日</td>
		<th>出勤日数</th>
		<td><?if(!empty($syukkin_nissuu)){
		print $syukkin_nissuu;
		}
		else{?>
			0<?}?>日</td>
		<th>欠勤日数</th>
		<td><?if(!empty($kekkin)){
		print $kekkin;
		}
		else{?>
			0<?}?>日</td>
		<th>振休日数</th>
		<td><?if(!empty($transfer_holiday)){
			print $transfer_holiday;
			}else{?>
			0
			<?}?>日</td>
	</tr>
	<tr>
		<th>遅刻回数</th>
		<td><?if(!empty($tikoku)){
			print $tikoku;
		}else{?>
			0
		<?}?>回</td>
		<th>早退回数</th>
		<td><?if(!empty($soutai)){
			print $soutai;
		}else{?>
			0
		<?}?>回</td>
		<th>普通残業</th>
		<td>
			<?if($sum_overtime["total_time"]!=""){
				if($sum_overtime["total_time"]!="00:00"){
				print $sum_overtime["total_time"];
				}else{
					print "00:00";
				}
				}else{
					print "00:00";
				}?>
		</td>
		<th>深夜残業</th>
		<td>
			<?if($sum_overtime_night["total_time"]!=""){
				if($sum_overtime_night["total_time"]!="00:00"){
				print $sum_overtime_night["total_time"];
				}else{
					print "00:00";
				}
				}else{
					print "00:00";
				}?>
		</td>
	</tr>
	<tr>
		<th>有給日数</th>
		<td><?if(!empty($syoka)){
			print $syoka;
		}else{?>
			0
			<?}?>日</td>
		<th>前半休</th>
		<td><?if(!empty($y_mae)){
			print $y_mae;
		}else{?>
			0
		<?}?>日</td>
		<th>後半休</th>
		<td><?if(!empty($y_ato)){
			print $y_ato;
		}else{?>
			0
		<?}?>日</td>
		<th>総作業時間</th>
		<td>
			<?if($sum_total["total_time"]!=""){
				print $sum_total["total_time"];
				}else{
					print "00:00";
				}?>
		</td>
	</tr>
	<tr>
		<th>有給残日数</th>
		<td><?if(!empty($yukyuzan)){
			print $yukyuzan;
		}else{
			print $yukyu;
		}?>日</td>
		<th></th>
		<td></td>
		<th></th>
		<td></td>
		<th>不足時間</th>
		<td><?=$fusoku?></td>
	</tr>
</table>

<p>
<a href="kinmuhyo.php">明細を入力する</a>

</body>
</html>
