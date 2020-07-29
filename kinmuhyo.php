<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/css/kinmuhyo.css">
<title>勤務表</title>
</head>
<body>
<?
//セッションが開始されていなければセッションを開始する。
if(!isset($_SESSION)){
	session_start();
}
session_regenerate_id(true);
// ログイン状態のチェック
// 不正ログインの場合ログイン画面に遷移させる
if (isset($_SESSION["login"])==false) {
		header("Location: staff_login.php");
		exit();
}
//ファイル読み込み	(DB接続クラス)
require'kinmu_common.php';

//ユーザー情報の読み込み S////////
//ログイン画面からセッションで引き継がれた値を変数に格納
//社員テーブルS///
$result=$_SESSION['result'];
//社員テーブルに格納されている該当の社員番号
$staff_number=$result['staff_number'];
//社員テーブルに格納されている該当の社員名
$staff_name=$result['familyname'].$result['firstname'];
//管理者かそうでないか（0=一般社員 1=管理者）
$admin_flag=$result['admin_flag'];
//メールアドレス
$email=$result['email'];
//現在の有給残数
$yukyu=$result['holiday_with_pay'];

//社員テーブルE//

//メソッドを参照する(勤務地テーブル)S///
$BELONGSS= kinmu_common::BELONGSS($result['staff_number']);
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
//勤務地テーブルE///

//メソッドを参照する（サマリーテーブル）S////
$kinmuhyo_summary= kinmu_common::kinmuhyo($result['staff_number']);
$work_id=$kinmuhyo_summary['work_ID'];
$_SESSION['work_id']=$work_id;
//勤務地のみ抽出
$work_tbl= kinmu_common::work_tbl($work_id);
//サマリーテーブルE///
//メソッドを参照する（勤務情報）S////
$kinmuhyo_attendance= kinmu_common::Attendance($result['staff_number']);
//メソッドを参照し、値があった場合
//それぞれの変数に値を格納
if($kinmuhyo_attendance!=""){
	foreach ($kinmuhyo_attendance as $key => $value) {
		//作業内容
		$naiyou[$key]=$value['content'];
		//桁数指定
		//始業時間
		$open[$key]=substr($value['opening_hours'],0,2);
		$open2[$key]=substr($value['opening_hours'],3,2);
		//桁数指定
		//終業時間
		$close[$key]=substr($value['closing_hours'],0,2);
		$close2[$key]=substr($value['closing_hours'],3,2);
		//桁数指定
		//休憩時間
		$weekday_rest[$key]=substr($value['break_time'],0,5);

		$holiday_rest_hour[$key]=substr($value['break_time'],0,2);
		$holiday_rest_min[$key]=substr($value['break_time'],3,2);
		//実働時間・桁数指定
		$total[$key]=substr($value['total'],0,5);
		//残業時間・桁数指定
		$overtime_normal[$key]=substr($value['overtime_normal'],0,5);
		//深夜残業時間・桁数指定
		$overtime_night[$key]=substr($value['overtime_night'],0,5);
		//不足時間・桁数指定
		$Shortage[$key]=substr($value['short'],0,5);
		//備考コメント
		$bikou[$key]=$value['bikou'];
		//休暇フラグ
		$holiday[$key]=$value['vacation'];
		//チェック結果
		$check[$key]=$value['check_result'];
		//シフトフラグ
		$shift[$key]=$value['shift'];
		//出勤日数の算出
	$syukkin = array_count_values($check);
	$syukkin_nissuu=$syukkin["OK"];
	}
}

///メソッドを参照する(勤務テーブル)E///

