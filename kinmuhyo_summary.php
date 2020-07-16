<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>勤務表</title>
</head>
<body>
<?php
if(!isset($_SESSION)){
		session_start();
	}
// ob_start();
// include("kinmuhyo.php");
// ob_clean();
$result=$_SESSION['result'];
$staff_name=$result['familyname'].$result['firstname'];
$email=$result['email'];
//$nengetshu=$_POST['nengetshu'];
$yukyu=$result['holiday_with_pay'];
//print'$nengetshu';
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

$selected['show']=array_fill(1,2,"");
$show=filter_input(INPUT_POST,"show");
$selected["show"][$show]="selected";

print <<<eof
<form method="post" action="kinmuhyo_summary.php">
表示する月　
<select name="show" width:50px>
	//<td>の中に変数入れる
	<option value="1"{$selected["show"][1]}>今月</option>
	<option value="2"{$selected["show"][2]}>先月</option>
</select>
<input name="nengetshu" type="submit" value="表示">
</form>
eof;
// print'<input name="nengetshu" type="submit" value="表示">';
// print'</form>';
print'<p>';
//出勤日数の取得
$syukkin = array_count_values($check);
$syukkin_nissuu=$syukkin["OK"];

 $kyuuka_nissuu="";
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
if($kyuuka_nissuu!=""){
$syukkin_nissuu=$syukkin_nissuu-$kyuuka_nissuu;
}

//振休日数の取得
if(isset($holiday)){
$kai=0;
foreach($holiday as $key =>$val){
   if(stristr($holiday[$key],"2") !== false){
      $kai++;
   }
}
//有給休暇の日数
$syoka=0;
foreach($holiday as $key =>$val){
   if(stristr($holiday[$key],"1") !== false){
      $syoka++;
   }
}

//特別休暇日数の取得
if(isset($holiday)){
$tokkyu=0;
foreach($holiday as $key =>$val){
   if(stristr($holiday[$key],"3") !== false){
      $tokkyu++;
   }
}
}

//欠勤日数の取得
if(isset($syukkin_nissuu)){
$kekkin = $eigyoubi - $syukkin_nissuu - $kai - $syoka;
}
//遅刻回数の日数
if(isset($bikou)){
$tikoku=0;
foreach($bikou as $key =>$val){
   if(stristr($bikou[$key],"遅刻") !== false){
      $tikoku++;
   }
}
//早退回数の日数
$soutai=0;
foreach($bikou as $key =>$val){
   if(stristr($bikou[$key],"早退") !== false){
      $soutai++;
   }
}
}
//遅刻・早退を足して３回以上(３の倍数)の時、欠勤とする。
if(isset($tikoku)|| isset($soutai)){
$batu="";
$bassoku=$tikoku+$soutai;
if($bassoku >= 3){
	if($bassoku % 3 === 0){
		$kekkin++;
	}
}
}
if(isset($holiday)){
//前半休の日数
$y_mae=0;
foreach($holiday as $key =>$val){
   if(stristr($holiday[$key],"4") !== false){
      $y_mae++;
   }
}
// 後半休の日数
$y_ato=0;
foreach($holiday as $key =>$val){
   if(stristr($holiday[$key],"5") !== false){
      $y_ato++;
   }
}
//半休は0.5日のため２で割る
//有給残数を求める
$yukyuzan=$yukyu-$syoka-($y_mae/2)-($y_ato/2);
$_SESSION['yukyuzan']=$yukyuzan;
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

$kyuuka_keisann="";
//総作業時間の算出
$kyuuka_keisan = ($syoka * $diff_hour2) + ($kai * $diff_hour2) + ($tokkyu * $diff_hour2);

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
		<td><?if(!empty($kai)){
			print $kai;
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
