<!DOCTYPE HTML PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="/css/kinmuhyo.css">
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
// ログイン状態のチェック
// 不正ログインの場合ログイン画面に遷移させる
//if($_SERVER['HTTP_REFERER']!="https://www.pros-service.co.jp/kinmu/list_of_members.php"){
if(isset($_POST["kinmuhyou"])){
	if(isset($_SESSION["login"])==false) {
		header("Location:staff_login.php");
		exit();
	}
}

//勤務表保存後はセッション情報のcheck,yukyu_errを初期化する
//if($_SERVER['HTTP_REFERER']=="https://www.pros-service.co.jp/kinmu/kinmuhyo_done.php"){
if($_SERVER['HTTP_REFERER']=="http://localhost:8080/kinmuhyo/kinmuhyo_done.php"){
	unset($_SESSION['check']);
	unset($_SESSION["yukyu_err"]);
}
//ファイル読み込み	(DB接続クラス)
//ユーザー情報の読み込み S////////
//ログイン画面からセッションで引き継がれた値を変数に格納
//社員テーブルS///
//if($_SERVER['HTTP_REFERER']!="https://www.pros-service.co.jp/kinmu/list_of_members.php"){
if($_SERVER['HTTP_REFERER']!="http://localhost:8080/kinmuhyo/list_of_members.php"){
	$result=$_SESSION['result'];
	require'kinmu_common.php';
	//管理者かそうでないか（0=一般社員 1=管理者）
	$admin_flag=$result['admin_flag'];
	//メールアドレス
	$email=$result['email'];
}
//社員テーブルに格納されている該当の社員番号
$staff_number=$result['staff_number'];
//社員テーブルに格納されている該当の社員名
$staff_name=$result['familyname'].$result['firstname'];

//該当月の勤務地IDを取得する
if(date("Y-").$_SESSION["month"].date("-01") > $result["old_end_month"]){
	$work_id=$result['new_work_id'];
}else{
	$work_id=$result['old_work_id'];
}
$_SESSION['work_id']=$work_id;
//社員テーブルE//

//メソッドを参照する（サマリーテーブル）S////
$kinmuhyo_summary= kinmu_common::kinmuhyo($result['staff_number']);

$year_and_month=$kinmuhyo_summary['year_and_month'];
//現在の有給残数
$yukyu=$kinmuhyo_summary['remaining_paid_days'];
$_SESSION["yukyu"]=$yukyu;
//勤務地のみ抽出
$work_tbl= kinmu_common::work_tbl($work_id);
//サマリーテーブルE///
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
//勤務地テーブルE///

