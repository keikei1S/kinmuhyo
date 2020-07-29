<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>	
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/css/kinmuhyo.css">
<title>勤務表</title>
</head>
<body>
<?php
if(!isset($_SESSION)){
		session_start();
	}
session_regenerate_id(true);
// ログイン状態のチェック
//不正ログインの場合ログイン画面に遷移させる
if (isset($_SESSION["login"])==false) 
{
	header("Location: staff_login.php");
	exit();
}
//ファイル読み込み	
require'kinmu_common.php';

//require'kinmuhyo_summary.php';
//ログイン画面からセッションで引き継がれた値を変数に格納
$result=$_SESSION['result'];
$staff_number=$result['staff_number'];
$staff_name=$result['familyname'].$result['firstname'];
$admin_flag=$result['admin_flag'];
$email=$result['email'];
$yukyu=$result['holiday_with_pay'];

//メソッドを参照する（サマリーテーブル）
$kinmuhyo_summary= kinmu_common::kinmuhyo($result['staff_number']);
//社員番号にひもづく勤務地IDを取得
$work_id=$kinmuhyo_summary['work_ID'];
$_SESSION['work_id']=$work_id;
//メソッドを参照する（勤務情報）
$kinmuhyo_attendance= kinmu_common::Attendance($result['staff_number']);
//メソッドを参照し、値があった場合
//それぞれの変数に値を格納
if($kinmuhyo_attendance!=""){
foreach ($kinmuhyo_attendance as $key => $value) {
	//コメント
	$naiyou[$key]=$value['content'];
	//桁数指定
	//始業時間
	$open[$key]=substr($value['opening_hours'],0,2);
	$open2[$key]=substr($value['opening_hours'],3,2);
	//桁数指定
	//終業時間
	$close[$key]=substr($value['closing_hours'],0,2);
	$close2[$key]=substr($value['closing_hours'],3,2);
	//桁数指定？？
	//休憩時間
	$rest[$key]=substr($value['break_time'],0,5);
	$res_1[$key]=substr($value['break_time'],0,2);
	$res_2[$key]=substr($value['break_time'],3,2);
	//実働時間
	$total[$key]=substr($value['total'],0,5);
	//残業時間
	$overtime_normal[$key]=substr($value['overtime_normal'],0,5);
	//深夜残業時間
	$overtime_night[$key]=substr($value['overtime_night'],0,5);
	//不足時間
	$Shortage[$key]=substr($value['short'],0,5);
	//備考コメント
	$bikou[$key]=$value['bikou'];
	//休暇フラグ
	$holiday[$key]=$value['vacation'];
	//チェック結果
	$check[$key]=$value['check_result'];
	//シフトフラグ
	$shift[$key]=$value['shift'];
}
}

//メソッド参照
//実働合計
$sum_total= kinmu_common::sum_total($result['staff_number']);
//残業合計
$sum_overtime= kinmu_common::sum_overtime($result['staff_number']);
//深夜残業合計
$sum_overtime_night= kinmu_common::sum_overtime_night($result['staff_number']);
//不足合計
$sum_short= kinmu_common::sum_short($result['staff_number']);
//祝日取得
$kinmuhyo_holiday= kinmu_holiday::Holiday("");

//変数の整理する
//勤務地テーブルを参照
$BELONGSS= kinmu_common::BELONGSS($result['staff_number']);
//$nengetshu=$kinmuhyo_attendance['year_and_month'];
//始業時間を取得
$opening=$BELONGSS['opening_hours'];
$_SESSION['opening']=$opening;
//取得した始業時間の桁数調整
$opening_get=substr($opening,0,5);
//終業時間を取得
$closong=$BELONGSS['closing_hours'];
$_SESSION['closong']=$closong;
//取得した終業時間の桁数調整
$closong_get=substr($closong,0,5);
//勤務地名の取得
$work_name=$BELONGSS['work_name'];
//休憩開始時間の取得
$break_start=$BELONGSS['break_start'];
//休憩終了時間の取得
$break_end=$BELONGSS['break_end'];