//メソッド参照
//実働合計(勤務表テーブルの読み込み)S////
$sum_total= kinmu_common::sum_total($result['staff_number']);
//実働合計(勤務表テーブルの読み込み)E///
//残業合計(勤務表テーブルの読み込み)S///
$sum_overtime= kinmu_common::sum_overtime($result['staff_number']);
//残業合計(勤務表テーブルの読み込み)E///
//深夜残業合計(勤務表テーブルの読み込み)S///
$sum_overtime_night= kinmu_common::sum_overtime_night($result['staff_number']);
//深夜残業合計(勤務表テーブルの読み込み)E///
//不足合計(勤務表テーブルの読み込み)S///
$sum_short= kinmu_common::sum_short($result['staff_number']);
//不足合計(勤務表テーブルの読み込み)E///
//祝日取得(祝日テーブルの読み込み)S////
$kinmuhyo_holiday= kinmu_holiday::Holiday("");
//祝日テーブルE///
//ユーザー情報の読み込み E////////
//カレンダー作成S///
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
	//配列に曜日をセット
	$aryWeek = ['日', '月', '火', '水', '木', '金', '土'];
	//1日から月末日までループ
	for ($i = 1; $i <= $now_end_month; $i++){
		//カレンダー配列(連想配列)に日にちを代入
	    $aryCalendar[$i]['day'] = $i;
	    //カレンダー配列(連想配列)の日にちに合致する曜日を代入
	    $aryCalendar[$i]['week'] = date('w', strtotime($year.$month.sprintf('%02d', $i)));
	    //YMD形式に日付をなおし、祝日テーブルの値と比較する
	    $this_month[$i] = $year."/".$month."/".sprintf('%02d',$i);
	    $syuku=array_intersect($kinmuhyo_holiday, $this_month);
	    $judge[]=sprintf("%02d", $aryCalendar[$i]['day']);
	}
	if(!empty($syuku)){
		//祝日日数を出す。
		foreach ($syuku as $value) {
			$syuku_day[]=substr($value,8,2);
		}
		//祝日日数を計算
		$sum_syuku=array_intersect($syuku_day,$judge);
		//祝日日数を計算
		$count_syuku=count($sum_syuku);
	}
	//カレンダー作成E///

	//チェックの結果エラーメッセージがあれば変数に格納
	if(isset($_SESSION['err_msg'])){
		$err_msg=$_SESSION['err_msg'];
		$check=$_SESSION['check'];
	}
?>
<div class="img">
	<img src="/img/image_2020_4_10.png" height="100" width="100" alt="ロゴ" align="right" >
</div>
<!-- 該当月の勤務表タイトルの表示 -->
<h3><?=$now_month?>月分勤務表</h3>
<!--end-->
<!-- 氏名表示エリア -->
<table border="1">
	<tr>
		<th>　氏名　
			<td><?php print $staff_name;?>
			</td>
		</th>
	</tr>
</table>
<!--end-->
<?
//月選択テキストボックス
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
<!-- 該当の社員番号に紐づく勤務地IDから算出した勤務地情報を表示-->
<table border="1">
	<tr>
		<th>始業</th>
			<?if($opening_get!=""){?>
				<td><?=$opening_get?></td>
			<?}else{?>
				<td>
					<input type="time" name="work_start" value="09:00" step="900">
				</td>
			<?}?>
	</tr>
	<tr>
		<th>終業</th>
			<?if($closong_get!=""){?>
				<td><?=$closong_get ?></td>
			<?}else{?>
				<td>
					<input type="time" name="work_end" value="18:00" step="900">
				</td>
			<?}?>
	</tr>
	<tr>
		<th>勤務地</th>
			<?if($work_id!=""){?>
				<td><?=$work_name ?></td>
			<?}else{?>
				<td>
					<? for ($i=0; $i < count($work_tbl); $i++) {
							print "<select name='work'>";
								print "<option>".""."</option>";
								print "<option>".$work_tbl."</option>";
							print "</select>";
						}?>
				</td>
			<?}?>
	</tr>
</table>
<!-- 勤務表サマリー画面へ遷移 -->
	<form method="post" action="kinmuhyo_summary.php">
		<!-- <a href="kinmuhyo_summary.php">サマリー情報を見る</a> -->
		<input type="submit" value="サマリー情報を見る">
	</form>
