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
//不正ログインの場合ログイン画面に遷移させる
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
//管理者かそうでないか（0!=管理者 1=管理者）
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
		$rest[$key]=substr($value['break_time'],0,5);
		$res_1[$key]=substr($value['break_time'],0,2);
		$res_2[$key]=substr($value['break_time'],3,2);
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
	}
}
///勤務表テーブルE///

//メソッド参照
//実働合計(勤務表テーブルの読み込み)S////
$sum_total= kinmu_common::sum_total($result['staff_number']);
//残業合計(勤務表テーブルの読み込み)
$sum_overtime= kinmu_common::sum_overtime($result['staff_number']);
//深夜残業合計(勤務表テーブルの読み込み)
$sum_overtime_night= kinmu_common::sum_overtime_night($result['staff_number']);
//不足合計(勤務表テーブルの読み込み)
$sum_short= kinmu_common::sum_short($result['staff_number']);
//祝日取得(祝日テーブルの読み込み)
$kinmuhyo_holiday= kinmu_holiday::Holiday("");
//勤務表テーブルE///

//有給日数の計算
if(isset($holiday)){
	//有給休暇の日数
	$syoka=0;
	foreach($holiday as $key =>$val){
   		if(stristr($holiday[$key],"1") !== false){
      		$syoka++;
   		}
	}
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
//チェックの結果エラーメッセージがあれば変数に格納
$err_msg=isset($_SESSION['err_msg']) ? $_SESSION['err_msg'] : '';
$check[]=isset($_SESSION['check']) ? $_SESSION['check'] : '';
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
<form method="post" action="new_kinmuhyo.php">
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
<!--kinmuhyo_checkに値を投げる-->
<form method="post" action="kinmuhyo_check.php">
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
							print "<option>".$work_tbl[$i]."</option>";	
						print "</select>";
							}?>
				</td>
			<?}?>
		</tr>
			<?
				foreach ($work_tbl as $value) {
					print $value;
				}
			?>
	</table>





	<input name="tochu" type="submit" value="保存">
</form>