//カレンダー作成
//タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');
//今月・先月の判定

if(empty($_POST['show'])|| $_POST['show']=="1"){
	//システム日付(年)を取得
	$year=date('Y');
	//システム日付(月)を取得
	$month=date('m');
	//今月頭を取得
	$now_month = date('Y/m', strtotime(date('Y/m/01')));
	//月末日を取得
	$now_end_month = date('t', strtotime($now_month.'/'.'01'));
	
}else{
	//システム日付(年)を取得
	$year=date('Y');
	//システム日付(月)を取得
	$month=date('m',strtotime('-1 month'));
	//先月頭
	$now_month = date('Y/m', strtotime(date('Y/m/01') . '-1 month'));
	//先月末日を取得
	$now_end_month = date('t', strtotime($now_month.'/'.'01'));
}
$_SESSION['month']=$month;
$aryWeek = ['日', '月', '火', '水', '木', '金', '土'];
	//1日から月末日までループ
	for ($i = 1; $i <= $now_end_month; $i++){
    $aryCalendar[$i]['day'] = $i;
    $aryCalendar[$i]['week'] = date('w', strtotime($year.$month.sprintf('%02d', $i)));
    //YMD形式に日付をなおし、祝日テーブルの値と比較する
    $this_month[$i] = $year."/".$month."/".sprintf('%02d',$i);
    $syuku=array_intersect($kinmuhyo_holiday, $this_month);
    $judge[]=sprintf("%02d", $aryCalendar[$i]['day']);
	}

if(!empty($syuku)){
	//祝日日数を出す。
	foreach ($syuku as $value) {
		$w[]=substr($value,8,2);
	}
	//祝日日数を計算
	$q=array_intersect($w,$judge);
	//祝日日数を計算
	$c_q=count($q);
}
//チェックの結果エラーメッセージがあれば変数に格納
$err_msg=isset($_SESSION['err_msg']) ? $_SESSION['err_msg'] : '';
$check[]=isset($_SESSION['check']) ? $_SESSION['check'] : '';
?>
<div class="img">
	<img src="/img/image_2020_4_10.png" height="100" width="100" alt="ロゴ" align="right" >
</div>
<!-- 先月か今月の判定 -->
<?php if(isset($_POST['show'])==""){?>
	<h3><?php print $now_month?>月分勤務表</h3>
<?php }
elseif($_POST['show']==1){?>
 	<h3><?php print $now_month?>月分勤務表</h3><?php }
 		else{?>
			<h3><?php print $now_month?>月分勤務表</h3>
		<?php }?>
<table border="1">
	<tr>
		<th>　氏名　
		<td><?php print $staff_name;?>
		</td>
		</th>
	</tr>
</table>
<!-- <?php if($err_msg1!=""){?>
	<div class="errMsg">
		<?php foreach ($err_msg1 as $errMsg) {
			print $errMsg;
		};?>
	</div>
<?php }?> -->
<?php
//今月先月選択
$selected['show']=array_fill(1,2,"");
$show=filter_input(INPUT_POST,"show");
$selected["show"][$show]="selected";
print <<<eof
<form method="post" action="kinmuhyo.php">
表示する月　
<select name="show" width:50px>
	//<td>の中に変数入れる
	<option value="1"{$selected["show"][1]}>今月</option>
	<option value="2"{$selected["show"][2]}>先月</option>