<!-- 勤務表提出確認画面へ遷移 -->
	<form method="post" action="kinmuhyo_done.php">
		<input name="tochu" type="submit" value="管理者へ提出">
	</form>
	<!-- カレンダークラス -->
	<!--kinmuhyo_checkに値を投げる-->
	<form method="post" action="kinmuhyo_check.php">
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
	    <? foreach($aryCalendar as $value){
			//祝日の取得
				if(!empty($sum_syuku)){
	        		$syukujitu_count=in_array(sprintf("%02d", $value['day']),$sum_syuku);
	    		}?>
	    		<!-- 取得した該当の曜日クラスを割り当てる -->
				<tr class="week<?php echo $value['week'] ?>">
				<!-- 祝日の場合日曜クラスを適用する -->
				<?if(!empty($syukujitu_count)){
					$value['week']="0";?>
					<tr class="week<?php echo $value['week'] ?>">
				<?}?>
	    		<? if($value['day'] != date('j')){ ?>
	    		<?}
	    			//土日の度にカウントする(営業日算出のため)
	    		 	if($value['week']=="6"){
	    				$doyou[]=$i++;
	    			}elseif($value['week']=="0"){
	    				$nitisyuku[]=$i++;
	    			}?>
	        	<td>
	            	<?
	            	//日付を表示
	            	echo $value['day'];
	            	?>
	        	</td>
	        	<td>
	        		<!-- 月の情報をチェックに渡す -->
	        		<input type='hidden' name="month" value="<?=$month?>">
	        		<!-- 週の情報をチェックに渡す -->
	        		<input type='hidden' name="week[]" value="<?=$aryWeek[$value['week']]?>">
	        		<!-- 祝日でない場合 -->
	        		<?
	        		if(empty($syukujitu_count)){
	        			echo $aryWeek[$value['week']];
	        			//祝日の場合
	        		}else{
	        			$value['week']=date('w', strtotime($year.$month.$value['day']));
	        			echo $aryWeek[$value['week']];
	        		}?>
	        	</td>
	        	<td>
	        	<? if($value['week']=="0" || $value['week']=="6" || !empty($syukujitu_count)){
	        		//作業内容入力欄
	        			if(!empty($naiuou[$value['day']-1])){?>
	        				<input type='text' name="naiyou[]" value="<? foreach($naiyou as $key =>$sagyo){
	           				if($value['day']-1==$key){
	           							print $sagyo;
	           				}
	        			}?>">
	        			<?}else{?>
	        				<input type='text' name="naiyou[]">
	           			<?}?>
	        		</td>
	        		<td>
	        	<?
	        	//始業時間の入力域
					print "<select name=open[]>";
					print "<option></option>";
					for($i=0;$i<24;$i++){
						//テーブルに始業時間(時)がある場合、初期値を設定する
						if(isset($open)){
							$selected=($open[$value['day']-1]==sprintf("%02d",$i))?" selected":"";
						}
						print "<option value=\"".sprintf("%02d",$i)."\"{$selected}>".sprintf("%02d",$i)."</option>";
					}
					print "</select>\n";
					print "<select name=open2[]>";
					print "<option></option>";
					for($i=0;$i<60;$i+=15){
						//テーブルに始業時間(分)がある場合、初期値を設定する
						if(isset($open2)){
							$selected=($open2[$value['day']-1]==sprintf("%02d",$i))?" selected":"";
						}
						print "<option value=\"".sprintf("%02d",$i)."\"{$selected}>".sprintf("%02d",$i)."</option>";
					}
					print "</select>\n";
					?>
	        		</td>
	        		<td>
	        		<?
	        		//終業時間の入力域
	        		print "<select name=close[]>";
							print "<option></option>";
					for($i=0;$i<24;$i++){
						//テーブルに終業時間(時)がある場合、初期値を設定する
						if(isset($close)){
							$selected=($close[$value['day']-1]==sprintf("%02d",$i))?" selected":"";
						}
						print "<option value=\"".sprintf("%02d",$i)."\"{$selected}>".sprintf("%02d",$i)."</option>";
					}
					print "</select>\n";
					print "<select name=close2[]>";
					print "<option></option>";
					for($i=0;$i<60;$i+=15){
						//テーブルに終業時間(分)がある場合、初期値を設定する
						if(isset($close2)){
							$selected=($close2[$value['day']-1]==sprintf("%02d",$i))?" selected":"";
						}
						print "<option value=\"".sprintf("%02d",$i)."\"{$selected}>".sprintf("%02d",$i)."</option>";
					}
					print "</select>\n";
					?>
	        		</td>
	        		<td>
	        	<?
	        	//休憩時間の入力域
	        	print "<select name=rest[]>";
						print "<option></option>";
					for($i=0;$i<3;$i++){
						//テーブルに休憩時間(時)がある場合、初期値を設定する
						if(isset($holiday_rest_hour)){
							$selected=($holiday_rest_hour[$value['day']-1]==sprintf("%02d",$i))?" selected":"";
						}
						print "<option value=\"".sprintf("%02d",$i)."\"{$selected}>".sprintf("%02d",$i)."</option>";
					}
					print "</select>\n";
					print "<select name=rest2[]>";
					print "<option></option>";
					for($i=0;$i<60;$i+=30){
						//テーブルに休憩時間(分)がある場合、初期値を設定する
						if(isset($holiday_rest_min)){
							$selected=($holiday_rest_min[$value['day']-1]==sprintf("%02d",$i))?" selected":"";
						}
						print "<option value=\"".sprintf("%02d",$i)."\"{$selected}>".sprintf("%02d",$i)."</option>";
					}
				print "</select>\n";
				?>
	        	</td>
	        	<td>
	        	<?
	        	//実働時間の表示域
	        	 if(isset($total[$value['day']-1])){
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
	        	//残業時間の表示域
	        	if(isset($overtime_normal[$value['day']-1])){
	        		if($value['week']=="6"){
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
	        	}?>
	        	</td>
	        	<td>
	        	<?
	        	//深夜残業の表示域
	        	if(isset($total[$value['day']-1])){
	        		if($value['week']!="6"){
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
	           	}?>
	        	</td>
	        	<td>
	        	<?
	        	//不足の表示域
	        	//土日祝日の場合、不足はあり得ないため表示しない。
	        	print "";?>
	        	</td>
	        	<td class="biko">
	        	<?
	        	//土日祝日の場合、備考はないため表示しないが、エラーの場合があるため、エラーメッセージの処理を記載
	        	if(isset($err_msg[$value['day']-1])){
	        		foreach ((array)$err_msg as $n_msg => $msg) {
	        			if($value['day']-1==$n_msg){
	        				print $msg;
	        			}
	        		}
	        	}?>
	        	</td>
	        	<td class="kyuka">
	        	<!-- 土日祝日の場合、休暇フラグは不要 -->
	        		<select name="holiday[]" class="kyuka">
	        			<option value="0"></option>
	        		</select>
	        	</td>
	        	<td class="check">
	        	<?
	        	//チェック結果表示域
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
	        	 <!-- 土日祝日の場合シフトフラグは不要 -->
	        	 <select name="shift_kinmu[]">
	        		<option value="0"></option>
	        	</select>
	        	</td>
					<!-- 土日祝日以外 -->
	       	<?}else{?>
	       	<?//作業内容入力欄
	        	if(isset($naiuou[$value['day']-1])){?>
	        		<input type='text' name="naiyou[]" value="<? foreach($naiyou as $key =>$sagyo){
	           			if($value['day']-1==$key){
	           				print $sagyo;
	           			}
	        		}?>">
	        	<?}else{?>
	        		<input type='text' name="naiyou[]">
	           	<?}?>
	         <td>
	        <?
	        //始業時間の入力域
				print "<select name=open[]>";
				print "<option></option>";
				for($i=0;$i<24;$i++){
					//テーブルに始業時間(時)がある場合、初期値を設定する
					if(isset($open[$value['day']-1])){
						$selected=($open[$value['day']-1]==sprintf("%02d",$i))?" selected":"";
					}else{
						//テーブルに始業時間(時)がない場合、所定始業時間をデフォルト
						$selected=(substr($opening_get,0,2)==sprintf("%02d",$i))?" selected":"";
					}
						print "<option value=\"".sprintf("%02d",$i)."\"{$selected}>".sprintf("%02d",$i)."</option>";
				}
				print "</select>\n";
				print "<select name=open2[]>";
				print "<option></option>";
				for($i=0;$i<60;$i+=15){
					//テーブルに始業時間(分)がある場合、初期値を設定する
					if(isset($open2[$value['day']-1])){
						$selected=($open2[$value['day']-1]==sprintf("%02d",$i))?" selected":"";
					}else{
					//テーブルに始業時間(分)がない場合、所定始業時間をデフォルト
						$selected=(substr($opening_get,3,2)==sprintf("%02d",$i))?" selected":"";
					}
						print "<option value=\"".sprintf("%02d",$i)."\"{$selected}>".sprintf("%02d",$i)."</option>";
				}
				print "</select>\n";
			?>
			</td>
			<td>
			<?
	        //終業時間の入力域
	        	print "<select name=close[]>";
						print "<option></option>";
					for($i=0;$i<24;$i++){
						//テーブルに終業時間(時)がある場合、初期値を設定する
						if(isset($close[$value['day']-1])){
							$selected=($close[$value['day']-1]==sprintf("%02d",$i))?" selected":"";
						}else{
							//テーブルに終業時間(時)がない場合、所定始業時間をデフォルト
							$selected=(substr($closong_get,0,2)==sprintf("%02d",$i))?" selected":"";
						}
						print "<option value=\"".sprintf("%02d",$i)."\"{$selected}>".sprintf("%02d",$i)."</option>";
					}
					print "</select>\n";
					print "<select name=close2[]>";
					print "<option></option>";
					for($i=0;$i<60;$i+=15){
						//テーブルに終業時間(分)がある場合、初期値を設定する
						if(isset($close2[$value['day']-1])){
							$selected=($close2[$value['day']-1]==sprintf("%02d",$i))?" selected":"";
						}else{
						//テーブルに終業時間(分)がない場合、所定始業時間をデフォルト
							$selected=(substr($closong_get,3,2)==sprintf("%02d",$i))?" selected":"";
						}
						print "<option value=\"".sprintf("%02d",$i)."\"{$selected}>".sprintf("%02d",$i)."</option>";
					}
				print "</select>\n";
				?>
			</td>
			<td>
			<?
						if(isset($weekday_rest[$value['day']-1])){
							foreach($weekday_rest as $r => $res){
								if($value['day']-1==$r){
									print $res;
								}
							}
						}else{
							print "";
							
				}?>
				<input type="hidden" name="rest[<?$value['day']-1?>]" value="">
				<input type="hidden" name="rest2[<?$value['day']-1?>]" value="">
			</td>
			<td>
			<?
			//実働時間の表示域
	        if(isset($total[$value['day']-1])){
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
	        	//残業時間の表示域
	        	if(isset($overtime_normal[$value['day']-1])){
	        			foreach($overtime_normal as $over_num => $over_all){
	           				if($value['day']-1==$over_num){
	           					if($over_all=="00:00"){
	           						print "";
	           					}else{
									print $over_all;
	        					}
	        				}
	        			}
	        	}?>
			</td>
			<td>
			<?
	        //深夜残業の表示域
	        	if(isset($overtime_night[$value['day']-1])){
							foreach($overtime_night as $number => $night){
								if($value['day']-1==$number){
									if($night=="00:00"){
										print "";
									}else{
										print $night;
									}
	           		}
	           	}
	          }?>
	        </td>
	        <td>
	        <?
	        	//不足の表示域
						if(isset($Shortage[$value['day']-1])){
							foreach($Shortage as $suuji => $Short){
								if($value['day']-1==$suuji){
									if($Short!="" && $Short=="00:00"){
					 						print "";
					 				}elseif($Short!="" && $Short!="00:00"){
					 						print $Short;
									}
								}
							}
						}
					?>
	        </td>
	        <td class="biko">
	        	<?
						//備考メッセージ
	        if(isset($bikou[$value['day']-1])){
						foreach ((array)$bikou as $n_bikou => $bk) {
	        		if($value['day']-1==$n_bikou){
	        			print $bk;
	        		}
						}
					}
					//エラーメッセージ
	        	if(isset($err_msg[$value['day']-1])){
	        		foreach ($err_msg as $n_msg => $msg) {
	        			if($value['day']-1==$n_msg){
	        				print $msg;
	        			}
	        		}
	        	}?>
	        </td>
	        <td class="kyuka">
	         <?
					 //休暇フラグ
					 print "<select name=holiday[] class=kyuka>";
					 print "<option value='0'></option>";
				 	 for($i=1;$i<6;$i++){
					 if(isset($holiday[$value['day']-1])){
						 $selected=($holiday[$value['day']-1]==$i)?" selected":"";
					 }
					 print "<option value=\"".$i."\"{$selected}>".$i."</option>";
				 }
				 print "</select>\n";
				?>
	        </td>
	        <td class="check">
	        	<?
	        	//チェック結果表示域
	        	if(!empty($check[$value['day']-1])){
	        		foreach ($check as $c_key => $chk) {
	        			if($value['day']-1==$c_key){
	        				print $chk;
	        			}
	        		}
	        	}else{
	        		print "";
	        	}?>
	        </td>
	        <td class="shift">
						<?
						print "<select name=shift_kinmu[]>";
						print "<option value='0'></option>";
						for($i=1;$i<2;$i++){
						if(isset($shift[$value['day']-1])){
							$selected=($shift[$value['day']-1]==$i)?" selected":"";
						}
						print "<option value=\"".$i."\"{$selected}>".$i."</option>";
					}
					print "</select>\n";
					?>
	        </td>
	    </tr>
	 <?}}?>
	<input name="tochu" type="submit" value="保存">
	</form>
</table>
<table border="1">
	<td class="sum_title"><p>合計</p></td>
		<td>
		<!-- テーブルに格納された実働時間の合計を表示 -->
			<?if($sum_total["total_time"]!=""){
				$sum_total["total_time"] = substr($sum_total["total_time"], 0, -3); 
				print $sum_total["total_time"];
			}else{
				print "00:00";
			}?>
		</td>
		<td>
		<!-- テーブルに格納された残業時間の合計を表示 -->
			<?if($sum_overtime["total_time"]!=""){
				if($sum_overtime["total_time"]!="00:00"){
					$sum_overtime["total_time"] = substr($sum_overtime["total_time"], 0, -3); 
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
					$sum_overtime_night["total_time"] = substr($sum_overtime_night["total_time"], 0, -3); 
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
					$sum_short["total_time"] = substr($sum_short["total_time"], 0, -3); 
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
<?
$kyuujitu=array_merge($doyou, $nitisyuku);
//営業日を算出
$eigyoubi = $value['day']-count($kyuujitu);
?>
</body>
</html>
<?
unset($_SESSION['err_msg']);
unset($_SESSION['check']);
?>