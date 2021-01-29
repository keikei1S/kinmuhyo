<?php
if(!isset($_SESSION)){
  session_start();
}
include("kinmu_common.php");
//祝日テーブルに祝日を追加するクラス
//1年後まで追加できるため、反映時期の検討要
/**
 * HTTP でデータを取得します。
 */
function httpGet($url)
{
    $option = [
        CURLOPT_RETURNTRANSFER => true, // 文字列として返す
        CURLOPT_TIMEOUT => 10, // タイムアウト時間 (秒)
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, $option);

    $data = curl_exec($ch);
    $info = curl_getinfo($ch);
    $errorNo = curl_errno($ch);

    // OK 以外はエラーなので空白配列を返す
    if ($errorNo !== CURLE_OK) {
        // CURLE_OPERATION_TIMEDOUT: タイムアウト
        return [];
    }

    if ($info['http_code'] !== 200) {
        return false;
    }

    return $data;
}

/**
 * 祝祭日データを配列で取得します。
 */
function loadHolidays() {
  // 祝祭日データ URL
  //正規URL
  $url = 'https://www8.cao.go.jp/chosei/shukujitsu/syukujitsu.csv';
  // HTTP GET で取得
  $data = httpGet($url);
  if (!$data) {
    header('Location: err_report.php');
    exit();
  }
  // CSV が SJIS なので文字コードを変換しておく
  $data = mb_convert_encoding($data, 'UTF-8', 'SJIS');
  // 行ごとに分割
  $lines = explode("\n", $data);
  $holidays = [];
  foreach ($lines as $line) {
      // カンマで分割
      $cols = explode(",", $line);
      if($cols[0]!="国民の祝日・休日月日"){
	      if($cols[0] > date("Y")){
	      	$holidays[] = trim($cols[0]);
	      }
	   }
  }
  // 現在から1年間分の年末年始を追加
  $currentYear = intval(date('Y'));
  for ($i = 0; $i < 3; $i++) { // 2年間
      $y = $currentYear + $i;
      $date = strtotime("$y/12/30"); // 12月30日から
      for ($j = 0; $j < 5; $j++) { // 1月3日まで3日間
          $dateStr = date('Y/m/d', $date);
          $holidays[] = $dateStr;
          $date = strtotime("+1 day", $date);
      }
  }
  return $holidays;
}
$holiday=loadHolidays();

foreach ($holiday as $key => $value) {
	$h_day[$key]=$value;
	$now = new DateTime($h_day[$key]);
	$day[$key]= $now->format('Y/m/d');
}

for($i=0; $i < count($day); $i++){
	try{
		  $dbh = db_connect();
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql="INSERT INTO `TBL_HOLIDAY`(`day`) VALUES (:day)on duplicate key update day=:day";
		$stmt=$dbh->prepare($sql);
	 	$params =array('day' => $day[$i]);
		$stmt->execute($params);
		$dbh=null;
	}
	catch(Exception $e){
    header('Location: err_report.php');
  exit();
	}
}
	$_SESSION["print_err"] = "祝日テーブルを更新しました。";
  header('Location:list_of_members.php');
  exit;
?>
