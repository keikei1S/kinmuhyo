<?
//セッションが始まっていなければセッションを開始する
if(!isset($_SESSION)){
	session_start();
}
//勤務表ファイルを読み込む
ob_start();
	include("kinmuhyo.php");
ob_clean();
//変数の整理S///
$opening_get=substr($opening,0,2);
$opening_get2=substr($opening,3,2);
$opening_get3=$opening_get.$opening_get2;
$closong_get=substr($closong,0,2);
$closong_get2=substr($closong,3,2);
$closong_get3=$closong_get.$closong_get2;
$work=$closong_get-$opening_get-1;
$naiyou=$_POST['naiyou'];
$naiyou = array_map('htmlspecialchars', $naiyou);
$open=$_POST['open'];
$close=$_POST['close'];
$open2=$_POST['open2'];
$close2=$_POST['close2'];
$rest=$_POST['rest'];
$rest2=$_POST['rest2'];
$holiday=$_POST['holiday'];
$shift=$_POST['shift_kinmu'];
$check=array();
$err_msg=array();
//月初
$first_date = date("Y-").$_POST['month'].date("-01");
//今月末
$now_month = date('t', strtotime($first_date));

//曜日取得
$week=$_POST['week'];
//土曜のキーを取得
$week_day=array_keys($week, '土');
//日曜のキーを取得
$week_day2=array_keys($week, '日');
//上記をマージし、土日の配列を作る
$week_day3=array_merge($week_day,$week_day2);
sort($week_day3);
//土曜の配列作成
$week_day4 =array_fill(0, $now_month, 'ダミー');
foreach($week_day as $key => $value){
    $key = $value;
    $week_day4[$key]=$value;
}
//日曜の配列作成
$week_day5 =array_fill(0, $now_month, 'ダミー');
foreach($week_day2 as $key => $value){
    $key = $value;
    $week_day5[$key]=$value;
}
//土日祝の配列作成
$week_day6 =array_fill(0, $now_month, 'ダミー');
foreach($week_day3 as $key => $value){
   	$key = $value;
   	$week_day6[$key]=$value;
}
for($i = 0; $i <= $now_month ; $i++){
	$kensaku[]=$i;
}
//変数の整理E///
//該当月の日数分for分を回す
for($i = 0; $i < $now_month ; $i++){
	//エラーチェック
	//ブランクチェック
	//始業(時)なし、以外あり
	if($open[$i]==""&& $open2[$i]!=""&& $close[$i]!=""&&$close2[$i]!=""){
		$check[$i]="NG";
		$err_msg[$i]="始業時間(時)を入力してください。";
		//始業(分)なし、以外あり
	}elseif($open[$i]!=""&& $open2[$i]==""&& $close[$i]!=""&&$close2[$i]!=""){
		$check[$i]="NG";
		$err_msg[$i]="始業時間(分)を入力してください。";
		//終業(時)なし、以外あり
	}elseif($open[$i]!=""&& $open2[$i]!=""&& $close[$i]==""&&$close2[$i]!=""){
		$check[$i]="NG";
		$err_msg[$i]="終業時間(時)を入力してください。";
		//終業(分)なし、以外あり
	}elseif($open[$i]!=""&& $open2[$i]!=""&& $close[$i]!=""&&$close2[$i]==""){
		$check[$i]="NG";
		$err_msg[$i]="終業時間(分)を入力してください。";
		//始業(分)終業(時)なし、以外あり
	}elseif($open[$i]!=""&& $open2[$i]==""&& $close[$i]==""&&$close2[$i]!=""){
		$check[$i]="NG";
		$err_msg[$i]="始業終業時間を入力してください。";
		//始業(時)終業(分)なし、以外あり
	}elseif($open[$i]==""&& $open2[$i]!=""&& $close[$i]!=""&&$close2[$i]==""){
		$check[$i]="NG";
		$err_msg[$i]="始業終業時間を入力してください。";
		//始業(時)以外なし
	}elseif($open[$i]!=""&& $open2[$i]==""&& $close[$i]==""&&$close2[$i]==""){
		$check[$i]="NG";
		$err_msg[$i]="始業終業時間を入力してください。";
		//始業(分)以外なし
	}elseif($open[$i]==""&& $open2[$i]!=""&& $close[$i]==""&&$close2[$i]==""){
		$check[$i]="NG";
		$err_msg[$i]="始業終業時間を入力してください。";
		//終業(時)以外なし
	}elseif($open[$i]==""&& $open2[$i]==""&& $close[$i]!=""&&$close2[$i]==""){
		$check[$i]="NG";
		$err_msg[$i]="始業終業時間を入力してください。";
		//終業(分)以外なし
	}elseif($open[$i]==""&& $open2[$i]==""&& $close[$i]==""&&$close2[$i]!=""){
		$check[$i]="NG";
		$err_msg[$i]="始業終業時間を入力してください。";
		//時なし
	}elseif($open[$i]==""&& $open2[$i]!=""&& $close[$i]==""&&$close2[$i]!=""){
		$check[$i]="NG";
		$err_msg[$i]="始業終業時間を入力してください。";
		//分なし
	}elseif($open[$i]!=""&& $open2[$i]==""&& $close[$i]!=""&&$close2[$i]==""){
		$check[$i]="NG";
		$err_msg[$i]="始業終業時間を入力してください。";
		//終業時間なし
	}else{
		$check[$i]="OK";
	}
	//上記エラーがない場合
	if($check[$i]=="OK"){
	//休暇関連チェック
	//シフトフラグありかつ休暇フラグあり
		if($shift[$i]=="1"&& $holiday[$i]!="0"){
			$check[$i]="NG";
			$err_msg[$i]="休暇関連エラー";
		}else{
			$check[$i]="OK";
			$bikou[$i]="";
		}
	//シフトフラグあり、かつ始業・終業ブランク
		if($shift[$i]=="1"){
			if($open[$i]=="" || $open2[$i]=="" || $close[$i]=="" || $close2[$i]==""){
				$check[$i]="NG";
				$err_msg[$i]="シフト勤務エラー";
				$bikou[$i]="";
			}else{
				$check[$i]="OK";
				$bikou[$i]="シフト勤務";
			}
		}
	}
	//エラーメッセージがある場合、insertせず、勤務表画面に返す。
	if(isset($err_msg[$i])){
		$_SESSION['err_msg']=$err_msg;
		$_SESSION['check']=$check;
		header('Location:kinmuhyo.php');
		exit;
	}
	if($check[$i]=="OK"){
//入力された時間の表示形式を修正//S
		if($open[$i]==""){
			$open[$i]="00";
		}
		if($open2[$i]==""){
			$open2[$i]="00";
		}
		if($close[$i]==""){
			$close[$i]="00";
		}
		if($close2[$i]==""){
			$close2[$i]="00";
		}
		$total[$i]="00:00";
		$overtime[$i]="00:00";
		$overtime_night[$i]="00:00";
		$Shortage[$i]="00:00";
		if($open[$i]!='00'){
			switch ($close[$i]) {
			//終業時間が以下の場合、closeに+24時間する
				case '00':
					$close[$i]=$close[$i]+24;
					break;
				case '01':
					$close[$i]=$close[$i]+24;
					break;
				case '02':
					$close[$i]=$close[$i]+24;
					break;
				case '03':
					$close[$i]=$close[$i]+24;
					break;
				case '04':
					$close[$i]=$close[$i]+24;
					break;
				case '05':
					$close[$i]=$close[$i]+24;
					break;
				case '06':
					$close[$i]=$close[$i]+24;
					break;
				case '07':
					$close[$i]=$close[$i]+24;
					break;
				case '08':
					$close[$i]=$close[$i]+24;
					break;
				case '09':
					$close[$i]=$close[$i]+24;
					break;
				case '10':
					if($opening_get==10){
						$close[$i]=$close[$i]+24;
					}
					break;
				default:
					break;
			}
		}
//シフトフラグ=1の時
		if($shift[$i]=="1"){
			$shift_total[$i]=$open[$i]-$opening_get;
			$shift_total2[$i]=$open2[$i]-$opening_get2;
			$s_open[$i]=$open[$i]-$shift_total[$i];
			$s_open2[$i]=$open2[$i]-$shift_total2[$i];
			$s_close[$i]=$close[$i]-$shift_total[$i];
			$s_close2[$i]=$close2[$i]-$shift_total2[$i];

			//時、分を結合する
			foreach (array($s_open[$i]) as $o_key => $op) {
				foreach(array($s_open2[$i]) as $o_key2 => $op2) {
					if($o_key==$o_key2){
						$open_array[$i] = sprintf('%02d',$op).sprintf('%02d',$op2);
					}
				}
			}
			foreach ($s_close[$i] as $c_key => $cl) {
				foreach ($s_close2[$i] as $c_key2 => $cl2) {
					if($c_key==$c_key2){
						$close_array[$i] = sprintf('%02d',$cl).sprintf('%02d',$cl2);
					}
				}
			}
		}else{
		//シフトにフラグがない場合
		//時、分を結合する
			foreach (array($open[$i]) as $o_key => $op) {
				foreach(array($open2[$i]) as $o_key2 => $op2) {
					if($o_key==$o_key2){
						$open_array[] = $op.$op2;
					}
				}
			}
			foreach (array($close[$i]) as $c_key => $cl) {
				foreach (array($close2[$i]) as $c_key2 => $cl2) {
					if($c_key==$c_key2){
						$close_array[] = $cl.$cl2;
					}
				}
			}
		}
		//始業時間>終業時間
		if($shift!="1"){
			if($open_array[$i] > $close_array[$i]){
				$check[$i]="NG";
				$err_msg[$i]="時刻逆転エラー" ;
			}
		}
	}
	//半休フラグがある場合
	if($check[$i]=="OK"){	
		if($holiday[$i]==4 || $holiday[$i]==5){
			if($open_array[$i]=="" || $close_array[$i]==""){
				$check[$i]="NG";
				$err_msg[$i]="始業・終業時間を入力してください。";
			}elseif($open[$i]==""&& $open2[$i]!=""&& $close[$i]!=""&&$close2[$i]!=""){
				$check[$i]="NG";
				$err_msg[$i]="始業時間(時)を入力してください。";
				//始業(分)なし、以外あり
			}elseif($open[$i]!=""&& $open2[$i]==""&& $close[$i]!=""&&$close2[$i]!=""){
				$check[$i]="NG";
				$err_msg[$i]="始業時間(分)を入力してください。";
				//終業(時)なし、以外あり
			}elseif($open[$i]!=""&& $open2[$i]!=""&& $close[$i]==""&&$close2[$i]!=""){
				$check[$i]="NG";
				$err_msg[$i]="終業時間(時)を入力してください。";
				//終業(分)なし、以外あり
			}elseif($open[$i]!=""&& $open2[$i]!=""&& $close[$i]!=""&&$close2[$i]==""){
				$check[$i]="NG";
				$err_msg[$i]="終業時間(分)を入力してください。";
				//始業(分)終業(時)なし、以外あり
			}elseif($open[$i]!=""&& $open2[$i]==""&& $close[$i]==""&&$close2[$i]!=""){
				$check[$i]="NG";
				$err_msg[$i]="始業終業時間を入力してください。";
				//始業(時)終業(分)なし、以外あり
			}elseif($open[$i]==""&& $open2[$i]!=""&& $close[$i]!=""&&$close2[$i]==""){
				$check[$i]="NG";
				$err_msg[$i]="始業終業時間を入力してください。";
				//始業(時)以外なし
			}elseif($open[$i]!=""&& $open2[$i]==""&& $close[$i]==""&&$close2[$i]==""){
				$check[$i]="NG";
				$err_msg[$i]="始業終業時間を入力してください。";
				//始業(分)以外なし
			}elseif($open[$i]==""&& $open2[$i]!=""&& $close[$i]==""&&$close2[$i]==""){
				$check[$i]="NG";
				$err_msg[$i]="始業終業時間を入力してください。";
				//終業(時)以外なし
			}elseif($open[$i]==""&& $open2[$i]==""&& $close[$i]!=""&&$close2[$i]==""){
				$check[$i]="NG";
				$err_msg[$i]="始業終業時間を入力してください。";
				//終業(分)以外なし
			}elseif($open[$i]==""&& $open2[$i]==""&& $close[$i]==""&&$close2[$i]!=""){
				$check[$i]="NG";
				$err_msg[$i]="始業終業時間を入力してください。";
				//時なし
			}elseif($open[$i]==""&& $open2[$i]!=""&& $close[$i]==""&&$close2[$i]!=""){
				$check[$i]="NG";
				$err_msg[$i]="始業終業時間を入力してください。";
				//分なし
			}elseif($open[$i]!=""&& $open2[$i]==""&& $close[$i]!=""&&$close2[$i]==""){
				$check[$i]="NG";
				$err_msg[$i]="始業終業時間を入力してください。";
				//終業時間なし
			}elseif($open_array[$i]!=""&& $close_array[$i]==""){
				$check[$i]="NG";
				$err_msg[$i]="終業時間を入力してください。";
				//始業時間なし
			}elseif($open_array[$i]==""&& $close_array[$i]!=""){
				$check[$i]="NG";
				$err_msg[$i]="始業時間を入力してください。";
			}else{
				$check[$i]="OK";
			}
		}
	}
	//エラーメッセージがある場合、insertせず、勤務表画面に返す。
	if(isset($err_msg[$i])){
		$_SESSION['err_msg']=$err_msg;
		$_SESSION['check']=$check;
		header('Location:kinmuhyo.php');
		exit;
	}
	if($check[$i]=="OK"){
		// 休憩時間の処理s//
		// 休日の場合//
		if($week_day6[$i]===$kensaku[$i]){
			if($rest[$i]=="" && $rest2[$i]==""){
				$rest[$i]="00";
				$rest2[$i]="00";
			}
		}else{
			if($week_day6[$i]!==$kensaku[$i]){
			//平日の場合
				if($open_array[$i]<=1200 && $close_array[$i]<=2200){
					$rest[$i]="01";
					$rest2[$i]="00";
				}elseif($open_array[$i]!="" && $close_array[$i]!=""){
					if($open_array[$i]<=1200 && $close_array[$i]<=1200){
						$rest[$i]="00";
						$rest2[$i]="00";
					}
				}elseif($close_array[$i] > 1200 && $close_array[$i] < 1300){
					switch ($close_array[$i]) {
						case '1215':
							$rest[$i]="00";
							$rest2[$i]="15";
						break;
						case '1230':
							$rest[$i]="00";
							$rest2[$i]="30";
						break;
						case '1245':
							$rest[$i]="00";
							$rest2[$i]="45";
						break;
						default:
							# code...
						break;
					}
				}
				//始業が12:15~12:45の間:
				if($open_array[$i] > 1200 && $open_array[$i] < 1300){
					switch ($open_array[$i]) {
						case '1215':
							$rest[$i]="00";
							$rest2[$i]="45";
							$open_array[$i]=$open_array[$i]-100;
						break;
						case '1230':
							$rest[$i]="00";
							$rest2[$i]="30";
							$open_array[$i]=$open_array[$i]-100;
						break;
						case '1245':
							$rest[$i]="00";
							$rest2[$i]="15";
							$open_array[$i]=$open_array[$i]-100;
						break;
						default:
							# code...
						break;
					}
				//始業時間が12時以降
				}elseif($open_array[$i]>1200){
						$open_array[$i]=$open_array[$i]-100;
				}
				//終業時間が22時以降
				if($close_array[$i]>=2230 && $close_array[$i]<=2900){
					$rest[$i]="01";
					$rest2[$i]="30";
				}
				if($close_array[$i]==2215){
					$rest[$i]="01";
					$rest2[$i]="15";
				}elseif($close_array[$i]==2915){
					$rest[$i]="01";
					$rest2[$i]="45";
				}
				if($close_array[$i]>=2930 && $close_array[$i]<=3400){
					$rest[$i]="02";
					$rest2[$i]="00";
				}
				if($shift[$i]=="1"){
					if($s_close[$i]=="22" && $s_close2[$i]=="15"){
						$rest2[$i]="15";
					}elseif($s_close[$i]=="29" && $s_close2[$i]=="15"){
						$rest[$i]="01";
						$rest2[$i]="45";
					}
				}
			}
		}
		//休憩時間e//
		//実働計算
		//DateTimeクラス
		if($shift[$i]=="0"){
			$objDatetime1 = new DateTime(date(("Y/m/d H:i"), mktime($open[$i]+$rest[$i],$open2[$i]+$rest2[$i])));
			$objDatetime2 = new DateTime(date(("Y/m/d H:i"), mktime($close[$i],$close2[$i])));
		}else{
			$objDatetime1 = new DateTime(date(("Y/m/d H:i"), mktime($s_open[$i]+$rest[$i],$s_open2[$i]+$rest2[$i])));
			$objDatetime2 = new DateTime(date(("Y/m/d H:i"), mktime($s_close[$i],$s_close2[$i])));
		}
		//diffメソッド
		//ふたつの日付の差をあらわす DateInterval オブジェクトを返す。
		$objInterval[$i] = $objDatetime1->diff($objDatetime2);
		//実働時間
		$total[$i] = $objInterval[$i]->format('%H:%I');
		//終業時間によって休憩時間を調整
		if($close[$i]=="22" && $close2[$i]=="15"){
			$rest2[$i]="30";
		}elseif($close[$i]=="29" && $close2[$i]=="15"){
			$rest[$i]="02";
			$rest2[$i]="00";
		}
		//シフトの場合も同様に調整
		if($shift[$i]=="1"){
			if($s_close[$i]=="22" && $s_close2[$i]=="15"){
				$rest2[$i]="30";
			}elseif($s_close[$i]=="29" && $s_close2[$i]=="15"){
				$rest[$i]="02";
				$rest2[$i]="00";
			}
		}
		//休日の場合備考には何も入れない
		if($week_day6[$i]==$kensaku[$i]){
			$bikou[$i]="";
		}else{
			if($shift[$i]=="0" && $open_array[$i]!="" && $close_array[$i]!=""){
			//遅刻判定s//
				$Shortage[$i]="00:00";
				$overtime_night[$i] = "00:00";
				$work_start = $open_array[$i];
				$default_work = $opening;
				//DateTimeクラス
				$work_start_time = new DateTime($work_start);
				$default_work_time = new DateTime($default_work);
				//diffメソッド
				//ふたつの日付の差をあらわす DateInterval オブジェクトを返します。
				$tikoku[$i] = $work_start_time->diff($default_work_time);
				$tikoku_time[$i] = $tikoku[$i]->format('%H:%I');
				if($work_start_time > $default_work_time){
					//不足に遅刻時間を代入
					$Shortage[$i]= $tikoku_time[$i];
					$bikou[$i]="遅刻";
				}
			//遅刻判定e//
			//早退判定s//
				$work_end = $close_array[$i];
				$default_end = $closong;
				$work_end_time = new DateTime($work_end);
				$default_end_time = new DateTime($default_end);
				$soutai[$i] = $work_end_time->diff($default_end_time);
				$soutai_time[$i] = $soutai[$i]->format('%H:%I');
				if($work_end_time < $default_end_time){
				//遅刻でなかった場合、以下の処理
					if($Shortage[$i]=="00:00"){
					//不足に早退時間を代入
						$Shortage[$i] = $soutai_time[$i];
						$bikou[$i]="早退";
					}else{
					//早退かつ遅刻の場合
						$tikoku_time[$i] = filter_var($tikoku_time[$i],FILTER_SANITIZE_NUMBER_INT);
						$soutai_time[$i] = filter_var($soutai_time[$i],FILTER_SANITIZE_NUMBER_INT);
							//計算用に時分にわける
						$tikoku_hour[$i]=substr($tikoku_time[$i],0,2);
						$tikoku_min[$i]=substr($tikoku_time[$i],2,2);
						$soutai_hour[$i]=substr($soutai_time[$i],0,2);
						$soutai_min[$i]=substr($soutai_time[$i],2,2);
						//遅刻時間と早退時間を足した値を不足時間に代入
						$Shortage[$i] = date('H:i',mktime($tikoku_hour[$i]+$soutai_hour[$i],$tikoku_min[$i]+$soutai_min[$i]));
						$bikou[$i]="遅刻早退";
					}
				}
			}
		}
		//半休判定(午前半休)
		if($holiday[$i]==4){
			$bikou[$i]="午前半休";
			//遅刻判定
			//始業時間が08:00
			if($opening=="0800"){
				if($open_array[$i] > 1200){
					$sigyo=new DateTime($open_array[$i]);
					$syotei=new DateTime(1230);
					$s_short[$i] = $sigyo->diff($syotei);
					$Shortage[$i] = $s_short[$i]->format('%H:%I');
					$bikou[$i]="前・遅刻";
				}else{
					$Shortage[$i] = "00:00";
				}
				//始業時間が09:00
			}elseif($opening_get=="09"){
				if($open_array[$i] > 1300){
					$sigyo=new DateTime($open_array[$i]);
					$syotei=new DateTime(1300);
					$s_short[$i] = $sigyo->diff($syotei);
					$Shortage[$i] = $s_short[$i]->format('%H:%I');
					$bikou[$i]="前・遅刻";
				}else{
					$Shortage[$i] = "00:00";
				}
			}elseif($opening_get=="10"){
				if($open_array[$i] > 1400){
					$sigyo=new DateTime($open_array[$i]);
					$syotei=new DateTime(1400);
					$s_short[$i] = $sigyo->diff($syotei);
					$Shortage[$i] = $s_short[$i]->format('%H:%I');
					$bikou[$i]="前・遅刻";
				}else{
					$Shortage[$i] = "00:00";
				}
			}
			//早退判定
			//終業時間（時）が17時の場合
			if($closong_get=="17"){
				if($close_array[$i] < 1730){
					$syugyo=new DateTime($close_array[$i]);
					$syotei=new DateTime(1730);
					$s_short[$i] = $syugyo->diff($syotei);
					$Shortage2[$i] = $s_short[$i]->format('%H:%I');
					if($bikou[$i]=="午前半休"){
						$bikou[$i]="前・早退";
						$Shortage=$Shortage2;
					}else{
						$Shortage[$i]=date('H:i',mktime(substr($Shortage[$i],0,2)+substr($Shortage2[$i],0,2),substr($Shortage[$i],3,2)+substr($Shortage2[$i],3,2)));
						$bikou[$i]="前・遅刻早退";
					}
				}
				//終業時間（時）が18時の場合
			}elseif($closong_get=="18"){
				if($close_array[$i] < 1800){
					$syugyo=new DateTime($close_array[$i]);
					$syotei=new DateTime(1800);
					$s_short[$i] = $syugyo->diff($syotei);
					$Shortage2[$i] = $s_short[$i]->format('%H:%I');
					if($bikou[$i]=="午前半休"){
						$bikou[$i]="前・早退";
						$Shortage=$Shortage2;
					}else{
						$Shortage[$i]=date('H:i',mktime(substr($Shortage[$i],0,2)+substr($Shortage2[$i],0,2),substr($Shortage[$i],3,2)+substr($Shortage2[$i],3,2)));
						$bikou[$i]="前・遅刻早退";
					}
				}
				//終業時間（時）が19時の場合
			}elseif($closong_get=="19"){
				if($close_array[$i] < 1900){
					$syugyo=new DateTime($close_array[$i]);
					$syotei=new DateTime(1900);
					$s_short[$i] = $syugyo->diff($syotei);
					$Shortage2[$i] = $s_short[$i]->format('%H:%I');
					if($bikou[$i]=="午前半休"){
						$bikou[$i]="前・早退";
						$Shortage=$Shortage2;
					}else{
						$Shortage[$i]=date('H:i',mktime(substr($Shortage[$i],0,2)+substr($Shortage2[$i],0,2),substr($Shortage[$i],3,2)+substr($Shortage2[$i],3,2)));
						$bikou[$i]="前・遅刻早退";
					}
				}
			}
			// $check[$i]="OK";
		}elseif($holiday[$i]==5){
			$bikou[$i]="午後半休";
			$h_syugyo=new DateTime($closong_get3);
			$h_syotei=new DateTime(date(("H:i"), mktime(04,00)));
			$h_short[$i] = $h_syugyo->diff($h_syotei);
			$h_kihon[$i] = $h_short[$i]->format('%H:%I');
			$h_kihon1[$i]=substr($h_kihon[$i],0,2);
			$h_kihon2[$i]=substr($h_kihon[$i],3,2);
			$h_kihon3[$i]=$h_kihon1[$i].$h_kihon2[$i];
			if($total[$i] >= "0400"){
				if($opening_get3 < $open_array[$i]){
					$syugyo=new DateTime($open_array[$i]);
					$syotei=new DateTime($opening_get3);
					$s_short[$i] = $syugyo->diff($syotei);
					$Shortage[$i] = $s_short[$i]->format('%H:%I');
					$bikou[$i]="後・遅刻";
				}else{
					$Shortage[$i]="00:00:00";
				}
			}elseif($opening_get3 < $open_array[$i]){
				$syugyo=new DateTime($total[$i]);
				$syotei=new DateTime(date(("H:i"), mktime(04,00)));
				$s_short[$i] = $syugyo->diff($syotei);
				$Shortage[$i] = $s_short[$i]->format('%H:%I');
				$bikou[$i]="後・遅刻";
			}elseif($opening_get3 == $open_array[$i]){
				$syugyo=new DateTime($total[$i]);
				$syotei=new DateTime(date(("H:i"), mktime(04,00)));
				$s_short[$i] = $syugyo->diff($syotei);
				$Shortage[$i] = $s_short[$i]->format('%H:%I');
				$bikou[$i]="後・早退";
			}else{
				$Shortage[$i]="";
			}
			if($opening_get3 < $open_array[$i]){
				if($close_array[$i] < $h_kihon3[$i]){
				$syugyo=new DateTime($total[$i]);
				$syotei=new DateTime(date(("H:i"), mktime(04,00)));
				$s_short[$i] = $syugyo->diff($syotei);
				$Shortage[$i] = $s_short[$i]->format('%H:%I');
				$bikou[$i]="後・遅刻早退";
				}
			}
		}
		//土曜日の残業時間s//
		if($week_day4[$i]===$kensaku[$i]){
			if($close_array[$i]>=2230){
				if($rest[$i]==""||$rest2[$i]==""){
					$overtime[$i]="13:30";
				}else{
					$moto=new DateTime(date(("Y/m/d H:i"), mktime(substr($total[$i],0,2),substr($total[$i],3,2))));
					$hiku=new DateTime(date(("Y/m/d H:i"), mktime($rest[$i],$rest2[$i])));
					$t_over[$i] = $moto->diff($hiku);
					$overtime[$i] = $t_over[$i]->format('%H:%I');
				}
			}else{
				$overtime[$i]=$total[$i];
			}
			//22時以上、29時（翌朝5時）までの場合
			if($close_array[$i]>=2230 && $close_array[$i]<=2900){
				$moto=new DateTime(date(("Y/m/d H:i"), mktime($close[$i],$close2[$i])));
				$hiku=new DateTime(2230);

				$t_over[$i] = $moto->diff($hiku);
				$overtime_night[$i] = $t_over[$i]->format('%H:%I');
			}
			//29時以上、34時以下の場合
			elseif($close_array[$i]>=2930 && $close_array[$i]<=3400){
				$moto1=new DateTime(date(("Y/m/d H:i"), mktime($close[$i],$close2[$i])));
				$hiku1=new DateTime(date(("Y/m/d H:i"), mktime(29,30)));
				$t_over[$i] = $hiku1->diff($moto1);
				$overtime1[$i] = $t_over[$i]->format('%H:%I');
				$overtime[$i]= date('H:i',mktime(substr($overtime[$i],0,2)+substr($overtime1[$i],0,2),(substr($overtime[$i],3,2)+substr($overtime1[$i],3,2))));
				$overtime_night[$i]="06:30";
			}elseif($close_array[$i]==2915){
				$moto1=new DateTime(date(("Y/m/d H:i"), mktime($close[$i],$close2[$i])));
				$hiku1=new DateTime(date(("Y/m/d H:i"), mktime(29,15,)));
				$t_over[$i] = $hiku1->diff($moto1);
				$overtime1[$i] = $t_over[$i]->format('%H:%I');
				$overtime[$i]= date('H:i',mktime(substr($overtime[$i],0,2)+substr($overtime1[$i],0,2),(substr($overtime[$i],3,2)+substr($overtime1[$i],3,2))));
				$overtime_night[$i]="06:30";
			}
			//土曜日の残業時間e//
			}else{
			//所定の終業時間以上、22時以下の場合かつ平日
			if($close_array[$i] >= $closong_get3){
				if($close_array[$i]<=2200){
					if($opening_get=="08" && $closong_get=="17"){
						$moto=new DateTime($close_array[$i]);
						$hiku=new DateTime(1730);
						$t_over[$i] = $moto->diff($hiku);
						$overtime[$i] = $t_over[$i]->format('%H:%I');
					}elseif($opening_get=="09" && $closong_get==17 || $opening_get=="09" && $closong_get==18){
						$moto=new DateTime($close_array[$i]);
						$hiku=new DateTime(1800);
						$t_over[$i] = $moto->diff($hiku);
						$overtime[$i] = $t_over[$i]->format('%H:%I');
					}elseif($opening_get=="10" && $closong_get==19){
						$moto=new DateTime($close_array[$i]);
						$hiku=new DateTime(1900);
						$t_over[$i] = $moto->diff($hiku);
						$overtime[$i] = $t_over[$i]->format('%H:%I');
					}
				}else{
					if($opening_get=="09" && $closong_get==17){
						$overtime[$i]="04:00";
					}else{
						$moto=new DateTime($closong);
						$hiku=new DateTime(2200);
						$t_over[$i] = $hiku->diff($moto);
						$overtime[$i] = $t_over[$i]->format('%H:%I');
					}
				}

				//22時以上、29時（翌朝5時）までの場合
				if($close_array[$i]>=2230 && $close_array[$i]<=2900){
					if($shift[$i]=="0"){
						$moto=new DateTime(date(("Y/m/d H:i"), mktime($close[$i],$close2[$i])));
						$hiku=new DateTime(2230);
					}else{
						$moto=new DateTime(date(("Y/m/d H:i"), mktime($s_close[$i],$s_close2[$i])));
						$hiku=new DateTime(date(("Y/m/d H:i"),mktime(22,30)));
					}
						$t_over[$i] = $moto->diff($hiku);
						$overtime_night[$i] = $t_over[$i]->format('%H:%I');
				}
				//29時以上、34時以下の場合
				elseif($close_array[$i]>=2930 && $close_array[$i]<=3400){
					if($shift[$i]=="0"){
						$moto1=new DateTime(date(("Y/m/d H:i"), mktime($close[$i],$close2[$i])));
						$hiku1=new DateTime(date(("Y/m/d H:i"), mktime(29,30)));
					}else{
						$moto1=new DateTime(date(("Y/m/d H:i"), mktime($s_close[$i],$s_close2[$i])));
						$hiku1=new DateTime(date(("Y/m/d H:i"), mktime(29,30)));
					}
					$t_over[$i] = $hiku1->diff($moto1);
					$overtime1[$i] = $t_over[$i]->format('%H:%I');
					$overtime[$i]= date('H:i',mktime(substr($overtime[$i],0,2)+substr($overtime1[$i],0,2),(substr($overtime[$i],3,2)+substr($overtime1[$i],3,2))));
					$overtime_night[$i]="06:30";
				}elseif($close_array[$i]==2915){
					if($shift[$i]=="0"){
						$moto1=new DateTime(date(("Y/m/d H:i"), mktime($close[$i],$close2[$i])));
						$hiku1=new DateTime(date(("Y/m/d H:i"), mktime(29,15,)));
					}else{
						$moto1=new DateTime(date(("Y/m/d H:i"), mktime($s_close[$i],$s_close2[$i])));
						$hiku1=new DateTime(date(("Y/m/d H:i"), mktime(29,15)));
					}
					$t_over[$i] = $hiku1->diff($moto1);
					$overtime1[$i] = $t_over[$i]->format('%H:%I');
					$overtime[$i]= date('H:i',mktime(substr($overtime[$i],0,2)+substr($overtime1[$i],0,2),(substr($overtime[$i],3,2)+substr($overtime1[$i],3,2))));
					$overtime_night[$i]="06:30";
					}
				}
			}
			//日曜日の残業時間（普通残業はなく、全て深夜残業）
			if($week_day5[$i]===$kensaku[$i]){
				$overtime[$i]="00:00";
			 	$overtime_night[$i]=$total[$i];
			}
	//チェックOKのみe//
	}
	if($holiday[$i]=="1"){
		$bikou[$i]="有休";
		$open[$i]="00";
		$open2[$i]="00";
		$close[$i]="00";
		$close2[$i]="00";
		$rest[$i]="00";
		$rest2[$i]="00";
		$total[$i]="00";
		$check[$i]="OK";
	}elseif($holiday[$i]=="2"){
		$bikou[$i]="振休";
		$open[$i]="00";
		$open2[$i]="00";
		$close[$i]="00";
		$close2[$i]="00";
		$rest[$i]="00";
		$rest2[$i]="00";
		$total[$i]="00";
		$check[$i]="OK";
	}elseif($holiday[$i]=="3"){
		$bikou[$i]="特休";
		$open[$i]="00";
		$open2[$i]="00";
		$close[$i]="00";
		$close2[$i]="00";
		$rest[$i]="00";
		$rest2[$i]="00";
		$total[$i]="00";
		$check[$i]="OK";
	}
	//勤務時間の入力がなければ何も入れない
	if($open[$i]=="00"&& $close[$i]=="00" && $open2[$i]=="00"&& $close2[$i]=="00"){
		$Shortage[$i]="00:00";
		if($holiday[$i]=="1" || $holiday[$i]=="2" || $holiday[$i]=="3"){
			$check[$i]="OK";
		}else{
			$check[$i]="";
		}
	}	
	if($close[$i]>='24'){
			switch ($close[$i]) {
			//終業時間が以下の場合、closeに+24時間する
				case '24':
					$close[$i]=$close[$i]-24;
					break;
				case '25':
					$close[$i]=$close[$i]-24;
					break;
				case '26':
					$close[$i]=$close[$i]-24;
					break;
				case '27':
					$close[$i]=$close[$i]-24;
					break;
				case '28':
					$close[$i]=$close[$i]-24;
					break;
				case '29':
					$close[$i]=$close[$i]-24;
					break;
				case '30':
					$close[$i]=$close[$i]-24;
					break;
				case '31':
					$close[$i]=$close[$i]-24;
					break;
				case '32':
					$close[$i]=$close[$i]-24;
					break;
				case '33':
					$close[$i]=$close[$i]-24;
					break;
				case '34':
					if($opening_get==10){
						$close[$i]=$close[$i]-24;
					}
					break;
				default:
					break;
			}
		}
//for分の終わり
}
//エラーメッセージがなければkinmu_insertファイルに遷移
	$_SESSION['staff_number']=$staff_number;
	$_SESSION['get_month']=$first_date;
	$_SESSION['naiyou']=$naiyou;
	$_SESSION['open']=$open;
	$_SESSION['open2']=$open2;
	$_SESSION['close']=$close;
	$_SESSION['close2']=$close2;
	$_SESSION['rest']=$rest;
	$_SESSION['rest2']=$rest2;
	$_SESSION['total']=$total;
	$_SESSION['overtime']=$overtime;
	$_SESSION['overtime_night']=$overtime_night;
	$_SESSION['Shortage']=$Shortage;
	$_SESSION['bikou']=$bikou;
	$_SESSION['holiday']=$holiday;
	$_SESSION['check']=$check;
	$_SESSION['shift']=$shift;
	header('Location:kinmu_insert.php');
	exit;
	?>

