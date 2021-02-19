<script language="JavaScript">
//URLが手打ちされた場合に画面をログイン画面に返す
var refinfo=document.referrer;
if (!refinfo){
　window.location.href = 'https://www.pros-service.co.jp/kinmu/staff_login.php';
}
</script>
<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/css/kinmu_summary.css">
<title>勤務表</title>
</head>
<body>
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
//ファイル読み込み	(勤務表)
if($_SERVER['HTTP_REFERER']!="https://www.pros-service.co.jp/kinmu/list_of_members.php"){
//if($_SERVER['HTTP_REFERER']!="http://localhost:8080/kinmuhyo/list_of_members.php"){
	ob_start();
	include("kinmuhyo.php");
	ob_clean();
//先月か今月の判定
?>
	<h3><?php print $now_month?>月分勤務表</h3>
<?php
//先月か今月を選択するプルダウン生成S//
$select['show']=array_fill(1,2,"");
$show=filter_input(INPUT_POST,"show");
$select["show"][$show]="selected";
if(isset($_SESSION["show"])){
	$select["show"][$_SESSION["show"]]="selected";
	unset($_SESSION["show"]);
}
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

//名前表示
print'<table border="1">';
	print'<tr>';
		print'<th>　氏名　';
		print'<td style="width:120px; text-align: center;">';
		print $staff_name;
		print'</td>';
		print'</th>';
	print'</tr>';
print'</table>';
}
//出勤日計算
if(isset($check_result)){
	$syukkin_nissuu="";
	foreach($check_result as $key =>$val){
  		if(stristr($check_result[$key],"OK") !== false){
      		$syukkin_nissuu++;
   		}
    	if(stristr($holiday[$key],"NULL") !== false){

   		}
		}
}else{
	$syukkin_nissuu=0;
}
//休暇日数の計算
//初期値は0
 $kyuuka_nissuu=0;
 $absence=0;
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
			if(stristr($holiday[$key],"6") !== false){
				//欠勤日数の取得
					$absence++;
			}
	}
}

if($kyuuka_nissuu!="" || $absence!=""){
$syukkin_nissuu = $syukkin_nissuu-$kyuuka_nissuu-$absence;
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
	//当月データありかつ勤務表未提出
	if($year_and_month==$comparison_month){
	 	if($kinmuhyo_summary['status']!="3" || $kinmuhyo_summary['status']!="4"){
			$yukyuzan= $yukyu-$syoka-($zenhan/2)-($kouhan/2);
	//当月データありかつ勤務表提出済
		}else{
			//その月にあった有給残数を表示したい
			$yukyuzan = $kinmuhyo_summary['remaining_paid_days'];
		}
	}
	}
}
if($zenhan!=0 || $kouhan!=0){
		$yuukyu_syoka = $syoka+($zenhan/2)+($kouhan/2);
}else{
	$yuukyu_syoka=$syoka;
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
		if(stristr($bikou[$key],"遅刻早退") !== false){
			//遅刻・早退回数の日数
   			$tikoku++;
      		$soutai++;
   		}elseif(stristr($bikou[$key],"遅刻") !== false){
   		//遅刻回数の日数
      		$tikoku++;
   		}elseif(stristr($bikou[$key],"早退") !== false){
   		//早退回数の日数
      		$soutai++;
   		}
	}
}

// //遅刻・早退を足して３回以上(３の倍数)の時、欠勤とする。
// if(isset($tikoku)|| isset($soutai)){
// 	$bassoku=$tikoku+$soutai;
// 	if($bassoku >= 3){
// 		//変数の初期化
// 		$i = 1;
// 	//ループ処理を$iが$bassokuの値になるまで実行する
// 		while ($i <= $bassoku){
//   		//$iを3で割った時のあまりが0になる時
//   			if ($i % 3 == 0){
//     			$kekkin++;
//     		}
//   			$i++;
// 		}
//
// 	}
// }
//不足時間の計算
//勤務地ごとの所定労働時間
$start = new DateTime($opening, new DateTimeZone('Asia/Tokyo'));
$stop= $start->diff(new DateTime($closong, new DateTimeZone('Asia/Tokyo')))->format('%H:%I');

