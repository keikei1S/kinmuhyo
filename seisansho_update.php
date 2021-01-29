<?php
ob_start();
include("kinmu_common.php");
ob_clean();

header('Location:seisansho_unset.php');
session_start();

//社員番号
$result = $_SESSION['result'];
$staff_number=$result['staff_number'];

// エラー表示を停止
error_reporting(8192);

$month = $_SESSION["select1"];
if(isset($month)){
        $s_year_and_month = $month.date("-01");
        $now_month = date('t', strtotime($s_year_and_month));
        $e_year_and_month = $month.date("-".$now_month);
    }
    //ローカル用

    // $dbh = db_connect();
    $dsn='mysql:dbname=pros-service_kinmu;host=mysql731.db.sakura.ne.jp;charset=utf8';
    $user='pros-service';
    $password='cl6cNJs2lt5W';
    $dbh = new PDO($dsn, $user, $password);

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql="SELECT * FROM TBL_CHECKOUT WHERE staff_number=:staff_number AND year_and_month BETWEEN :s_year_and_month AND :e_year_and_month";
    $stmt=$dbh->prepare($sql);
    $stmt->bindValue(":staff_number",$staff_number,PDO::PARAM_STR);
    $stmt->bindValue(":s_year_and_month",$s_year_and_month,PDO::PARAM_STR);
    $stmt->bindValue(":e_year_and_month",$e_year_and_month,PDO::PARAM_STR);
    $stmt->execute();
    $rec = $stmt -> fetchAll(PDO::FETCH_ASSOC);
//日付
var_dump(count($_SESSION['houmon']));
$X = count($_SESSION['houmon']);
for ($i = 0; $i <= $X ; $i++) {
    if(!empty($_SESSION['date'][$i])){
        $date[$i] = $_SESSION['date'][$i];
        $houmon[$i] = $_SESSION['houmon'][$i];
        $keiro[$i] = $_SESSION['keiro'][$i];
        $check[$i] = $_SESSION['check'][$i];
        $keiros[$i] = $_SESSION['keiros'][$i];
        $kingaku[$i] = $_SESSION['kingaku'][$i];
        $No[$i] = $_SESSION['no'][$i];
try
{  
    $dsn='mysql:dbname=pros-service_kinmu;host=mysql731.db.sakura.ne.jp;charset=utf8';
    $user='pros-service';
    $password='cl6cNJs2lt5W';
    $dbh = new PDO($dsn, $user, $password);

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if($rec[$i] == ""){
    print 'if';
    $sql='INSERT INTO TBL_CHECKOUT (
    s_No,
    staff_number,
    year_and_month,
    visit,
    Point_of_departure, 
    Checkout_flag,  
    Point_of_Arrival,
    Settlement_amount
    )
    VALUES(:s_No,:staff_number,:year_and_month,:visit,:Point_of_departure,:Checkout_flag,:Point_of_Arrival,:Settlement_amount)';
    $stmt=$dbh->prepare($sql);
    $params =array('s_No' => null,'staff_number' => $staff_number,'year_and_month' => $date[$i], 'visit' => $houmon[$i],'Point_of_departure' => $keiro[$i], 'Checkout_flag' => $check[$i],'Point_of_Arrival' => $keiros[$i],'Settlement_amount' => $kingaku[$i]);
    $stmt->execute($params);
    }
    else{
        print 'else';
        $sql = "UPDATE TBL_CHECKOUT SET year_and_month=:year_and_month,visit=:visit,Point_of_departure=:Point_of_departure,Checkout_flag=:Checkout_flag,Point_of_Arrival=:Point_of_Arrival,Settlement_amount=:Settlement_amount  WHERE s_No=:s_No";
        $stmt=$dbh->prepare($sql);
       $params =array('year_and_month' => $date[$i], 'visit' => $houmon[$i],'Point_of_departure' => $keiro[$i], 'Checkout_flag' => $check[$i],'Point_of_Arrival' => $keiros[$i],'Settlement_amount' => $kingaku[$i],'s_No' =>$rec[$i]["s_No"]);
    $stmt->execute($params);
    }
     $dbh=null;

    }catch (Exception $e){
        var_dump($e);
        print 'システムエラーが発生しました';
    exit();
        }
    }
}
?>