//メソッドを参照する（勤務情報）S////
$kinmuhyo_attendance= kinmu_common::Attendance($result['staff_number']);
//メソッドを参照し、値があった場合
//それぞれの変数に値を格納
if($kinmuhyo_attendance!=""){
	foreach ($kinmuhyo_attendance as $key => $value) {
		//作業内容
		$naiyou[$key]=$value['content'];
		//始業時間のAMPMのフラグ
		$open_ampm[$key]=$value['open_ampm'];
		//桁数指定
		//始業時間
		$open[$key]=substr($value['opening_hours'],0,5);
		$open4[$key]=substr($value['opening_hours'],0,5);
		//終業時間のAMPMのフラグ
		$close_ampm[$key]=$value['close_ampm'];
		//桁数指定
		//終業時間
		$close[$key]=substr($value['closing_hours'],0,5);
		$close4[$key]=substr($value['closing_hours'],0,5);
		//桁数指定
		//休憩時間
		$weekday_rest[$key]=substr($value['break_time'],0,5);
		$holiday_rest_hour[$key]=substr($value['break_time'],0,5);
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
		$check_result[$key]=$value['check_result'];
		//シフトフラグ
		$shift[$key]=$value['shift'];
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

//先月のサマリー情報がない場合は社員テーブルからとってくる
//カレンダー作成S///
//タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');
//今月・先月の判定
//if($_SERVER['HTTP_REFERER']!="https://www.pros-service.co.jp/kinmu/list_of_members.php"){
if($_SERVER['HTTP_REFERER']!="http://localhost:8080/kinmuhyo/list_of_members.php"){
	if(isset($_SESSION["show"])){
		if($_SESSION["show"]=="1"){
			//システム日付(年)を取得
			$year=date('Y');
			//システム日付(月)を取得
			$month=date('m');
			//今月頭を取得
			$now_month = date('Y-m', strtotime(date('Y-m-01')));
			$comparison_month= date('Y-m-d', strtotime(date('Y-m-01')));
			//月末日を取得
			$now_end_month = date('t', strtotime($now_month.'-'.'01'));
	}else{
		//システム日付(年)を取得
		$year=date('Y');
		//システム日付(月)を取得
		$month=date('m',strtotime('-1 month'));
		//先月頭
		$now_month = date('Y-m', strtotime(date('Y-m-01') . '-1 month'));
		$comparison_month= date('Y-m-d', strtotime(date('Y-m-01') . '-1 month'));
		//先月末日を取得
		$now_end_month = date('t', strtotime($now_month.'-'.'01'));
		}
	}elseif(empty($_POST['show'])|| $_POST['show']=="1"){
		//システム日付(年)を取得
		$year=date('Y');
		//システム日付(月)を取得
		$month=date('m');
		//今月頭を取得
		$now_month = date('Y-m', strtotime(date('Y-m-01')));
		$comparison_month= date('Y-m-d', strtotime(date('Y-m-01')));
		//月末日を取得
		$now_end_month = date('t', strtotime($now_month.'-'.'01'));
	}else{
		//システム日付(年)を取得
		$year=date('Y');
		//システム日付(月)を取得
		$month=date('m',strtotime('-1 month'));
		//先月頭
		$now_month = date('Y-m', strtotime(date('Y-m-01') . '-1 month'));
		$comparison_month= date('Y-m-d', strtotime(date('Y-m-01') . '-1 month'));
		//先月末日を取得
		$now_end_month = date('t', strtotime($now_month.'-'.'01'));
	}
}else{
	//システム日付(年)を取得
	$year=substr($now_month2,0,4);
	//システム日付(月)を取得
	$month=substr($now_month2,5,7);
	//今月頭を取得
	$now_month = date('Y-m-d', strtotime('first day of ' . $now_month2));
	$comparison_month= date('Y-m-d', strtotime('first day of ' . $now_month2));
	//月末日を取得
	$now_end_month = date('Y-m-d', strtotime('last day of ' . $now_month2));
	$now_end_month = substr($now_end_month, -2);
}

$_SESSION['month']=$month;
//配列に曜日をセット
$aryWeek = ['日', '月', '火', '水', '木', '金', '土'];
//1日から月末日までループ
for ($i = 1; $i <= $now_end_month; $i++){
	//カレンダー配列(連想配列)に日にちを代入
	$aryCalendar[$i]['day'] = $i;
	//カレンダー配列(連想配列)の日にちに合致する曜日を代入
	$aryCalendar[$i]['week'] = date('w', strtotime(substr($now_month,0,4).$month.sprintf('%02d', $i)));
	//YMD形式に日付をなおし、祝日テーブルの値と比較する
	$this_month[$i] = substr($now_month,0,4)."/".$month."/".sprintf('%02d',$i);
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
}
?>
<div class="img">
<img src="/img/image_2020_4_10.png" height="100" width="100" alt="ロゴ" align="right" >
</div>
<!-- 該当月の勤務表タイトルの表示 -->
<? //if($_SERVER['HTTP_REFERER']!="https://www.pros-service.co.jp/kinmu/list_of_members.php"){
if($_SERVER['HTTP_REFERER']!="http://localhost:8080/kinmuhyo/list_of_members.php"){?>
<h3><?=$now_month?>月分勤務表</h3>
<?$_SESSION["first_date"]=$now_month."-01";
}else{?>
<h3><?php print $now_month1?>月分勤務表</h3>
<?}?>
<!--end-->
<!-- 有給休暇エラー -->
<?php
if(isset($_SESSION["yukyu_err"])){
print '<FONT COLOR="red">'.$_SESSION["yukyu_err"];
}elseif(isset($check_result)){
if(in_array('NG', $check_result)){
print "<FONT COLOR=\"red\"> 入力内容に不備があります。チェック結果・備考欄を確認してください</FONT>";
}
}?>
</FONT>
<!-- 氏名表示エリア -->
<?//if($_SERVER['HTTP_REFERER']!="https://www.pros-service.co.jp/kinmu/list_of_members.php"){
if($_SERVER['HTTP_REFERER']!="http://localhost:8080/kinmuhyo/list_of_members.php"){?>
<table border="1">
<tr>
<th>　氏名　
<td><?php print $staff_name;?>
</td>
</th>
</tr>
</table>
<?php
//月選択テキストボックス
$selected['show']=array_fill(1,2,"");
$show=filter_input(INPUT_POST,"show");
$selected["show"][$show]="selected";
if(isset($_SESSION["show"])){
$selected["show"][$_SESSION["show"]]="selected";
}
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
<? if($work_id!=""){?>
<td><?=$work_name ?></td>
<?}else{?>
<td>
<?php for ($i=0; $i < count($work_tbl); $i++) {
print "<select name='work'>";
print "<option>".""."</option>";
print "<option>".$work_tbl."</option>";
print "</select>";
}?>
</td>
<?}?>
</tr>
</table>
<?}?>
<?//if($_SERVER['HTTP_REFERER']!="https://www.pros-service.co.jp/kinmu/list_of_members.php"){
if($_SERVER['HTTP_REFERER']!="http://localhost:8080/kinmuhyo/list_of_members.php"){?>
<!-- 勤務表サマリー画面へ遷移 -->
<form method="post" action="kinmuhyo_summary.php">
<input type="submit" value="サマリー情報を見る">
</form>
<!-- ユーザー切り替え画面へ遷移 -->
<button class="done" type=“button” onclick="location.href='switch.php'">戻る</button>
<?}?>
<!-- カレンダークラス -->
<!--kinmuhyo_checkに値をpost-->
<?if($_SERVER['HTTP_REFERER']!="http://localhost:8080/kinmuhyo/list_of_members.php"){?>
<form method="post" action="check.php">
<?}else{?>
	<form method="post" action="preview.php">
<?}?>
<table class="calender_column">
<?//if($_SERVER['HTTP_REFERER']!="https://www.pros-service.co.jp/kinmu/list_of_members.php"){
if($_SERVER['HTTP_REFERER']!="http://localhost:8080/kinmuhyo/list_of_members.php"){?>
<td class="stiky">
<p>日</p>
</td>
<td class="stiky">
<p>曜</p>
</td>
<td class="stiky">
<p>作業内容</p>
</td>
<td class="stiky">
<p>始業</p>
</td>
<td class="stiky">
<p>終業</p>
</td>
<td class="stiky">
<p>休憩</p>
</td>
<td class="stiky">
<p>実働</p>
</td>
<td class="stiky">
<p>普通残業</p>
</td>
<td class="stiky">
<p>深夜残業</p>
</td>
<td class="stiky">
<p>不足</p>
</td>
<td class="stiky">
<p>備考</p>
</td>
<td class="stiky kyuka">
<p>1.有給  4.前休</p>
<p>2.振休  5.後休</p>
<p>3.特休  6.欠勤</p>
</td>
<td class="stiky check">
<p>チェック結果</p>
</td>
<td  class="stiky shift">
<p>シフト</p>
</td>
<?}else{?>
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
<td  class="shift">
<p>シフト</p>
</td>
<?}
foreach($aryCalendar as $value){
//祝日の取得
if(!empty($sum_syuku)){
$syukujitu_count=in_array(sprintf("%02d", $value['day']),$sum_syuku);
}?>
<!-- 取得した該当の曜日クラスを割り当てる -->
<tr class="week<?php echo $value['week']?>">
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
<?php
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
<?php
if(empty($syukujitu_count)){
echo $aryWeek[$value['week']];
//祝日の場合
}else{
$value['week']=	date('w', strtotime(substr($now_month,0,4).$month.sprintf('%02d', $value['day'])));
echo $aryWeek[$value['week']];
}?>
</td>
<?//if($_SERVER['HTTP_REFERER']!="https://www.pros-service.co.jp/kinmu/list_of_members.php"){
if($_SERVER['HTTP_REFERER']!="http://localhost:8080/kinmuhyo/list_of_members.php"){?>
<td>
<?
if($value['week']=="0" || $value['week']=="6" || !empty($syukujitu_count)){
//作業内容入力欄
if(!empty($naiyou[$value['day']-1])){?>
<? foreach($naiyou as $key =>$sagyo){
if($value['day']-1==$key){?>
<textarea name="naiyou[]" rows="3" cols="20" maxlength="20"><?=$sagyo;?></textarea>
<?}
}
}else{?>
<!-- <input type='text' name="naiyou[]"> -->
<textarea name="naiyou[]" rows="3" cols="20" maxlength="20"></textarea>
<?}?>
</td>
<td>
<?
//始業時間
//AM/PM選択ボタン
print "<select name=open_ampm[]>";
print "<option></option>";
if($open_ampm[$value['day']-1] == 1){
	print '<option value = 1 selected>AM</option>
	<option value = 2>PM</option>;
	<option value = 3>AP</option>';
}elseif($open_ampm[$value['day']-1] == 2){
	print '<option value = 1 >AM</option>
	<option value = 2 selected>PM</option>;
	<option value = 3>AP</option>';
}elseif($open_ampm[$value['day']-1] == 3){
	print '<option value = 1>AM</option>
	<option value = 2>PM</option>;
	<option value = 3 selected>AP</option>';
}else{
	print '<option value = 1>AM</option>
	<option value = 2>PM</option>;
	<option value = 3>AP</option>';
}
print "</select>";

$t = Time_select(strtotime('00:00'));
print "<select name=open[]>";
print "<option></option>";
for ($i=0; $i < count($t); $i++) {
//テーブルに始業時間(時)がある場合、初期値を設定する
if(isset($open[$value['day']-1])){
	$selected=($open[$value['day']-1]==$t[$i])?" selected":"";
}
elseif(isset($err_msg)){
	$selected=($_SESSION['open'][$value['day']-1]==$t[$i])?" selected":"";
}
else{
//テーブルに始業時間(時)がない場合、所定始業時間をデフォルト
$selected = "";
}
print "<option value=\"".$t[$i]."\"{$selected}>".$t[$i]."</option>";
}?>
</select>
</td>
<!--終業時間の入力欄!-->
<td>
<?
//AM/PM選択ボタン
print "<select name=close_ampm[]>";
print "<option></option>";
if($close_ampm[$value['day']-1] == 1){
	print '<option value = 1 selected>AM</option>
	<option value = 2>PM</option>;
	<option value = 3>AP</option>';
}elseif($close_ampm[$value['day']-1] == 2){
	print '<option value = 1 >AM</option>
	<option value = 2 selected>PM</option>;
	<option value = 3>AP</option>';
}elseif($close_ampm[$value['day']-1] == 3){
	print '<option value = 1>AM</option>
	<option value = 2>PM</option>;
	<option value = 3 selected>AP</option>';
}else{
	print '<option value = 1>AM</option>
	<option value = 2>PM</option>;
	<option value = 3>AP</option>';
}
print "</select>";

$t = Time_select(strtotime('00:00'));
$t[0]="00:00";
print "<select name=close[]>";
print "<option></option>";
for ($i=0; $i < count($t); $i++) {
//テーブルに始業時間(時)がある場合、初期値を設定する
if(isset($close[$value['day']-1])){
	$selected=($close[$value['day']-1]==$t[$i])?" selected":"";
}elseif(isset($err_msg)){
	$selected=($_SESSION['close'][$value['day']-1]==$t[$i])?" selected":"";
}
else{
//テーブルに始業時間(時)がない場合、所定始業時間をデフォルト(土日の場合はブランクをセット)
$selected = "";
}
print "<option value=\"".$t[$i]."\"{$selected}>".$t[$i]."</option>";
}?>
</select>
</td>
<td>
<?
$t = rest_time(strtotime('00:00'));
print "<select name=rest[]>";
print "<option></option>";
for ($i=0; $i < count($t); $i++) {
	if(isset($holiday_rest_hour[$value['day']-1])){
		$selected=($holiday_rest_hour[$value['day']-1]==$t[$i])?" selected":"";
	}elseif(isset($err_msg)){
		$selected=($_SESSION['rest'][$value['day']-1]==$t[$i])?" selected":"";
	}else{
	//テーブルに始業時間(時)がない場合、所定始業時間をデフォルト(土日の場合はブランクをセット)
	$selected = "";
	}
	print "<option value=\"".$t[$i]."\"{$selected}>".$t[$i]."</option>";
}
?>
</select>
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
}else{
foreach($overtime_night as $n_over => $over){
if($value['day']-1==$n_over){
if($over=="00:00"){
print "";
}else{
print $over;
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
if(!empty($check_result[$value['day']-1])){
foreach ($check_result as $c_key => $chk) {
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
if(isset($naiyou[$value['day']-1])){?>
<? foreach($naiyou as $key =>$sagyo){
if($value['day']-1==$key){?>
<textarea name="naiyou[]" rows="3" cols="20" maxlength="20"><?=$sagyo;?></textarea><?}
}
}else{?>
<textarea name="naiyou[]" rows="3" cols="20" maxlength="20"></textarea>
<?}?>
<td>
<?
//始業時間
//AM/PM選択ボタン
print "<select name=open_ampm[]>";
print "<option></option>";
if($open_ampm[$value['day']-1] == 1){
	print '<option value = 1 selected>AM</option>
	<option value = 2>PM</option>;
	<option value = 3>AP</option>';
}elseif($open_ampm[$value['day']-1] == 2){
	print '<option value = 1 >AM</option>
	<option value = 2 selected>PM</option>;
	<option value = 3>AP</option>';
}elseif($open_ampm[$value['day']-1] == 3){
	print '<option value = 1 >AM</option>
	<option value = 2>PM</option>;
	<option value = 3 selected>AP</option>';
}elseif($holiday[$value['day']-1] == "1" || $holiday[$value['day']-1] == "2" ||
$holiday[$value['day']-1] == "3" || $holiday[$value['day']-1] == "6"){
	print '<option value =1>AM</option>
	<option value = 2>PM</option>;
	<option value = 3>AP</option>';
}elseif($open_ampm[$value['day']-1]==NULL && $err_msg[$value['day']-1]!=""){
	print '<option value =1>AM</option>
	<option value = 2>PM</option>;
	<option value = 3>AP</option>';
}else{
	print '<option value = 1 selected>AM</option>
	<option value = 2>PM</option>;
	<option value = 3>AP</option>';
}
print "</select>";

$t = Time_select(strtotime('00:00'));
print "<select name=open[]>";
print "<option></option>";
for ($i=0; $i < count($t); $i++) {
//テーブルに始業時間(時)がある場合、初期値を設定する
if(isset($open[$value['day']-1])){
	$selected=($open[$value['day']-1]==$t[$i])?" selected":"";
}elseif(isset($err_msg)){
	$selected=($_SESSION['open'][$value['day']-1]==$t[$i])?" selected":"";
}
else{
//テーブルに始業時間(時)がない場合、所定始業時間をデフォルト
$selected=$opening_get==$t[$i] ? " selected":"";
}
print "<option value=\"".$t[$i]."\"{$selected}>".$t[$i]."</option>";
}?>
</select>
</td>
<!--終業時間の入力欄!-->
<td>
<?
//AM/PM選択ボタン
print "<select name=close_ampm[]>";
print "<option></option>";
if($close_ampm[$value['day']-1] == 1){
	print '<option value = 1 selected>AM</option>
	<option value = 2>PM</option>;
	<option value = 3>AP</option>';
}elseif($close_ampm[$value['day']-1] == 2){
	print '<option value = 1 >AM</option>
	<option value = 2 selected>PM</option>;
	<option value = 3>AP</option>';
}elseif($close_ampm[$value['day']-1] == 3){
	print '<option value = 1 >AM</option>
	<option value = 2>PM</option>;
	<option value = 3 selected>AP</option>';
}elseif($holiday[$value['day']-1] == "1" || $holiday[$value['day']-1] == "2" ||
$holiday[$value['day']-1] == "3" || $holiday[$value['day']-1] == "6"){
	print '<option value =1>AM</option>
	<option value = 2>PM</option>;
	<option value = 3>AP</option>';
}elseif($close_ampm[$value['day']-1]==NULL && $err_msg[$value['day']-1]!=""){
	print '<option value =1>AM</option>
	<option value = 2>PM</option>;
	<option value = 3>AP</option>';
}else{
	print '<option value = 1>AM</option>
	<option value = 2 selected>PM</option>;
	<option value = 3>AP</option>';
}
print "</select>";

$t = Time_select(strtotime('00:00'));
$t[0]="00:00";
print "<select name=close[]>";
print "<option></option>";
for ($i=0; $i < count($t); $i++) {
//テーブルに始業時間(時)がある場合、初期値を設定する
if(isset($close[$value['day']-1])){
	$selected=($close[$value['day']-1]==$t[$i])?" selected":"";
}elseif(isset($err_msg)){
	$selected=($_SESSION['close'][$value['day']-1]==$t[$i])?" selected":"";
}
else{
//テーブルに始業時間(時)がない場合、所定始業時間をデフォルト
$closong_get1 = minVtime1($closong_get,"12:00");
$selected=$closong_get1==$t[$i] ? " selected":"";
}
print "<option value=\"".$t[$i]."\"{$selected}>".$t[$i]."</option>";
}?>
</select>
</td>
<td>
<?
if(isset($weekday_rest[$value['day']-1])){
if($weekday_rest[$value['day']-1]=="00:00"){
print "";
}else{
foreach($weekday_rest as $r => $res){
if($value['day']-1==$r){
print $res;
}
}
}
}else{
print "";

}?>
<input type="hidden" name="rest[]" value="">
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
//備考
if(isset($bikou)){
//備考メッセージ表示
if(empty($err_msg[$value['day']-1])){
foreach ((array)$bikou as $n_bikou => $bk) {
if($value['day']-1==$n_bikou){
print $bk;
}
}
//エラーメッセージ表示
}else{
foreach ($err_msg as $n_msg => $msg) {
if($value['day']-1==$n_msg){
print $msg;
}
}
}
}
?>

</td>
<td class="kyuka">
<?
//休暇フラグ
print "<select name=holiday[] class=kyuka>";
print "<option value='0'></option>";
for($i=1;$i<=6;$i++){
if(isset($err_msg)){
$selected=($_SESSION['holiday'][$value['day']-1]==$i)?" selected":"";
}
elseif(isset($holiday[$value['day']-1])){
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
if(!empty($check_result[$value['day']-1])){
foreach ($check_result as $c_key => $chk) {
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
if(isset($err_msg)){
$selected=($_SESSION['shift'][$value['day']-1]==$i)?" selected":"";
}
elseif(isset($shift[$value['day']-1])){
$selected=($shift[$value['day']-1]==$i)?" selected":"";
}
print "<option value=\"".$i."\"{$selected}>".$i."</option>";
}
print "</select>\n";
?>
</td>
</tr>
<?}

}else{?>
<!--  印刷プレビュー用表示画面 -->
<td>
<!-- 作業内容 -->
<?foreach($naiyou as $key =>$sagyo){
if($value['day']-1==$key){?>
<p class="sagyo_comment"><?=$sagyo;?></p>
<?}
}?>
</td>
<td>
<!-- 始業時間 -->
<?foreach($open4 as $key =>$op4){
if($value['day']-1==$key){
	if($open_ampm[$key] == 2){
		$open_pm1[$key] = explode(":", $op4)[0] + 12;
		$open_pm2[$key] = explode(":", $op4)[1];
		$open_pm[$key] = $open_pm1[$key].":".$open_pm2[$key];
	}elseif($open_ampm[$key] == 3){
			$open_pm1[$key] = explode(":", $op4)[0] + 24;
			$open_pm2[$key] = explode(":", $op4)[1];
			$open_pm[$key] = $open_pm1[$key].":".$open_pm2[$key];
		}else{
			$open_pm[$key]= $op4;
		}
		print $open_pm[$key];
	}
}?>
</td>
<td>
<!-- 終業時間 -->
<?foreach($close4 as $key =>$cl4){
	if($value['day']-1==$key){
		if($close_ampm[$key] == 2){
			$close_pm1[$key] = explode(":", $cl4)[0] + 12;
			$close_pm2[$key] = explode(":", $cl4)[1];
			$close_pm[$key] = $close_pm1[$key].":".$close_pm2[$key];
		}elseif($close_ampm[$key] == 3){
				$close_pm1[$key] = explode(":", $cl4)[0] + 24;
				$close_pm2[$key] = explode(":", $cl4)[1];
				$close_pm[$key] = $close_pm1[$key].":".$close_pm2[$key];
			}else{
				$close_pm[$key]= $cl4;
			}
			print $close_pm[$key];
		}
}?>
</td>
<td>
<!-- 休憩時間 -->
<?foreach($weekday_rest as $key =>$wr3){
if($value['day']-1==$key){
if($wr3=="00:00"){
print "";
}else{
print $wr3;
}
}
}?>
</td>
<td>
<!-- 実働時間 -->
<?foreach($total as $key => $val){
if($value['day']-1==$key){
if($val=="00:00"){
print "";
}else{
print $val;
}
}
}?>
</td>
<td>
<!-- 普通残業時間 -->
<?foreach($overtime_normal as $over_num => $over_all){
if($value['day']-1==$over_num){
if($over_all=="00:00"){
print "";
}else{
print $over_all;
}
}
}?>
</td>
<td>
<!-- 深夜残業時間 -->
<?foreach($overtime_night as $number => $night){
if($value['day']-1==$number){
if($night=="00:00"){
print "";
}else{
print $night;
}
}
}?>
</td>
<td>
<!-- 不足 -->
<?foreach($Shortage as $suuji => $Short){
if($value['day']-1==$suuji){
if($Short!="" && $Short=="00:00"){
print "";
}elseif($Short!="" && $Short!="00:00"){
print $Short;
}
}
}?>
</td>
<td>
<!-- 備考 -->
<?foreach ((array)$bikou as $n_bikou => $bk) {
if($value['day']-1==$n_bikou){
print $bk;
}
}?>
</td>
<!-- 以下は画面上表示するが、印刷はしない -->
<!-- 休暇フラグ -->
<td class="kyuka">
<?foreach($holiday as $key =>$holi){
if($value['day']-1==$key){
if($holi!="0"){
print $holi;
}
}
}?>
</td>
<!-- チェック結果 -->
<td class="check">
<?php foreach ($check_result as $c_key => $chk) {
if($value['day']-1==$c_key){
print $chk;
}
}?>
</td>
<!-- シフトフラグ -->
<td class="shift">
<?php foreach($shift as $key =>$shi){
if($value['day']-1==$key){
if($shi!="0"){
print $shi;
}
}
}?>
</td>
<?}
}?>
<?//if($_SERVER['HTTP_REFERER']!="https://www.pros-service.co.jp/kinmu/list_of_members.php"){
if($_SERVER['HTTP_REFERER']!="http://localhost:8080/kinmuhyo/list_of_members.php"){
//当月データなしかつ、先月ステータス2以外の時は保存ボタン非活性
if($year_and_month!=$comparison_month){
if($kinmuhyo_summary['status']=="0" || $kinmuhyo_summary['status']=="1" || $year_and_month==NULL){?>
<input name="tochu" type="submit" disabled value="保存" class="save" style="margin-top: -100px">
<?}else{?>
<input name="tochu" type="submit" value="保存" class="save" style="margin-top: -100px">
<?}
//当月データあり
}else{
	//管理者確認が済の場合は勤務表の保存を不可とする
	if($kinmuhyo_summary['status']=="4"){?>
	<input name="tochu" type="submit" disabled value="保存" class="save" style="margin-top: -100px">
<?}else{?>
	<input name="tochu" type="submit" value="保存" class="save" style="margin-top: -100px">
<?}?>
<?}?>
</form>
</table>
<table border="1" class="second_tbl">
<td class="sum_title" style="width: 60px"><p>合計</p></td>
<td style="width: 80px; text-align: center">
<!-- テーブルに格納された実働時間の合計を表示 -->
<?php if($sum_total["total_time"]!=""){
$sum_total["total_time"] = substr($sum_total["total_time"], 0, -3);
print $sum_total["total_time"];
}else{
print "00:00";
}?>
</td>
<td style="width: 95px; text-align: center">
<!-- テーブルに格納された残業時間の合計を表示 -->
<?php if($sum_overtime["total_time"]!=""){
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
<td style="width: 95px; text-align: center">
<!-- テーブルに格納された深夜残業時間の合計を表示 -->
<?php if($sum_overtime_night["total_time"]!=""){
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
<td style="width: 70px; text-align: center">
<!-- テーブルに格納された不足時間の合計を表示 -->
<?php if($sum_short["total_time"]!=""){
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
<?}?>
<!-- 土日祝日を足し、足した結果当月日数から引く -->
<?php
$kyuujitu=array_merge($doyou, $nitisyuku);
//営業日を算出
$eigyoubi = $value['day']-count($kyuujitu);
?>
</body>
</html>