//勤務地の休憩時間
$stop1= "01:00";
//所定労働時間から休憩時間を引く
$start2 = new DateTime($stop, new DateTimeZone('Asia/Tokyo'));
$stop2= $start2->diff(new DateTime($stop1, new DateTimeZone('Asia/Tokyo')))->format('%H:%I');
//求まった勤務時間から営業日数をかけ１ヶ月の所定労働時間を算出する
//不足時間の合計
//所定労働時間が７時間半の場合7.5に変更
if($stop2=="07:30"){
	$stop2="7.5";
}

//秒に直す
	$sum_fusoku = (float)$stop2*$eigyoubi * 3600 ;
//時間に直す
	$fusoku= floor($sum_fusoku / 3600) . gmdate(":i:s", $sum_fusoku);
//営業日*所定労働時間数＝不足時間
	$fusoku=substr($fusoku, 0, -3);

//有給フラグがある場合、有給時間数を求める
if($syoka!=0 || $transfer_holiday!=0 || $special_holiday!=0 || $zenhan!=0 || $kouhan!=0){
	$sum = $sum_fusoku - (float)$stop2*$syoka * 3600 - (float)$stop2*$transfer_holiday * 3600 - (float)$stop2*$special_holiday * 3600 - 4 * $zenhan * 3600 -  4 * $kouhan * 3600;
	$sum2= floor($sum / 3600) . gmdate(":i:s", $sum);
	//上記で求めた不足時間-休暇日数*所定労働時間=休暇を考慮した不足時間
	$fusoku=substr($sum2, 0, -3);
}
//実働合計から不足時間を引く
if($syoka==0 && $transfer_holiday==0 && $special_holiday==0 && $zenhan==0 && $kouhan==0){
	if(isset($sum_total["total_time"])){
		if($fusoku > $sum_total["total_time"]){
			$objDatetime1 = new DateTime(date(("Y/m/d H:i"), mktime(explode(":", $fusoku)[0],explode(":", $fusoku)[1])));
			$objDatetime2 = new DateTime(date(("Y/m/d H:i"), mktime(explode(":", $sum_total["total_time"])[0],explode(":", $sum_total["total_time"])[1])));
			$objInterval = $objDatetime1->diff($objDatetime2);
			$fusoku1 = $objInterval->format('%d');
			$fusoku2 = $objInterval->format('%H:%I');
			//不足時間が24時間を超える場合
			if($fusoku1!="0"){
				$fusoku = $objInterval->format('%H:%I');
				$sum_fusoku= $fusoku1 * 2400;
				$sum_fusoku = $sum_fusoku * 3600;
				$sum_fusoku= floor($sum_fusoku / 360000) . gmdate(":i:s", $sum_fusoku);
				$sum_fusoku=substr($sum_fusoku, 0, -3);
				$tArry=explode(":",$sum_fusoku);
				$fusoku3=explode(":",$fusoku2);
				$hour=$tArry[0]*60 + $fusoku3[0]*60;//時間→分
				$mins=$hour+$tArry[1]+$fusoku3[1];//分だけを足す
				function change_time_format($mm) {
    				return sprintf("%02d:%02d", floor($mm/60), $mm%60);
				}
				$mm = $mins;
				$fusoku =change_time_format($mm);
				//不足時間が24時間未満
			}else{
				$fusoku = $objInterval->format('%H:%I');
			}
			//実働時間 > 不足時間の場合
		}else{
			$fusoku="00:00";
		}
	}
//有給フラグがある場合
}else{
	if(isset($sum_total["total_time"])){
		if($fusoku >= $sum_total["total_time"]){
			$objDatetime1 = new DateTime(date(("Y/m/d H:i"), mktime(explode(":", $fusoku)[0],explode(":", $fusoku)[1])));
			$objDatetime2 = new DateTime(date(("Y/m/d H:i"), mktime(explode(":", $sum_total["total_time"])[0],explode(":", $sum_total["total_time"])[1])));
			$objInterval = $objDatetime1->diff($objDatetime2);
			$fusoku1 = $objInterval->format('%d');
			$fusoku2 = $objInterval->format('%H:%I');

			if($fusoku1!="0"){
				$fusoku = $objInterval->format('%H:%I');
				$sum_fusoku= $fusoku1 * 2400;
				$sum_fusoku = $sum_fusoku * 3600;
				$sum_fusoku= floor($sum_fusoku / 360000) . gmdate(":i:s", $sum_fusoku);
				$sum_fusoku=substr($sum_fusoku, 0, -3);
				$tArry=explode(":",$sum_fusoku);
				$fusoku3=explode(":",$fusoku2);
				$hour=$tArry[0]*60 + $fusoku3[0]*60;//時間→分
				$mins=$hour+$tArry[1]+$fusoku3[1];//分だけを足す
				function change_time_format($mm) {
    				return sprintf("%02d:%02d", floor($mm/60), $mm%60);
				}
				$mm = $mins;
				$fusoku =change_time_format($mm);
			}else{
				$fusoku = $objInterval->format('%H:%I');
			}
		}else{
			$fusoku="00:00";
		}
	}
}
//有給時間を差し引いた不足分と実働合計を比較し、実働合計が不足より大きい場合不足に０を代入
if(isset($sum3)){
	if($sum3 <= $sum_total["total_time"]){
		$fusoku="00:00";
	}
}
?>
<?php if($_SERVER['HTTP_REFERER']=="https://www.pros-service.co.jp/kinmu/list_of_members.php"){
	//if($_SERVER['HTTP_REFERER']=="http://localhost:8080/kinmuhyo/list_of_members.php"){?>
	<table border="1" class="tbl_summary td">
	<tr>
		<th>　氏名　
			<td><?php print $staff_name;?>
			</td>
		</th>
	</tr>
	<tr>
		<th>勤務地</th>
			<?php if($work_id!=""){?>
				<td><?php print $work_name ?></td>
			<?php }else{?>
				<td>
					<?php for ($i=0; $i < count($work_tbl); $i++) {
							print "<select name='work'>";
								print "<option>".""."</option>";
								print "<option>".$work_tbl."</option>";
							print "</select>";
						}?>
				</td>
			<?php }?>
	</tr>
</table>
<?php }?>
<table border="1" align="left" class="tbl_summary" style="margin-top: 10px;">
	<tr>
		<th>執務日数</th>
		<td><?php if(!empty($eigyoubi)){
		print $eigyoubi;
		}else{?>
			0<?php }?>日</td>
		<th>出勤日数</th>
		<td><?php if(!empty($syukkin_nissuu)){
		print $syukkin_nissuu;
		}
		else{?>
			0<?php }?>日</td>
		<th>欠勤日数</th>
		<td><?php if(!empty($kekkin)){
		print $kekkin;
		}
		else{?>
			0<?php }?>日</td>
		<th>振休日数</th>
		<td><?php if(!empty($transfer_holiday)){
			print $transfer_holiday;
			}else{?>
			0
			<?php }?>日</td>
	</tr>
	<tr>
		<th>遅刻回数</th>
		<td><?php if(!empty($tikoku)){
			print $tikoku;
		}else{?>
			0
		<?php }?>回</td>
		<th>早退回数</th>
		<td><?php if(!empty($soutai)){
			print $soutai;
		}else{?>
			0
		<?php }?>回</td>
		<th>普通残業</th>
		<td>
			<?php if($sum_overtime["total_time"]!=""){
				if($sum_overtime["total_time"]!="00:00"){
				print explode(":", $sum_overtime["total_time"])[0].":".explode(":", $sum_overtime["total_time"])[1];
				}else{
					print "00:00";
				}
				}else{
					print "00:00";
				}?>
		</td>
		<th>深夜残業</th>
		<td>
			<?php if($sum_overtime_night["total_time"]!=""){
				if($sum_overtime_night["total_time"]!="00:00"){
				print explode(":", $sum_overtime_night["total_time"])[0].":".explode(":", $sum_overtime_night["total_time"])[1];
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
		<td><?php if(!empty($yuukyu_syoka)){
			print $yuukyu_syoka;
		}else{?>
			0
			<?php }?>日</td>
		<th>前半休</th>
		<td><?php if(!empty($zenhan)){
			print $zenhan;
		}else{?>
			0
		<?php }?>日</td>
		<th>後半休</th>
		<td><?php if(!empty($kouhan)){
			print $kouhan;
		}else{?>
			0
		<?php }?>日</td>
		<th>総作業時間</th>
		<td>
			<?php if($sum_total["total_time"]!=""){
					print explode(":", $sum_total["total_time"])[0].":".explode(":", $sum_total["total_time"])[1];
				}else{
					print "00:00";
				}?>
		</td>
	</tr>
	<tr>
		<th>有給残日数</th>
		<td><?php if($kinmuhyo_summary['status']==4){
			print $kinmuhyo_summary['remaining_paid_days'];
		}elseif(isset($yukyuzan)){
				print $yukyuzan;
			}elseif(isset($_SESSION["yukyuzan"])){
				print $_SESSION["yukyuzan"];
			}else{
				print $yukyu;
			}?>日</td>
		<th>特休日数</th>
		<td><?php if(!empty($special_holiday)){
			print $special_holiday;
		}else{?>
			0
		<?php }?>日</td>
		<th></th>
		<td></td>
		<th>不足時間</th>
		<td><?php print $fusoku?></td>
	</tr>
</table>
<?php if($_SERVER['HTTP_REFERER']!="https://www.pros-service.co.jp/kinmu/list_of_members.php"){
	//if($_SERVER['HTTP_REFERER']!="http://localhost:8080/kinmuhyo/list_of_members.php"){?>
	</br>
	</br>
	</br>
	</br>
	</br>
	</br>
	<p>
	<a href="kinmuhyo.php">明細を入力する</a>
<?php }else{?>
	<div class="box">担当印
	<?php if($kinmuhyo_summary['status']!="0" || $kinmuhyo_summary['status']!="1"){?>
		<div class="stamp stamp-approve">
			<span><?php print $kinmuhyo_summary['send_date']?></span>
			<span><?php print $staff_name?></span>
		</div>
	<?php }?>
	</div>
	<div class="box">責任者確認印
		<div class="stamp stamp-approve">
			<span><?php print $kinmuhyo_summary['send_date']?></span>
			<span>軽部</span>
		</div>
	</div>
<?php }?>
</body>
</html>
<style type="text/css">
.box{
	margin-top: -20px;
	float: right;
	width: 130px;
	height: 130px;
	font-weight: bold;
    border: solid 3px #000000;
    text-align: center;
}
.stamp { font-size:13px; border:3px double #f00; border-radius:50%; color:#f00; width:100px; height:100px; position:relative; margin:auto; }
.stamp span { display:inline-block; width:100%; text-align:center; }
.stamp span:first-child::before { position:absolute; top:50px; left:0; right:0; margin:auto; width:100%; border-bottom:1px line-height:1; padding-bottom:10px; }
.stamp span:first-child { line-height:60px; }
.stamp span:last-child { position:absolute; top:50px; left:0; right:0; margin:auto; width:80%; border-top:1px solid #f00; padding-top:10px; line-height:1; }

#square-button {
  width: 80px;
  height: 80px;
  background: #232323;
}
#square-button.blue {
  background: #21759b;
}
</style>
