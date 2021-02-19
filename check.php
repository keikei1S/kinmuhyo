<?php
//ワーニングメッセージは非表示にする
error_reporting(0);

//セッションが始まっていなければセッションを開始する
if(!isset($_SESSION)){
  session_start();
}
//勤務表ファイルを読み込む
ob_start();
include("kinmu_common.php");
ob_clean();

//変数の整理---start-----//
$naiyou=$_POST['naiyou'];
$naiyou　= array_map('htmlspecialchars', $naiyou);
$open_ampm = $_POST['open_ampm'];
$open = $_POST['open'];
$close_ampm =$_POST['close_ampm'];
$close=$_POST['close'];
$rest = $_POST['rest'];
$holiday =$_POST['holiday'];
$shift =$_POST['shift_kinmu'];
$_SESSION["month"]=$_POST['month'];
//変数の整理---end-----//

//有給休暇数を取得する
foreach($holiday as $key =>$value){
//初期化
  $paid_digestion[$key] = "";
//有給休暇の場合
  if($value==1){
    $paid_digestion[$key]++;
//前半休の場合
  }elseif($value==4){
    $paid_digestion[$key]++;
    $paid_digestion[$key] = $paid_digestion[$key]/2;
//後半休の場合
  }elseif($value==5){
    $paid_digestion[$key]++;
    $paid_digestion[$key] = $paid_digestion[$key]/2;
  }
//前後半休は半日休暇の為2で割る
}

//有休休暇数と有給残数を比較し、有給休暇数が期初有給残数が上回っている場合勤務表画面に返す
if(array_sum($paid_digestion) > $_SESSION["yukyu"]){
  $_SESSION["yukyu_err"]="有給休暇数が有給残数を上回っています";
  $_SESSION['naiyou']=$naiyou;
  $_SESSION['open_ampm']=$open_ampm;
  $_SESSION['open']=$open;
  $_SESSION['close_ampm']=$close_ampm;
  $_SESSION['close']=$close;
  $_SESSION['rest']=$rest;
  $_SESSION['holiday']=$holiday;
  $_SESSION['shift']=$shift;
  header('Location:kinmuhyo.php');
  exit;
}

//所定始業時間から30分前を取得--start//
$BELONGSS= kinmu_common::BELONGSS($_SESSION['result']['staff_number']);
//始業時間を取得
$opening=$BELONGSS['opening_hours'];
$deduction_time= MinusVtime($opening,"00:30");
$opening = substr($opening, 0,5);
//--end--

//所定就業時間を取得---start
$closing=$BELONGSS['closing_hours'];
$closing = substr($closing, 0,5);
//所定就業時間を取得---end

