<?php
//セッションスタート
session_start();

ob_start();
include("kinmu_common.php");
ob_clean();

//精算書画面に遷移
header('Location:seisansho_delete_done.php');

//社員番号
$result = $_SESSION['result'];
$staff_number=$result['staff_number'];

//POSTで受け取ったデータを変数に代入
$sentaku = $_SESSION['sentaku'];
$count = count($sentaku);

$month = $_SESSION["select1"];
if(isset($month)){
    $s_year_and_month = $month.date("-01");
    $now_month = date('t', strtotime($s_year_and_month));
    $e_year_and_month = $month.date("-".$now_month);
}
try
{
$dbh = db_connect();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//ローカル用
// $sql="SELECT * FROM tbl_checkout WHERE staff_number=:staff_number AND year_and_month BETWEEN :s_year_and_month AND :e_year_and_month";
//サーバー用
$sql="SELECT * FROM TBL_CHECKOUT WHERE staff_number=:staff_number AND year_and_month BETWEEN :s_year_and_month AND :e_year_and_month";
$stmt=$dbh->prepare($sql);
$stmt->bindValue(":staff_number",$staff_number,PDO::PARAM_STR);
$stmt->bindValue(":s_year_and_month",$s_year_and_month,PDO::PARAM_STR);
$stmt->bindValue(":e_year_and_month",$e_year_and_month,PDO::PARAM_STR);
$stmt->execute();
$rec = $stmt -> fetchall(PDO::FETCH_ASSOC);

}catch (Exception $e){
    // var_dump($e);
        print 'システムエラーが発生しました';
        exit();
    }
try
{     
$dbh = db_connect();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
for ($i = 0; $i < $count ; $i++) {  
//サーバー用
//delete処理
$sql = "DELETE FROM TBL_CHECKOUT WHERE s_No=:s_No";
$stmt=$dbh->prepare($sql);
$stmt->bindValue(':s_No',$rec[$sentaku[$i]]['s_No'], PDO::PARAM_INT);
$stmt->execute();
}
    

    $dbh = null;
}catch (Exception $e){
    // var_dump($e);
        print 'システムエラーが発生しました';
        exit();
    }

?>