	<?php
	if(!isset($_SESSION)){
		session_start();
	}
	ob_start();
	include("kinmuhyo.php");
	ob_clean();
		//require'kinmu_common.php';
		$result=$_SESSION['result'];
		$staff_number=$result['staff_number'];
		$kinmuhyo_attendance= kinmu_common::Attendance($result['staff_number']);
		$BELONGSS= kinmu_common::BELONGSS($result['staff_number']);
		$opening=$BELONGSS['opening_hours'];
		$opening_get=substr($opening,0,2);
		$opening_get2=substr($opening,3,2);
		$opening_get3=$opening_get.$opening_get2;
		$closong=$BELONGSS['closing_hours'];
		$closong_get=substr($closong,0,2);
		$closong_get2=substr($closong,3,2);
		$closong_get3=$closong_get.$closong_get2;
		$work=$closong_get-$opening_get-1;
		
		$week=$_POST['week'];

		$week_day=array_keys($week, '土');
		$week_day2=array_keys($week, '日');
		$week_day3=array_merge($week_day,$week_day2);
		sort($week_day3);

		date_default_timezone_set('Asia/Tokyo');
		//insert用日付
		$first_date = date("Y-").$_POST['month'].date("-01");
		//今月末
		$now_month = date('t', strtotime($first_date));
		//今日日付
		$today = date("Y/m/d");

		$week_day5 =array_fill(0, $now_month, 'ダミー');
		foreach($week_day as $key => $value){
    		$key = $value;
    		$week_day5[$key]=$value;
		}
		$week_day6 =array_fill(0, $now_month, 'ダミー');
		foreach($week_day2 as $key => $value){
    		$key = $value;
    		$week_day6[$key]=$value;
		}

		$week_day4 =array_fill(0, $now_month, 'ダミー');
		foreach($week_day3 as $key => $value){
    		$key = $value;
    		$week_day4[$key]=$value;
		}
		for($i = 0; $i <= $now_month ; $i++){
			$kensaku[]=$i;
		}

		$naiyou=$_POST['naiyou'];
		$open=$_POST['open'];
		$close=$_POST['close'];
		$open2=$_POST['open2'];
		$close2=$_POST['close2'];
		$holiday=$_POST['holiday'];
		$shift=$_POST['shift_kinmu'];
		$naiyou = array_map('htmlspecialchars', $naiyou);

		for($i = 0; $i < $now_month ; $i++){
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
			$rest=$_POST['rest'];
			$rest2=$_POST['rest2'];
			$total[$i]="00:00";
			$overtime[$i]="00:00";
			$overtime_night[$i]="00:00";
			$Shortage[$i]="00:00";
			//$err_msg[$i]="";
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

			if($shift[$i]=="1"){
				$shift_total[$i]=$open[$i]-$opening_get;
				$shift_total2[$i]=$open2[$i]-$opening_get2;
				$s_open[$i]=$open[$i]-$shift_total[$i];
				$s_open2[$i]=$open2[$i]-$shift_total2[$i];
				$s_close[$i]=$close[$i]-$shift_total[$i];
				$s_close2[$i]=$close2[$i]-$shift_total2[$i];

				//時、分を結合する
				foreach ((array)$s_open[$i] as $o_key => $op) {
					foreach ((array)$s_open2[$i] as $o_key2 => $op2) {
						if($o_key==$o_key2){
								$open_array[$i] = sprintf('%02d',$op).sprintf('%02d',$op2);
						}
					}
				}
				foreach ((array)$s_close[$i] as $c_key => $cl) {
					foreach ((array)$s_close2[$i] as $c_key2 => $cl2) {
						if($c_key==$c_key2){
							$close_array[$i] = sprintf('%02d',$cl).sprintf('%02d',$cl2);
							
						}
					}
				}
			}else{
			//シフトにフラグがない場合
			//時、分を結合する
				foreach ((array)$open[$i] as $o_key => $op) {
					foreach ((array)$open2[$i] as $o_key2 => $op2) {
						if($o_key==$o_key2){
								$open_array[] = $op.$op2;
						}
					}
				}
				foreach ((array)$close[$i] as $c_key => $cl) {
					foreach ((array)$close2[$i] as $c_key2 => $cl2) {
						if($c_key==$c_key2){
							$close_array[] = $cl.$cl2;

							
							
						}
					}
				}
			}
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
		}
		
			//シフトフラグあり、かつ始業・終業ブランク
			if($check[$i]=="OK"){
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
			//始業時間>終業時間
				if($shift!="1"){
				if($open_array[$i] > $close_array[$i]){
				$check[$i]="NG";
				$err_msg[$i]="時刻逆転エラー" ;
				}
			}
		}
		if($check[$i]=="OK"){
		//半休フラグがある場合
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
			if($week_day4[$i]===$kensaku[$i]){
				if($rest[$i]=="" && $rest2[$i]==""){
					$rest[$i]="";
					$rest2[$i]="";
				}
			}else{
			//休憩時間(通常)
			if($check[$i]=="OK" && $open_array[$i]!="" && $close_array[$i]!=""){
			if($open_array[$i]<=1200 && $close_array[$i]<=1200){
				$rest[$i]="00";
				$rest2[$i]="00";
			}
			elseif($close_array[$i] > 1200 && $close_array[$i] < 1300){
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
			elseif($open_array[$i]<=1200 && $close_array[$i]<=2200){
				$rest[$i]="01";
				$rest2[$i]="00";
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
				//ふたつの日付の差をあらわす DateInterval オブジェクトを返します。
				$objInterval[$i] = $objDatetime1->diff($objDatetime2);
				 
				$total[$i] = $objInterval[$i]->format('%H:%I');
			
			if($close[$i]=="22" && $close2[$i]=="15"){
				$rest2[$i]="30";
			}elseif($close[$i]=="29" && $close2[$i]=="15"){
				$rest[$i]="02";
				$rest2[$i]="00";
			}

			if($shift[$i]=="1"){
			if($s_close[$i]=="22" && $s_close2[$i]=="15"){
				$rest2[$i]="30";
			}elseif($s_close[$i]=="29" && $s_close2[$i]=="15"){
				$rest[$i]="02";
				$rest2[$i]="00";
			}
			}
			if($week_day4[$i]===$kensaku[$i]){
				$bikou[$i]="";
			}else{
			if($shift[$i]=="0" && $open_array[$i]!="" && $close_array[$i]!=""){
			//遅刻判定
			$Shortage[$i]="00:00";
			$overtime_night[$i] = "00:00";
			$n = $open_array[$i];
			$k = $opening;
				 
			//DateTimeクラス
			$n_open = new DateTime($n);
			$k_open = new DateTime($k);

			//diffメソッド
			//ふたつの日付の差をあらわす DateInterval オブジェクトを返します。
			$tikoku[$i] = $n_open->diff($k_open);
			$t_tikoku[$i] = $tikoku[$i]->format('%H:%I');

			
				if($n_open > $k_open){
					$Shortage[$i]= $t[$i];
					$bikou[$i]="遅刻";
				}
			//早退判定
					$kihon = $close_array[$i];
					$syugyo = $closong;
					$kiohon1 = new DateTime($kihon);
					$syugyo = new DateTime($syugyo);
					$soutai[$i] = $kiohon1->diff($syugyo);
					$s[$i] = $soutai[$i]->format('%H:%I');
					
				if($kiohon1 < $syugyo){
					if($Shortage[$i]=="00:00"){
						$Shortage[$i] = $s[$i];
						$bikou[$i]="早退";
					}else{
							$t_tikoku[$i] = filter_var($t_tikoku[$i],FILTER_SANITIZE_NUMBER_INT);
							$s[$i] = filter_var($s[$i],FILTER_SANITIZE_NUMBER_INT);
							$t1[$i]=substr($t_tikoku[$i],0,2);
							$t2[$i]=substr($t_tikoku[$i],2,2);
							$s1[$i]=substr($s[$i],0,2);
							$s2[$i]=substr($s[$i],2,2);

							$Shortage[$i] = date('H:i',mktime($t1[$i]+$s1[$i],$t2[$i]+$s2[$i]));				
							$bikou[$i]="遅刻早退";
					}
				}
			}
		}
				//半休判定(午前半休)
				if($holiday[$i]==4){
					$bikou[$i]="午前半休";
					//遅刻判定
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
					}else{
						$Shortage[$i] ="00:00";
					}
					//早退判定
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
						}elseif($closong_get=="10"){
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
						}else{
							$Shortage[$i]="00:00";
						}
						$check[$i]="OK";
						//午後半休
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
					$check[$i]="OK";
				}
				//土曜日の残業時間
				if($week_day5[$i]===$kensaku[$i]){
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
					if($shift[$i]==0){
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
					}
					else{
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
			//土曜日は通常残業時間までの勤務時間が通常残業となり以降は平日と同じ扱い（ただし、休憩はセルフ入力）
			 if($week_day6[$i]===$kensaku[$i]){
			 	$overtime[$i]="00:00";
			 	$overtime_night[$i]=$total[$i];
			 }
		//休暇判定
		if($check[$i]=="OK"){
		if($holiday[$i]==1){
					$bikou[$i]="有休";
					$total[$i]="00:00:00";
					// $Shortage[$i]="00:00:00";
					$rest[$i]="";
					$rest2[$i]="";
					$open[$i]="";
					$open2[$i]="";
					$close[$i]="";
					$close2[$i]="";
					// $overtime[$i]="00:00:00";
					// $overtime_night[$i]="00:00:00";
					$Shortage[$i]="00:00:00";
					$check[$i]="OK";
				}elseif($holiday[$i]==2){
					$bikou[$i]="振休";
					$total[$i]="00:00:00";
					// $Shortage[$i]="00:00:00";
					$rest[$i]="";
					$rest2[$i]="";
					$open[$i]="";
					$open2[$i]="";
					$close[$i]="";
					$close2[$i]="";
					// $overtime[$i]="00:00:00";
					// $overtime_night[$i]="00:00:00";
					$Shortage[$i]="00:00:00";
					$check[$i]="OK";
				}elseif($holiday[$i]==3){
					$bikou[$i]="特休";
					$total[$i]="00:00:00";
					// $Shortage[$i]="00:00:00";
					$rest[$i]="";
					$rest2[$i]="";
					$open[$i]="";
					$open2[$i]="";
					$close[$i]="";
					$close2[$i]="";
					// $overtime[$i]="00:00:00";
					// $overtime_night[$i]="00:00:00";
					$Shortage[$i]="00:00:00";
					$check[$i]="OK";
				}
			}
		if($open[$i]=="00"&& $close[$i]=="00" && $open2[$i]=="00"&& $close2[$i]=="00"){	
				$Shortage[$i]="00:00";
				$check[$i]="";
			}
			if(isset($err_msg[$i])==""){
			try{
 			$dsn='mysql:dbname=kinmuhyo;host=localhost;charset=utf8';
 			$user='root';
 			$password='';
 			$dbh= new PDO($dsn,$user,$password);
 			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 			//レコードがなければinsert。レコードがあればupdateする。
 				$sql="INSERT INTO `TBL_ATTENDANCE`(`staff_number`, `year_and_month`, `content`, `opening_hours`, `closing_hours`, `break_time`, `total`, `overtime_normal`, `overtime_night`, `short`, `bikou`, `vacation`, `check_result`, `shift`, `create_date`, `update_date`) VALUES (:staff_number, :year_and_month, :content, :opening_hours, :closing_hours, :break_time, :total, :overtime_normal, :overtime_night, :short, :bikou, :vacation, :check_result, :shift, now(), now())on duplicate key update staff_number=$staff_number, content = :content, opening_hours = :opening_hours, closing_hours = :closing_hours, break_time = :break_time, total = :total, overtime_normal = :overtime_normal, overtime_night = :overtime_night, short=:short, bikou =:bikou, vacation = :vacation, check_result = :check_result, shift = :shift";
 				$stmt=$dbh->prepare($sql);
 				$params = array('staff_number' => $staff_number,'year_and_month' => $first_date, 'content' => $naiyou[$i], 'opening_hours' => $open[$i].$open2[$i]."00", 'closing_hours' => $close[$i].$close2[$i]."00", 'break_time' => $rest[$i].$rest2[$i]."00", 'total' => $total[$i], 'overtime_normal' => $overtime[$i], 'overtime_night' => $overtime_night[$i], 'short'=>$Shortage[$i],'bikou' =>$bikou[$i], 'vacation' =>$holiday[$i], 'check_result' => $check[$i], 'shift' => $shift[$i]);
 				$stmt->execute($params);
 				$dbh = null;	
 		}catch(Exception $e){
 			var_dump($e);
 			print 'システムエラーが発生しました。';
 			exit();
 		}
 		}else{
 			$_SESSION['err_msg']=$err_msg;
 			$_SESSION['check']=$check[$i];
 			header('Location:kinmuhyo.php');
			exit;
		}
		$first_date++;
		
 	}
 	$summary= kinmu_common::INSERT_Summary($result['staff_number']);
 	$_SESSION['err_msg']="";
 	$_SESSION['check']="";
 	header('Location:kinmuhyo.php');
	exit;

	?>