//月日を取得--start
//月初
$first_date = $_SESSION["first_date"];
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
$week_day4 =array_fill(0, $now_month, '土曜以外');
foreach($week_day as $key => $value){
  $key = $value;
  $week_day4[$key]=$value;
}
//日曜の配列作成
$week_day5 =array_fill(0, $now_month, '日曜以外');
foreach($week_day2 as $key => $value){
  $key = $value;
  $week_day5[$key]=$value;
}
//土日祝の配列作成
$week_day6 =array_fill(0, $now_month, '平日');
foreach($week_day3 as $key => $value){
  $key = $value;
  $week_day6[$key]=$value;
}
//平日の取得
for($i = 0; $i <= $now_month ; $i++){
  $business_day[]=$i;
}
//有休残数の初期化
$y_kyuka="";
//該当月の日数分for分を回す
for($i = 0; $i < $now_month ; $i++){
//　変数の初期化--start--
//実働時間の初期化
  $total[$i]="00:00";
//普通残業時間の初期化
  $overtime[$i]="00:00";
//深夜残業時間の初期化
  $overtime_night[$i]="00:00";
//不足時間の初期化
  $Shortage[$i]="00:00";
//備考メッセージの初期化
  $bikou[$i]="";
//チェックの初期化
  $check[$i]="";
//エラーメッセージの初期化
  $err_msg[$i]="";

//休暇関連チェック
//シフトフラグありかつ休暇フラグあり
  if($shift[$i]=="1"&& $holiday[$i]!="0"){
    $check[$i]="NG";
    $err_msg[$i]="フラグ整合性エラー";
  }else{
    $check[$i]="OK";
  }
  if($check[$i]=="OK"){
    if($holiday[$i]=="1"){
      $bikou[$i]="有休";
      $yasumi[$i]="1";
      $y_kyuka++;
    }elseif($holiday[$i]=="2"){
      $bikou[$i]="振休";
      $yasumi[$i]="1";
    }elseif($holiday[$i]=="3"){
      $bikou[$i]="特休";
      $yasumi[$i]="1";
    }elseif($holiday[$i]=="6"){
      $bikou[$i]="欠勤";
      $yasumi[$i]="1";
    }else{
      $yasumi[$i]="0";
      $check[$i]="OK";
    }
//休暇判定---end---
//エラーチェック兼時間調整
//休暇フラグがない場合
    if($yasumi[$i]=="0"){
      if($week_day6[$i]!==$i){
//全ての項目がブランクの場合、欠勤フラグを入れさせるよう促す
        if($open_ampm[$i]=="" && $open[$i]=="" && $close_ampm[$i]=="" && $close[$i]==""){
          $check[$i]="NG";
          $err_msg[$i]="欠勤フラグを立ててください";
        }
//始業エリアがブランクの場合
        elseif($open_ampm[$i]=="" && $open[$i]==""){
          $check[$i]="NG";
          $err_msg[$i]="始業時間を選択してください";
          $open_ampm[$i]=NULL;
          $open[$i]=NULL;
//終業エリアがブランクの場合
        }elseif($close_ampm[$i]=="" && $close[$i]==""){
          $check[$i]="NG";
          $err_msg[$i]="終業時間を選択してください";
          $close_ampm[$i]=NULL;
          $close[$i]=NULL;
        }else{
//午前午後フラグチェック
          if($open_ampm[$i]=="" && $close_ampm[$i]==""){
            $check[$i]="NG";
            $err_msg[$i]="[始業・終業]午前午後を選択してください";
            $open_ampm[$i]=NULL;
            $close_ampm[$i]=NULL;
          }
          elseif($open_ampm[$i]==""){
            $check[$i]="NG";
            $err_msg[$i]="[始業]午前午後を選択してください";
            $open_ampm[$i]=NULL;
          }
          elseif($close_ampm[$i]==""){
            $check[$i]="NG";
            $err_msg[$i]="[終業]午前午後を選択してください";
            $close_ampm[$i]=NULL;
          }
//始業終業時間のブランクチェック
          if($open[$i]=="" && $close[$i]==""){
            $check[$i]="NG";
            $err_msg[$i]="[始業・終業]時間を選択してください";
            $open[$i]=NULL;
            $close[$i]=NULL;
          }
//始業時間のブランクチェック
          elseif($open[$i]==""){
            $check[$i]="NG";
            $err_msg[$i]="始業時間を選択してください";
            $open[$i]=NULL;
          }
//終業時間のブランクチェック
          elseif($close[$i]==""){
            $check[$i]="NG";
            $err_msg[$i]="終業時間を選択してください";
            $close[$i]=NULL;
          }
          if($open_ampm[$i]!="" && $open[$i]=="" && $close_ampm[$i]!="" && $close[$i]==""){
            $check[$i]="NG";
            $err_msg[$i]="勤務時間を選択してください";
            $close[$i]= NULL;
          }
        }

//土日祝日の場合
      }else{
//午前午後フラグチェック
//始業≠ブランク、終業＝ブランク
        if($open_ampm[$i]=="" && $open[$i]=="" && $close_ampm[$i]=="" && $close[$i]=="" && $rest[$i]==""){
          $yasumi[$i]=1;
          $check[$i]==NULL;
        }elseif($open_ampm[$i]!="" && $open[$i]!="" && $close_ampm[$i]!="" && $close[$i]!="" && $rest[$i]!=""){
          $check[$i]="OK";
        }else{
          if($open_ampm[$i]==""){
            $check[$i]="NG";
            $err_msg[$i]="[始業]午前午後を選択してください";
            $open_ampm[$i]=NULL;
          }
          if($close_ampm[$i]==""){
            $check[$i]="NG";
            $err_msg[$i]="[終業]午前午後を選択してください";
            $close_ampm[$i]=NULL;
          }
          if($open_ampm[$i]=="" && $close_ampm[$i]==""){
            $check[$i]="NG";
            $err_msg[$i]="[始業・終業]午前午後を選択してください";
            $open_ampm[$i]=NULL;
            $close_ampm[$i]=NULL;
          }
          if($open[$i]==""){
            $check[$i]="NG";
            $err_msg[$i]="始業時間を選択してください";
            $open[$i]=NULL;
          }
          if($close[$i]==""){
            $check[$i]="NG";
            $err_msg[$i]="終業時間を選択してください";
            $close[$i]=NULL;
          }
          if($rest[$i]==""){
            $check[$i]="NG";
            $err_msg[$i]="休憩時間を選択してください";
            $close[$i]=NULL;
          }
          if($open[$i]=="" && $close[$i]==""){
            $check[$i]="NG";
            $err_msg[$i]="始業・終業時間を選択してください";
            $open[$i]=NULL;
            $close[$i]=NULL;
          }
        }
      }
//午前午後の入力によって始業・終業時間を調整
      if($check[$i]=="OK"){
//APの場合入力時間に+24時間する
        if($open_ampm[$i]==3){
          $open_pm1[$i] = explode(":", $open[$i])[0] + 24;
          $open_pm2[$i] = explode(":", $open[$i])[1];
          $open_pm[$i] = $open_pm1[$i].":".$open_pm2[$i];
//PMの場合、12時間を足す(始業時間)
        }elseif($open_ampm[$i]==2){
          $open_pm1[$i] = explode(":", $open[$i])[0] + 12;
          $open_pm2[$i] = explode(":", $open[$i])[1];
          $open_pm[$i] = $open_pm1[$i].":".$open_pm2[$i];
//始業時間の場合のamはそのまま使用する
        }elseif($open_ampm[$i]==1){
          $open_pm[$i] = $open[$i];
        }
        //平日の場合以下の時間は始業時間を調整する
        if($week_day6[$i]!==$i){
          if($open_pm[$i]>="05:00" && $open_pm[$i] < "05:30"){
            $open_pm[$i]="05:00";
          }elseif($open_pm[$i]=="08:45"){
            $open_pm[$i]="08:30";
          }elseif ($open_pm[$i]>"12:00" && $open_pm[$i] < "13:00") {
            $open_pm[$i]="12:00";
          }elseif($open_pm[$i]=="22:15"){
            $open_pm[$i]="22:00";
          }elseif($open_pm[$i]>"24:00" && $open_pm[$i] < "25:00"){
            $open_pm[$i]="24:00";
          }elseif($open_pm[$i]=="29:15"){
            $open_pm[$i]="29:00";
          }
        }
//終業時間がAMの場合、そのままの値を代入する
    if($close_ampm[$i]== 1){
      $close_pm[$i] = $close[$i];
//終業がPMの場合、+12時間する
    }elseif($close_ampm[$i]== 2){
      $close_pm1[$i] = explode(":", $close[$i])[0] + 12;
      $close_pm2[$i] = explode(":", $close[$i])[1];
      $close_pm[$i] = $close_pm1[$i].":".$close_pm2[$i];
//終業時間がAPは場合、+24時間する
    }elseif($close_ampm[$i]== 3){
      $close_pm1[$i] = explode(":", $close[$i])[0] + 24;
      $close_pm2[$i] = explode(":", $close[$i])[1];
      $close_pm[$i] = $close_pm1[$i].":".$close_pm2[$i];

//終業時間の上限を設定
//所定の始業時間の24時間後
      $max_opening1 = explode(":", $opening)[0] + 24;
      $max_opening2 = explode(":", $opening)[1];
      $max_opening = $max_opening1.":".$max_opening2;

//入力した始業時間の24時間後
      $max_opening3[$i] = explode(":", $open_pm[$i])[0] + 24;
      $max_opening4[$i] = explode(":", $open_pm[$i])[1];
      $max_opening5[$i] = $max_opening3[$i].":".$max_opening4[$i];

//所定始業時間の24時間後と入力した始業時間の24時間後を比較し、小さい値の方を上限値としてエラーチェックする
      if($max_opening > $max_opening5[$i]){
        $max_opening = $max_opening5[$i];
      }
      if($max_opening < $close_pm[$i]){
        $check[$i]="NG";
        $err_msg[$i]="終業時間は所定始業時間の24時間後又は始業時間の24時間後までです。";
      }
    }
//就業時間が以下の場合、就業時間を調整する(シフト以外)
//(以下の時間は休憩時間の調整が必要、最初から以下の時間が入力された場合は裏側で処理する？？)
    if($shift[$i] == 1 && $err_msg[$i]!="フラグ整合性エラー"){
//シフトフラグあり、かつ始業・終業ブランク
      if($open_ampm[$i]=="" || $open_pm[$i]=="" || $close_ampm[$i]=="" || $close_pm[$i]==""){
        $check[$i]="NG";
        $err_msg[$i]="シフト勤務エラー";
      }else{
        $bikou[$i]="シフト勤務";
      }
//遅番の場合
      if($open_pm[$i] >= $opening){
//始業時間から所定始業時間を引き、差分を求める
        $shift_total[$i]= minVtime1($open_pm[$i],$opening);
//計算用に始業時間をと調整する
        $open_pm[$i] = minVtime1($open_pm[$i],$shift_total[$i]);
//求まった差分を終業時間から引く
// $close_pm[$i] = minVtime1($close_pm[$i],$shift_total[$i]);
        $objDatetime1[$i] = new DateTime(date(("Y-m-d H:i"),mktime(explode(":", $close_pm[$i])[0],explode(":", $close_pm[$i])[1])));
        $objDatetime2[$i] = new DateTime(date(("Y-m-d H:i"),mktime(explode(":", $shift_total[$i])[0],explode(":", $shift_total[$i])[1])));
//ふたつの日付の差をあらわす DateInterval オブジェクトを返す。
        $objInterval[$i] = $objDatetime1[$i]->diff($objDatetime2[$i]);
//日跨ぎの場合
        $day_difference[$i] = $objInterval[$i]->format('%d');
//実働時間
        $time_day_difference[$i] = $objInterval[$i]->format('%H:%I');

        if($day_difference[$i]=="1" && $time_day_difference[$i]!="0"){
          $close_pm1[$i] = explode(":", $time_day_difference[$i])[0] + 24;
          $close_pm2[$i] = explode(":", $time_day_difference[$i])[1];
          if(mb_strlen($close_pm2[$i])==1){
            $close_pm2[$i] = sprintf("%02d", $close_pm2[$i]);
          }
          $close_pm[$i] = $close_pm1[$i].":".$close_pm2[$i];
        }else{
          $close_pm1[$i] = explode(":", $close_pm[$i])[0] - explode(":", $shift_total[$i])[0];
          $close_pm2[$i] = explode(":", $close_pm[$i])[1] - explode(":", $shift_total[$i])[1];
          if(mb_strlen($close_pm2[$i])==1){
            $close_pm2[$i] = sprintf("%02d", $close_pm2[$i]);
          }
          $close_pm[$i] = $close_pm1[$i].":".$close_pm2[$i];
        }
//早番の場合
      }elseif($open_pm[$i] < $opening){
//始業時間から所定始業時間を引き、差分を求める
        $shift_total[$i]= minVtime1($opening,$open_pm[$i]);
//計算用に始業時間をと調整する
        $open_pm[$i] = AddVtime1($open_pm[$i],$shift_total[$i]);
//求まった差分を終業時間に足す
        $close_pm1[$i] = explode(":", $close_pm[$i])[0] + explode(":", $shift_total[$i])[0];
        $close_pm2[$i] =explode(":", $close_pm[$i])[1] + explode(":", $shift_total[$i])[1];
        if(mb_strlen($close_pm2[$i])==1){
          $close_pm2[$i] = sprintf("%02d", $close_pm2[$i]);
        }
        $close_pm[$i] = $close_pm1[$i].":".$close_pm2[$i];
      }
    }
    //平日の場合終業時間を調整する
    if($week_day6[$i]!==$i){
      if($close_pm[$i] > "12:00" && $close_pm[$i] < "13:00"){
        $close_pm[$i]="13:00";
      }elseif($close_pm[$i]=="22:15"){
        $close_pm[$i]="22:30";
      }elseif($close_pm[$i] > "24:00" && $close_pm[$i] < "25:00"){
        $close_pm[$i]="25:00";
      }elseif($close_pm[$i]=="29:15"){
        $close_pm[$i]="29:30";
      }
    }
  }
//休暇フラグがない場合
    if($yasumi[$i]=="0"){
      if($check[$i]=="OK"){
//始業時間が終業時間よりも遅い場合
//(所定始業時間の24時間以内の時刻逆転については判定しない)
        if($open_pm[$i] > $close_pm[$i]){
          $check[$i]="NG";
          $err_msg[$i]="時刻逆転エラー" ;
        }
//始業時間と終業時間が同じ場合
//(所定始業時間の24時間後は同一時刻とみなさず、24時間ごと判定する)
        if(isset($open_pm[$i]) && isset($close_pm[$i])){
          if($open_pm[$i] == $close_pm[$i]){
            $check[$i]="NG";
            $err_msg[$i]="同一時刻エラー" ;
          }
        }
      }
/////////////エラーチェックend/////////////////

//休憩時間の処理--start---
      if($check[$i]=="OK"){
        if($week_day6[$i]!==$business_day[$i]){
//平日の場合
//$opening=所定始業時間
          if($open_pm[$i]!="" && $close_pm[$i]!=""){
            if($close_pm[$i]<="12:00"){
              if($open_pm[$i]>= $opening && $open_pm[$i] < "12:00"){
                $rest[$i]="00:00";
              }else{
                $rest[$i]="00:30";
              }
            }
            if($close_pm[$i] >= "13:00"){
              if($open_pm[$i] < $opening){
                $rest[$i]="01:30";
              }elseif($open_pm[$i] <= "12:00"){
                $rest[$i]="01:00";
              }else{
                $rest[$i]="00:00";
              }
            }
            if($close_pm[$i] > "22:00"){
              if($open_pm[$i] < $opening){
                $rest[$i]="02:00";
              }elseif($open_pm[$i] <= "12:00"){
                $rest[$i]="01:30";
              }elseif($open_pm[$i] <= "22:00"){
                $rest[$i]="00:30";
              }
              else{
                $rest[$i]="00:00";
              }
            }
            if($close_pm[$i] >= "24:00"){
              if($open_pm[$i] < $opening){
                $rest[$i]="02:00";
              }elseif($open_pm[$i] <= "12:00"){
                $rest[$i]="01:30";
              }elseif($open_pm[$i] <= "22:00"){
                $rest[$i]="00:30";
              }else{
                $rest[$i]="00:00";
              }
            }

            if($close_pm[$i] >= "25:00"){
              if($open_pm[$i] < $opening){
                $rest[$i]="03:00";
              }elseif($open_pm[$i] <= "12:00"){
                $rest[$i]="02:30";
              }elseif($open_pm[$i] <= "22:00"){
                $rest[$i]="01:30";
              }elseif($open_pm[$i] <= "24:00"){
                $rest[$i]="01:00";
              }else{
                $rest[$i]="00:00";
              }
            }
            if($close_pm[$i] > "29:00"){
              if($open_pm[$i] < $opening){
                $rest[$i]="03:30";
              }elseif($open_pm[$i] <= "12:00"){
                $rest[$i]="03:00";
              }elseif($open_pm[$i] <= "22:00"){
                $rest[$i]="02:00";
              }elseif($open_pm[$i] <= "24:00"){
                $rest[$i]="01:00";
              }elseif($open_pm[$i] <= "29:00"){
                $rest[$i]="00:30";
              }else{
                $rest[$i]="00:00";
              }
            }
            if($open_pm[$i] < "05:30"){
              $rest[$i]= AddVtime1($rest[$i],"00:30");
            }
          }
        }
//休憩時間の処理--end---

//実働時間の計算---start
//終業時間ー始業時間
      $base_total[$i]=MinusVtime($close_pm[$i],$open_pm[$i]);
      
//休憩控除前の時間が00:00(=24:00)の場合24時間を足す
      if($base_total[$i]=="00:00"){
        $base_total1[$i] = explode(":", $base_total[$i])[0] - explode(":", $base_total[$i])[0]+24;
        $base_total2[$i] = explode(":", $base_total[$i])[1] - explode(":", $base_total[$i])[1];
        if(mb_strlen($base_total2[$i])==1){
          $base_total2[$i] = sprintf("%02d", $base_total2[$i]);
        }
        $base_total[$i] = $base_total1[$i].":".$base_total2[$i];
      }
//休憩控除前の時間と休憩時間を比較し、休憩時間の方が大きい場合エラーとする
      if($base_total[$i] <= $rest[$i]){
        $check[$i]="NG";
        $err_msg[$i]="休憩時間が実働時間を上回っています";
        $total[$i]=null;
//休憩時間の方が小さい場合、休憩時間を適用し、実働時間とする
      }else{
        $total[$i]= MinusVtime($base_total[$i],$rest[$i]);
      }
//遅刻早退/半休判定---start
      if($week_day6[$i]!==$i){
        if($check[$i]=="OK"){
//if($shift[$i]!=1){
//休暇フラグがない場合、通常の遅刻早退処理
//遅刻処理
          if($holiday[$i]==0){
            if($opening < $open_pm[$i]){
              if($open_pm[$i] >="13:00" || $rest[$i]=="00:00" || $rest[$i]==""){
                $base_short = AddVtime1($opening,"01:00");
              }else{
                $base_short = $opening;
              }
              $Shortage[$i] = MinusVtime($open_pm[$i],$base_short);
              if($shift[$i]!=1){
                $bikou[$i]="遅刻";
              }else{
                $bikou[$i]="シフト勤務";
              }
            }
            if($closing > $close_pm[$i]){
              if($rest[$i]=="00:00" || $rest[$i]==""){
                $base_short = MinusVtime($closing,"01:00");
              }else{
                $base_short = $closing;
              }
              if($Shortage[$i]=="00:00"){
                $Shortage[$i] = MinusVtime($base_short,$close_pm[$i]);
                if($shift[$i]!=1){
                  $bikou[$i]="早退";
                }else{
                  $bikou[$i]="シフト勤務";
                }
//不足時間がある = 遅刻かつ早退
              }else{
                $Shortage2[$i] =  MinusVtime($base_short,$close_pm[$i]);
                $Shortage[$i] = AddVtime1($Shortage2[$i],$Shortage[$i]);
                if($shift[$i]!=1){
                  $bikou[$i]="遅刻・早退";
                }else{
                  $bikou[$i]="シフト勤務";
                }
              }
            }
          }elseif($holiday[$i]==4){
            $bikou[$i]="午前半休";
// 遅刻判定
//所定労働時間の半分
            $half_prescribed = "05:00";
//半休時の所定始業時間
            $base_time = AddVtime1($opening,$half_prescribed);
//入力された始業時間と半休時の所定始業時間を比較する
            if($base_time < $open_pm[$i]){
              $Shortage[$i] = MinusVtime($open_pm[$i],$base_time);
              $bikou[$i]="前・遅刻";
            }
//早退判定
//所定終業時間 > 実際の終業時間
            if($closing > $close_pm[$i]){
//不足時間がない場合 = 遅刻でない場合
              if($Shortage[$i]=="00:00"){
                $Shortage[$i] = MinusVtime($closing,$close_pm[$i]);
                $bikou[$i]="前・早退";
//不足時間がある = 遅刻かつ早退
              }else{
                $Shortage2[$i] =  MinusVtime($closing,$close_pm[$i]);
                $Shortage[$i] = AddVtime1($Shortage2[$i],$Shortage[$i]);
                $bikou[$i]="前・遅刻早退";
              }
            }
//午後半休の場合
          }elseif($holiday[$i]==5){
            $bikou[$i]="午後半休";
// 遅刻判定
//所定労働時間の半分
            $half_prescribed = "04:00";
//半休時の所定始業時間
            $base_time = MinusVtime($closing,$half_prescribed);
//入力された始業時間と半休時の所定始業時間を比較する
            if($open_pm[$i] > $opening){
              $Shortage[$i] = MinusVtime($open_pm[$i],$opening);
              $bikou[$i]="後・遅刻";
            }
//早退判定
//所定終業時間 > 実際の終業時間
            if($base_time > $close_pm[$i]){
//不足時間がない場合 = 遅刻でない場合
              if($Shortage[$i]=="00:00"){
                $Shortage[$i] = MinusVtime($base_time,$close_pm[$i]);
                $bikou[$i]="後・早退";
//不足時間がある = 遅刻かつ早退
              }else{
                $Shortage2[$i] =  MinusVtime($base_time,$close_pm[$i]);
                $Shortage[$i] = AddVtime1($Shortage2[$i],$Shortage[$i]);
                $bikou[$i]="後・遅刻早退";
              }
            }

          }
        }
      }
    }
//遅刻早退/半休判定---end

//残業時間の計算
//土日祝日の場合
      if($check[$i]=="OK"){
//日曜・祝日の残業時間(全て深夜残業時間)
        if($week_day5[$i]===$business_day[$i]){
          $overtime[$i]="00:00";
          $overtime_night[$i]=$total[$i];
//---日曜の残業時間end---
//土曜日の残業時間--start
        }elseif($week_day4[$i]===$business_day[$i]){
//(朝)深夜残業時間
          if($open_pm[$i] < "05:00"){
//深夜残業
            $mornig_overtime2[$i] =MinusVtime("05:00",$open_pm[$i]);
//普通残業
            $overtime[$i] =MinusVtime($total[$i],$mornig_overtime2[$i]);
          }
//終業時間が05:00~22:00までの場合
          if($close_pm[$i] >="05:00" && $close_pm[$i] <= "22:30"){
//始業時間が5時以降の場合終業時間＝普通残業時間とする
            if($open_pm[$i] >= "05:00" && $close_pm[$i] <= "22:30"){
//終業時間＝普通残業時間
              $overtime[$i]=$total[$i];
            }
          }
//22時以上、29時（翌朝5時）までの場合
          if($close_pm[$i] > "22:30" && $close_pm[$i]<="29:30"){
            if($open_pm[$i] < "22:30"){
//深夜残業時間の計算//22時以降が深夜残業のため終業時間から22時30分を引く
              $overtime_night[$i]= MinusVtime($close_pm[$i],"22:30");
              if($overtime_night[$i] > $total[$i]){
                $overtime_night[$i]=$total[$i];
              }
//普通残業時間の計算//実働時間から深夜残業時間を引くことで普通残業時間が求まる。
              $overtime[$i] = MinusVtime($total[$i],$overtime_night[$i]);
            }else{
//始業が深夜残業時間の場合は普通残業時間がない
//実働＝深夜残業時間になる？？
              $overtime_night[$i]= $total[$i];
            }
          }
//29時以上、max以下の場合
          if($close_pm[$i] > "29:30" && $close_pm[$i]<=$max_opening){
            if($open_pm[$i] <= "22:30"){
//深夜残業時間　22時30分から29時までの時間を求める
              $overtime_night[$i] = MinusVtime("29:30","22:30");
//普通残業時間の計算　実働時間から深夜残業時間を引くことで普通残業時間が求まる
              $overtime[$i] = MinusVtime($total[$i],$overtime_night[$i]);
            }elseif($open_pm[$i] > "22:30" && $open_pm[$i] < "29:30"){
              if($close_pm[$i] < "29:30"){
                $overtime_night[$i] = $total[$i];
              }else{
//休憩時間がある場合、元となる時間に休憩時間を足す。
                $base_rest[$i] = AddVtime1($rest[$i],"29:30");
                $overtime[$i] = MinusVtime($close_pm[$i],$base_rest[$i]);
                $overtime_night[$i] = MinusVtime($total[$i],$overtime[$i]);
              }
            }else{
              $overtime[$i]= $total[$i];
            }
          }
//朝普通残業時間があり、夜普通残業時間がある場合、残業時間を足し合わせる
          if(isset($mornig_overtime1[$i])){
            $overtime[$i]=AddVtime1($overtime[$i],$mornig_overtime1[$i]);
          }
//朝深夜残業時間があり、夜深夜残業時間がある場合、残業時間を足し合わせる
          if(isset($mornig_overtime2[$i])){
            $overtime_night[$i]=AddVtime1($overtime_night[$i],$mornig_overtime2[$i]);
          }
//---土曜の残業時間end---

//---以降平日の場合----
//普通残業時間の計算---start
        }else{
//朝残業---start
//所定始業時間より前に来て就業した場合も同様に残業処理
          if($deduction_time > $open_pm[$i]){
//普通残業時間
            if($open_pm[$i]>= "05:00" && $open_pm[$i]<= $deduction_time){
              $mornig_overtime1[$i] =MinusVtime($deduction_time,$open_pm[$i]);
            }
//深夜残業時間
            if($open_pm[$i] <= "05:00"){
              $mornig_overtime1[$i] = MinusVtime($deduction_time,"05:30");
              $mornig_overtime2[$i] =MinusVtime("05:00",$open_pm[$i]);
            }
          }
//朝残業---end
//夜残業---start
          $base_open = AddVtime1($opening,"09:00");
          if($close_pm[$i] >= $base_open){
            if($close_pm[$i] <= "22:00"){
              if($open_pm[$i] <= $base_open){
                $overtime[$i] =  MinusVtime($close_pm[$i],$base_open);
              }else{
                $overtime[$i] = $total[$i];
              }
            }
//普通残業時間の計算---end
//深夜残業時間の計算---start
//時間帯によっては普通残業時間も組み入れる
            if($close_pm[$i] >="22:30" && $close_pm[$i]<="24:00"){
              if($open_pm[$i] <= $base_open){
                $overtime_night[$i] =  MinusVtime($close_pm[$i],"22:30");
                $overtime[$i] =  MinusVtime("22:00",$base_open);
              }elseif($open_pm[$i]<="24:00"){
                if($open_pm[$i] > "22:30"){
                  $overtime_night[$i] =  MinusVtime($close_pm[$i],$open_pm[$i]);
                }else{
                  $overtime_night[$i] =  MinusVtime($close_pm[$i],"22:30");
                }
                $overtime[$i] =  MinusVtime($total[$i],$overtime_night[$i]);
              }else{
                $overtime_night[$i] =  MinusVtime("24:00", $open_pm[$i]);
              }
            }

            if($close_pm[$i]>"24:00" && $close_pm[$i]<="25:00"){
              if($open_pm[$i] <= $base_open){
                $overtime_night[$i] =  MinusVtime("24:00","22:30");
                $overtime[$i] =  MinusVtime("22:00",$base_open);
              }elseif($open_pm[$i]<="24:00"){
                if($open_pm[$i]<="22:30"){
                  $overtime_night[$i] =  MinusVtime($close_pm[$i],"23:30");
                  $overtime[$i] =  MinusVtime($total[$i],$overtime_night[$i]);
                }else{
                  $overtime_night[$i] = $total[$i];
                }
              }else{
                $overtime_night[$i] =  MinusVtime("25:00", $open_pm[$i]);
              }

            }
            if($close_pm[$i] >"25:00" && $close_pm[$i]<="29:00"){
              if($open_pm[$i] <= $base_open){
                $overtime_night[$i] =  MinusVtime($close_pm[$i],"23:30");
                $overtime[$i] = MinusVtime("22:00",$base_open);
              }elseif($open_pm[$i] > $base_open && $open_pm[$i] <= "22:00"){
                $overtime_night[$i] =  MinusVtime($close_pm[$i],"23:30");
                $overtime[$i] = MinusVtime("22:00",$open_pm[$i]);
              }elseif($open_pm[$i] > "22:00"){
                $overtime_night[$i] =  $total[$i];
              }
            }
            if($close_pm[$i]>="29:30" && $close_pm[$i]<=$max_opening){
              //始業時間が所定終業時間以下の場合
              if($open_pm[$i] <= $base_open){
                //22時までの普通残業時間(所定終業時間を引く)
                $overtime1[$i] = MinusVtime("22:00",$base_open);
                //29時30分からの普通残業時間
                $overtime2[$i] = MinusVtime($close_pm[$i],"29:30");
                //2つの普通残業時間を足し合わせたものが普通残業時間
                $overtime[$i] = AddVtime1($overtime1[$i],$overtime2[$i]);
                //深夜残業時間
                $overtime_night[$i] = "05:30";
                //始業時間が所定終業時間を超え、22時以下の場合
              }elseif($open_pm[$i] > $base_open && $open_pm[$i] <= "22:00"){
                //22時までの普通残業時間(始業時間を引く)
                $overtime1[$i] = MinusVtime("22:00",$open_pm[$i]);
                //29時30分からの普通残業時間
                $overtime2[$i] = MinusVtime($close_pm[$i],"29:30");
                //2つの普通残業時間を足し合わせたものが普通残業時間
                $overtime[$i] = AddVtime1($overtime1[$i],$overtime2[$i]);
                $overtime_night[$i] = "05:30";
                //始業時間が22時を超える場合
              }else{
              // elseif($open_pm[$i] > "22:00" && $open_pm[$i]  <= "24:00"){
                //普通残業時間の計算（終業時間から29：30を引いた値）
                $overtime[$i] = MinusVtime($close_pm[$i],"29:30");
                //深夜残業時間の計算（実働時間から普通残業時間を引いた値）
                $overtime_night[$i] = MinusVtime($total[$i],$overtime[$i]);
                //始業時間が
              }
            }
          }
//深夜残業時間の計算---end

//朝普通残業時間がある場合、残業時間を足し合わせる
          if(isset($mornig_overtime1[$i])){
            $overtime[$i]=AddVtime1($overtime[$i],$mornig_overtime1[$i]);
          }
//朝深夜残業時間があり、夜深夜残業時間がある場合、残業時間を足し合わせる
          if(isset($mornig_overtime2[$i])){
            $overtime_night[$i]=AddVtime1($overtime_night[$i],$mornig_overtime2[$i]);
          }
//休みフラグなしの終わり----end
        }
      }
    }
  }
  }
//休みフラグ有り---start
  if($yasumi[$i]==1){
    if($holiday[$i]==6){
      $naiyou[$i]=null;
    }
    $open[$i]= null;
    $open_ampm[$i]=null;
    $close[$i]= null;
    $close_ampm[$i]=null;
    $rest[$i]= null;
    $total[$i]= null;
    $overtime[$i]= null;
    $overtime_night[$i]= null;
    $Shortage[$i]= null;
    if($holiday[$i]==0){
      $check[$i]=null;
    }
  }
  if($check[$i]=="NG"){
    if($open_ampm[$i]==""){
      $open_ampm[$i]=null;
    }
    if($open[$i]==""){
      $open[$i]=null;
    }
    if($close_ampm[$i]==""){
      $close_ampm[$i]=null;
    }
    if($close[$i]==""){
      $close[$i]=null;
    }
    if($rest[$i]==""){
      $rest[$i]= null;
    }
    $total[$i]= null;
    $overtime[$i]= null;
    $overtime_night[$i]= null;
    $Shortage[$i]= null;
  }
}
//入力値・処理結果をセッションに代入する
$_SESSION['staff_number']=$_SESSION['result']['staff_number'];
$_SESSION['get_month']=$first_date;
$_SESSION['naiyou']=$naiyou;
$_SESSION['open_ampm']=$open_ampm;
$_SESSION['open']=$open;
$_SESSION['close_ampm']=$close_ampm;
$_SESSION['close']=$close;
$_SESSION['rest']=$rest;
$_SESSION['total']=$total;
$_SESSION['overtime']=$overtime;
$_SESSION['overtime_night']=$overtime_night;
$_SESSION['Shortage']=$Shortage;
$_SESSION['bikou']=$bikou;
$_SESSION['holiday']=$holiday;
$_SESSION['shift']=$shift;
$_SESSION['err_msg']=$err_msg;
$_SESSION['y_kyuka']=$y_kyuka;
$_SESSION['check']=$check;
header('Location:kinmu_insert.php');
exit;
?>