</select>
<input name="nengetshu" type="submit" value="表示">
</form>
eof;
?>
	<p>
	<table border="1">
		<tr>
			<th>始業</th>
			<?php if($opening_get!=""){?>
			<td><?php print $opening_get?></td>
			<?}else{?>
			<td>aa</td>
			<?}?>
		</tr>
			<tr>
			<th>終業</th>
			<?php if($closong_get!=""){?>
			<td><?php print $closong_get ?></td>
			<?}else{?>
			<td>aa</td>
			<?}?>
		</tr>
		<tr>
			<th>勤務地</th>
			<?php if($work_id!=""){?>
			<td><?php print $work_name ?></td>
			<?}else{?>
			<td>aa</td>
			<?}?>
		</tr>
	</table>
	<p>
	<form method="post" action="kinmuhyo_summary.php">
	<!-- <a href="kinmuhyo_summary.php">サマリー情報を見る</a> -->
	<input type='hidden' name="month" value="<?=$now_month?>">
	<input type="submit" value="サマリー情報を見る">
	</form>
	<form method="post" action="kinmuhyo_done.php">
		<input name="tochu" type="submit" value="管理者へ提出">
	</form>
	<table class="calender_column">
		<td>
	    	<p>日</p>
	    </td>
	    <td>
	    	<p>曜</p>
	    </td>
	    <td>
	    	<p>作業内容</p>
	    </td>
	    <td>
	    	<p>始業</p>
	    </td>
		<td>
	    	<p>終業</p>
	    </td>
	    <td>
	    	<p>休憩</p>
	    </td>
	    <td>
	    	<p>実働</p>
	    </td>
		<td>
	    	<p>普通残業</p>
	    </td>
	    <td>
	    	<p>深夜残業</p>
	    </td>
	    <td>
	    	<p>不足</p>
	    </td>
	    <td>
	    	<p>備考</p>
	    </td>
	    <td class="kyuka">
	    	<p>1.有給  4.前休</p>
	    	<p>2.振休  5.後休</p>
	    	<p>3.特休</p>
	    </td>
	    <td class="check">
	    	<p>チェック結果</p>
	    </td>
	    <td class="shift">
	    	<p>シフト</p>
	    </td>
	    <!-- 今月（初期表示）を選択した場合 -->
	<?php if(isset($_POST['show'])==""|| 1){
		//カレンダーの配列分処理を行う
		foreach($aryCalendar as $value){
			//祝日の取得
			if(!empty($q)){
	        $re=in_array(sprintf("%02d", $value['day']),$q);
	    }
	  ?>
	  <!-- 取得した該当の曜日クラスを割り当てる -->
		<tr class="week<?php echo $value['week'] ?>">
		<!-- 祝日の場合日曜クラスを適用する -->
		<?if(!empty($re)){
		$value['week']="0";?>
		<tr class="week<?php echo $value['week'] ?>">
		<?}?>
	    	<?php if($value['day'] != date('j')){ ?>
	    	<?}
	    	//土日の度にカウントする
	    		 if($value['week']=="6"){
	    					$doyou[]=$i++;
	    				}elseif($value['week']=="0"){
	    					$nitisyuku[]=$i++;
	    				}?>
	        	<td>
	            	<?php 
	            	//日付を表示
	            		echo $value['day'];
	            		?>
	        	</td>
	        	<td>
	        		<!-- <form method="post" action="kinmuhyo_check.php"> -->
	        			<form method="post" action="check2.php">
	        		<input type='hidden' name="month" value="<?=$month?>">
	        		<!-- 週の情報をチェックに渡す -->
	        		<input type='hidden' name="week[]" value="<?=$aryWeek[$value['week']]?>">
	        		<!-- 祝日でない場合 -->
	        		<?
	        		if(empty($re)){
	        			echo $aryWeek[$value['week']];
	        			//祝日の場合
	        		}else{
	        			$value['week']=date('w', strtotime($year.$month.$value['day']));
	        			echo $aryWeek[$value['week']];
	        		}?>
	        	</td>
	        	<td>
	        	<!-- 配列の順序と合わせるため、日付マイナス１を指定し配列を指定 -->
	        		<? if(!empty($naiyou[$value['day']-1])){?>
	           		<input type='text' name="naiyou[]" value="<? foreach((array)$naiyou as $k_key =>$k_naiyou){
	           			if($value['day']-1==$k_key){
	           			print $k_naiyou;
	           		}
	           		}?>">
	           	<?}else{?>
	           		<input type='text' name="naiyou[]">
	           	<?}?>
	        	</td>
	        		<td>	
	        		<!-- open~closeは同じ処理(変数名の違いのみ) -->
	        			<?php
	        			//始業時間の入力域
	        				for($i=0; $i<= $now_end_month; $i++){
	        					print "<select name=open[]>";
	        					$t = strtotime('0:00');
								for ($i = 0; $i < 5 * 12 * 24; $i += 60) {
	   			 				echo "<option>".date('H', strtotime("+{$i} minutes", $t)) . PHP_EOL."</option>";
							}
							//テーブルに値があれば初期値にテーブルの値を表示する
							if(!empty($open[$value['day']-1])){
	        					foreach($open as $hour => $ope){
									if($value['day']-1==$hour){
										if($value['week']=="6" || $value['week']=="0" ||$re){
	   											echo "<option selected>".""."</option>";
	        						}
										if($ope=="00"){
											echo "<option>".""."</option>";
										}else{
									echo "<option>".""."</option>";
	 								print "<option selected>$ope</option>";
	 								}
	 							}
	 							}
	 							//現場所定労働時間ありかつ土日の場合
	        				}elseif($opening_get!=""){
	        					if($value['week']=="6" || $value['week']=="0" ||$re){
	   											echo "<option selected>".""."</option>";
	        						}else{
	        				echo "<option>".""."</option>";
	   			 			echo "<option selected>".substr($opening_get,0,2)."</option>";
	   			 			}
	   			 		}
							print "</select>";

							print "<select name=open2[]>";
								for ($i = 0; $i <= 45; $i+=15) {
									print '<option>'.date('i', strtotime("+{$i} minutes", $t)) . PHP_EOL.'</optin>';
								}
								if(!empty($open2[$value['day']-1])){
	        					foreach($open2 as $hour2 => $ope2){
									if($value['day']-1==$hour2){
									echo "<option>".""."</option>";
	 								print "<option selected>$ope2</option>";
	 							}
	 							}
	 							}elseif($opening_get!=""){
	 								echo "<option>".""."</option>";
	   			 					echo "<option selected>".substr($opening_get,3,2)."</option>";
	   			 				}
	   			
	   							if($value['week']=="6" || $value['week']=="0" ||$re){
	   								if(!empty($open[$value['day']-1]!=00)&&!empty($open2[$value['day']-1])){
	   									echo "<option>".""."</option>";
	 									print "<option selected>$ope2</option>";
	   								}else{
	        						echo "<option selected>".""."</option>";
	        						}
	        					}
							print "</select>";
						?> 
	        		</td>
	         		<td>
	         			<?php 
	        				for($i=0; $i<= $now_end_month; $i++){
	        					print "<select name=close[]>";
									for ($i = 0; $i < 5 * 12 * 24; $i += 60) {	
	   			 						echo "<option>".date('H', strtotime("+{$i} minutes", $t)) . PHP_EOL."</option>";
									}
							if(!empty($close[$value['day']-1])){
	        					foreach($close as $c_hour => $clo){
									if($value['day']-1==$c_hour){
										if($value['week']=="6" || $value['week']=="0" ||$re){
	        						echo "<option selected>".""."</option>";
	        					}
	        					if($clo=="00"){
									echo "<option>".""."</option>";
								}else{
									echo "<option>".""."</option>";
	 								print "<option selected>$clo</option>";
	 							}
							}
							}
							}elseif($closong_get!=""){
								if($value['week']=="6" || $value['week']=="0" ||$re){
	   											echo "<option selected>".""."</option>";
	        						}else{
										echo "<option>".""."</option>";
	   			 						echo "<option selected>".substr($closong_get,0,2)."</option>";
	   			 					}
	   			 				}
				print "</select>";

				print "<select name=close2[]>";
				for ($i = 0; $i <= 45; $i+=15) {
					print '<option>'.date('i', strtotime("+{$i} minutes", $t)) . PHP_EOL.'</optin>';
				}
				if(!empty($close2[$value['day']-1])){
	        		foreach($close2 as $c_hour2 => $clo2){
						if($value['day']-1==$c_hour2){
							echo "<option>".""."</option>";
	 						print "<option selected>$clo2</option>";
	 					}
	 				}
				}elseif($closong_get!=""){
					echo "<option>".""."</option>";
	   			 	echo "<option selected>".substr($closong_get,3,2)."</option>";
	   			 }
	   				if($value['week']=="6" || $value['week']=="0" ||$re){
	   								if(!empty($close[$value['day']-1]!=00)&&!empty($close2[$value['day']-1])){
	   									echo "<option>".""."</option>";
	 									print "<option selected>$clo2</option>";
	   								}else{
	        						echo "<option selected>".""."</option>";
	        						}
	        					}
				print "</select>";
				?>
	        </td>
	        <td>
	        <?
	        //休憩時間
	        //土日祝日の場合
	        	if($value['week']=="6" || $value['week']=="0" ||!empty($re)){
	        		?>
	        		<!-- テーブルに値がある場合 -->
					<select name="rest[]">
						<option value=""></option>
						<?if(!empty($res_1)){?>
						<option value="00" <?= $res_1[$value['day']-1] === '00' ? ' selected' : ''; ?>>00</option>
	        			<option value="01" <?= $res_1[$value['day']-1] === '01' ? ' selected' : ''; ?>>01</option>
	        			<option value="02" <?= $res_1[$value['day']-1] === '02' ? ' selected' : ''; ?>>02</option>
	        			<?}else{
	        				?>
	        				<!-- テーブルに値がない場合 -->
	        				<option value="00">00</option>
	        				<option value="01">01</option>
	        				<option value="02">02</option>
	        			<?}?>
	        		</select>
	        	<?}
	        	//テーブルに休憩時間ありかつ平日
				elseif(!empty($rest[$value['day']-1])){
					//if($value['week']!="6" || $value['week']!="0" ||$re){
					foreach((array)$rest as $r => $res){
						if($value['day']-1==$r){
	 						print $res;
	 					}
	 					}
	 				//}
	 				?><input type="hidden" name="rest[]" value="<?=$res?>"> 
	 				<?
				}else{?>
					<input type="hidden" name="rest[]" value=""> 
				<?}
	        	if($value['week']=="6" || $value['week']=="0" ||!empty($re)){?>
					<select name="rest2[]">
						<option value=""></option>
						<?if(!empty($res_2)){?>
						<option value="00" <?= $res_2[$value['day']-1] === '00' ? ' selected' : ''; ?>>00</option>
	        			<option value="30" <?= $res_2[$value['day']-1] === '30' ? ' selected' : ''; ?>>30</option>
	        			<?}else{?>
	        				<option value="00">00</option>
	        				<option value="30">30</option>
	        			<?}?>
	        		</select>
	        	<?}
				elseif(!empty($rest2[$value['day']-1])){
					//if($value['week']!="6" || $value['week']!="0" ||$re){
					foreach((array)$rest2 as $r2 => $res2){
						if($value['day']-1==$r2){
	 						print $res2;
	 					}
	 					}
	 				//}
	 				?><input type="hidden" name="rest[]" value="<?=$res2?>"> 
	 				<?
				}else{?>
						<input type="hidden" name="rest2[]" value=""> 
					<?}?>
	        </td>
	        <td>
	        <?php 
	        //実働時間
	        if(!empty($total[$value['day']-1])){
	        	foreach($total as $key => $val){
				if($value['day']-1==$key){
					if($val=="00:00"){
						print "";
					}else{
	 					print $val;
	 					}
	 				}
	 			}
			}else{
	           	 print "";
	         }?>
	        </td>
	        <td>
	         	<?
	         	//残業時間
	         	//実働時間ありかつ残業時間ありの場合
	         	if(!empty($total[$value['day']-1]) && !empty($overtime_normal[$value['day']-1])){
	           			if(!empty($re)){
	           				print "";
	           				//日・祝の場合
	           			}elseif($value['week']!="0"){
	           				foreach($overtime_normal as $over_num => $over_all){
	           					if($value['day']-1==$over_num){
	           						if($over_all=="00:00"){
	           							print "";
	           						}else{
									print $over_all;
	           					}
	           				}
	           			}
	           		}
	           	}	
	            ?>
	        </td>
	        <td>

	        <?
	        //深夜残業
	        //日・祝の場合
	       if($value['week']=="0" || !empty($re)){
	       	if(!empty($total[$value['day']-1])){
	           			foreach($total as $total_num3 => $over_total3){
	           				if($value['day']-1==$total_num3){
	           					if($over_total3=="00:00"){
	           						print "";
	           					}else{
										print $over_total3;
	           						}
	           					}
	           				}
	           				}
	           				//日・祝以外、深夜残業がある場合、00:00は非表示
	           			}elseif(!empty($overtime_night[$value['day']-1])){
	        					foreach($overtime_night as $number => $night){
									if($value['day']-1==$number){
										if($night=="00:00"){
											print "";
										}else{
	 									print $night;
	 								}
	 							}
	 						}
	 				
	 						}else{
	        					print "";
	        				}
	        			?>
	       </td>
	       <td>
	        <? 
	        //不足時間
	        if(!empty($Shortage[$value['day']-1])){
	        	foreach($Shortage as $suuji => $Short){
				if($value['day']-1==$suuji){
					if($value['week']=="6" || $value['week']=="0" ||!empty($re)){
	        			print "";
	 				}elseif($Short!="" && $Short=="00:00"){
	 						print "";
	 				}elseif($Short!="" && $Short!="00:00"){
	 						print $Short;
	 				}else{
	 					print "";
	 				}
	 			}
	 			}
			}
	        else{
	        		print "";
	        	}?>
	        </td>
	        <td class="biko">
	         <?
	         //備考
	         //土日祝日は非表示
	        if($value['week']=="6" || $value['week']=="0" ||!empty($re)){
	        	if(!empty($bikou[$value['day']-1])){
	        		print "";
	        	}
	        }else{
	        	if(!empty($bikou[$value['day']-1])){
	        	foreach ((array)$bikou as $n_bikou => $bk) {
	        		if($value['day']-1==$n_bikou){
	        			print $bk;
	        		}
	        	}
	        	}
	        	//エラーメッセージの有無
	        if($err_msg!="" && !empty($check[$value['day']-1])=="NG"){
	        	foreach ((array)$err_msg as $n_msg => $msg) {
	        		if($value['day']-1==$n_msg){
	        			print $msg;
	        	}
	        }
	    }
	    }
	    ?>
	        </td>
	        <td class="kyuka">
	       <!--  土日祝日の場合休暇フラグは非表示 -->
	        <?if($value['week']=="6" || $value['week']=="0" ||!empty($re)){?>
	        	<select name="holiday[]" class="kyuka">
	        		<option value="0"></option>
	        	</select>
	        	<!-- それ以外は表示 -->
	        <?}elseif(empty($holiday[$value['day']-1])){?>
	        	<select name="holiday[]" class="kyuka">
	        		<option value="0"></option>
	        		<option value="1">1</option>
	        		<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
	        	</select>
	        	<!-- テーブルに値がある場合 -->
	        <?}else{
	        	foreach ($holiday as $h_key => $holi) {
	        		if($value['day']-1==$h_key){?>
	        	<select name="holiday[]" class="kyuka">
	        		<option value="0" <?= $holi === '0' ? ' selected' : ''; ?>></option>
	        		<option value="1" <?= $holi === '1' ? ' selected' : ''; ?>>1</option>
	        		<option value="2" <?= $holi === '2' ? ' selected' : ''; ?>>2</option>
					<option value="3" <?= $holi === '3' ? ' selected' : ''; ?>>3</option>
					<option value="4" <?= $holi === '4' ? ' selected' : ''; ?>>4</option>
					<option value="5" <?= $holi === '5' ? ' selected' : ''; ?>>5</option>
	        	</select>
	       <?}
	       }
	       }?>
	        </td>
	        <td class="check">
	        	<?
	        	//チェック結果
	        	if(!empty($check[$value['day']-1])){
	        			foreach ((array)$check as $c_key => $chk) {
	        				if($value['day']-1==$c_key){
	        					print $chk;
	        				}
	        			}
	        		}else{
	        			print "";
	        		}?>
	        </td>
	        <td class="shift">
	        <!-- 土日祝日の場合シフトフラグは非表示 -->
	        <?if($value['week']=="6" || $value['week']=="0" ||!empty($re)){?>
	        	<select name="shift_kinmu[]">
	        		<option value="0"></option>
	        	</select>
	        	<!-- それ以外は表示 -->
	        <?}elseif(empty($shift[$value['day']-1])){?>
	        	<select name="shift_kinmu[]">
	        		<option value="0"></option>
	        		<option value="1">1</option>
	        	</select>
	        	<!-- テーブルにデータがある場合 -->
	        	<? }else{
	        			foreach ($shift as $kinmu => $shift_kinmu) {
	        				if($value['day']-1==$kinmu){?>
	        					<select name="shift_kinmu[]">
	        						<option value="0" <?= $shift_kinmu === '0' ? ' selected' : ''; ?>></option>
	        						<option value="1" <?= $shift_kinmu === '1' ? ' selected' : ''; ?>>1</option>
	        					</select>
	        				<?}
	        		}
	        		}?>
	        </td>
	    </tr>
	<?php }
	 }
	}?>
	<?php if(isset($_POST['show'])==""){?>
	 	<input name="tochu" type="submit" value="保存">
	         </form>
	        <?php 
	}
	?>

	<?php } elseif($_POST['show']==2){
		foreach($aryCalendar as $value){ ?>
	    <?php if($value['day'] != date('j')){ ?>
	    <tr class="week<?php echo $value['week'] ?>">
	    <?php }?>

	        <td>
	            <?php echo $value['day'] ?>
	        </td>
	        <td>
	        	<?php echo $aryWeek[$value['week']] ?>
	        </td>
	        <td>
	           <input type='text' name="naiyou">
	        </td>
	        <form method="post" action="kinmuhyo_check.php">
	        <td>
	        	<?php 
	        	for($i=0; $i<= $now_end_month; $i++){
	        	print "<select name=open[]>";
	        	$t = strtotime('0:00');
				for ($i = 0; $i < 5 * 12 * 24; $i += 60) {
	   			 echo "<option>".date('H', strtotime("+{$i} minutes", $t)) . PHP_EOL."</option>";
				}
				if($opening_get!=""){
	   			 	echo "<option selected>".substr($opening_get,0,2)."</option>";
	   			 }
	   			  if($value['week']=="6"){
	        		echo "<option selected>"."00"."</option>";
	        	}elseif($value['week']=="0"){
	        		echo "<option selected>"."00"."</option>";
		        	}
				print "</select>";

				print "<select name=open2[]>";
				for ($i = 0; $i <= 45; $i+=15) {
					print '<option>'.date('i', strtotime("+{$i} minutes", $t)) . PHP_EOL.'</optin>';
				}
				if($opening_get!=""){
	   			 	echo "<option selected>".substr($opening_get,3,2)."</option>";
	   			 }
	   			  if($value['week']=="6"){
	        		echo "<option selected>"."00"."</option>";
	        	}elseif($value['week']=="0"){
	        		echo "<option selected>"."00"."</option>";
	        	}
				print "</select>";
				?> 
	        </td>
	         <td>
	         <?php 
	        	for($i=0; $i<= $now_end_month; $i++){
	        	print "<select name=close[]>";
				for ($i = 0; $i < 5 * 12 * 24; $i += 60) {	
	   			 echo "<option>".date('H', strtotime("+{$i} minutes", $t)) . PHP_EOL."</option>";
				}
				if($closong_get!=""){
	   			 	echo "<option selected>".substr($closong_get,0,2)."</option>";
	   			 }
	   			  if($value['week']=="6"){
	        		echo "<option selected>"."00"."</option>";
	        	}elseif($value['week']=="0"){
	        		echo "<option selected>"."00"."</option>";
	        	}
				print "</select>";

				print "<select name=close2[]>";
				for ($i = 0; $i <= 45; $i+=15) {
					print '<option>'.date('i', strtotime("+{$i} minutes", $t)) . PHP_EOL.'</optin>';
				}
				if($closong_get!=""){
	   			 	echo "<option selected>".substr($closong_get,3,2)."</option>";
	   			 }
	   			  if($value['week']=="6"){
	        		echo "<option selected>"."00"."</option>";
	        	}elseif($value['week']=="0"){
	        		echo "<option selected>"."00"."</option>";
	        	}
				print "</select>";
				?>
	        </td>
	        <td>
	        <?php
	        	for($i=0; $i<= $now_end_month; $i++){
	        	print "<select name=rest[]>";
				?>
				<option>00</option>_
				<option selected>01</option>
				<option>02</option>
				<?php
				 if($value['week']=="6"){
	        		echo "<option selected>"."00"."</option>";
	        	}elseif($value['week']=="0"){
	        		echo "<option selected>"."00"."</option>";
	        	}
				print "</select>";

	        	print "<select name=rest2[]>";
				for ($i = 0; $i < 60; $i += 30) {
	   			 echo "<option>".date('i', strtotime("+{$i} minutes", $t)) . PHP_EOL."</option>";
				}
				 if($value['week']=="6"){
	        		echo "<option selected>"."00"."</option>";
	        	}elseif($value['week']=="0"){
	        		echo "<option selected>"."00"."</option>";
	        	}
				}
				?>
	        </td>
	        	<? if($total!=""){
	        	foreach($total as $key => $val){
				if($value['day']-1==$key && $key2){
	 				print "<td>".$val.":".$val2."</td>";
	 			}
	 		}
			}else{?>
	           	<?php print"<td>00:00</td>"?>
	        <?php }?>
	         <td>
	         	<?php if(isset($overtime_normal)!=""){?>
	        	<p><?php print $overtime_normal?></p>
	        	<?php }else{?>
	        	<p>00:00</p>
	        	<?php }?>
	        </td>
	        <td>
	        	<?php if(isset($overtime_night)!=""){?>
	        	<p><?php print $overtime_night?></p>
	        	<?php }else{?>
	        	<p>00:00</p>
	        	<?php }?>
	        </td>	
	        <td>
	        	<?php if(isset($Shortage)!=""){?>
	        	<p><?php print $Shortage?></p>
	        	<?php }else{?>
	        	<p>00:00</p>
	        	<?php }?>
	        </td>
	        <td class="kyuka">
	        	<input type='text' name="holiday" class="kyuka">
	        </td>
	        <td class="check">
	        	<p></p>
	        </td>
	    </tr>
	    <?php }

	} 
	}?>
	<?php }?>	
	<?php if(isset($_POST['show'])==2){?>
	 	<input name="tochu" type="submit" value="保存">
	</form>
	        <?php }?>
	</table>

	<table border="1">
			<td class="sum_title"><p>合計</p></td>
			<td>
			<!-- テーブルに格納された実働時間の合計を表示 -->
			<?if($sum_total["total_time"]!=""){
				print $sum_total["total_time"];
				}else{
					print "00:00";
				}?>
			</td>
			<td>
			<!-- テーブルに格納された残業時間の合計を表示 -->
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
			<td>
			<!-- テーブルに格納された深夜残業時間の合計を表示 -->
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
			<td>
			<!-- テーブルに格納された不足時間の合計を表示 -->
			<?if($sum_short["total_time"]!=""){
				if($sum_short["total_time"]!="00:00"){
				print $sum_short["total_time"];
				}else{
					print "00:00";
				}
				}else{
					print "00:00";
				}?>
			</td>
	</table>
	<!-- 土日祝日を足し、足した結果当月日数から引く -->
	 <?php $kyuujitu=array_merge($doyou, $nitisyuku);
	 //営業日を算出
	$eigyoubi = $value['day']-count($kyuujitu);
	?>
	</body>
	</html>
