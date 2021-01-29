<?php
//セッションスタート
session_start();
require('kinmu_common.php');
//セッションを終了
//$_SESSION = array();
try {
    // //////////////////データベースの読込 S//////////////////////
    $dbh = db_connect();
  	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // tbl_belongssの値を全て取得
    // $belongsssql = 'select * FROM  tbl_belongss';
    $belongsssql = 'select * FROM  TBL_BELONGSS';
	$belongssstmt = $dbh->prepare($belongsssql);
	$belongssstmt->execute();

    // tbl_belongssの値を全て取得
    // $belongsssql2 = 'select * FROM  tbl_belongss';
    $belongsssql2 = 'select * FROM  TBL_BELONGSS';
	$belongssstmt2 = $dbh->prepare($belongsssql2);
    $belongssstmt2->execute();

if(isset($_SESSION["kinmuchi"])){
  $asssql="SELECT * FROM TBL_BELONGSS WHERE work_id=?";
  $stmt=$dbh->prepare($asssql);
  $data[]=$_SESSION['kinmuchi'];
  $stmt->execute($data);
  $rec = $stmt -> fetch(PDO::FETCH_ASSOC);
  if(isset($_POST['tuika'])){
    if($rec["work_id"]==$_SESSION["kinmuchi"]){
      $errflag = 16;
    }
  }
}
    $dbh = null;
    // ////////////////データベースの読込 E//////////////////////
}catch (Exception $e) {
        print 'ただいま障害が発生しております';
        exit();
    }
//     if(empty($_POST['kousin'])==false){
// $_SESSION['kousin'] = $_POST['kousin'];
//     }
    if(isset($_POST['kousin'])){
$_SESSION['kousin'] = $_POST['kousin'];
    }
    if(isset($_POST['tuika'])){
    $_SESSION['tuika'] = $_POST['tuika'];
    }

//$_SESSION['kinmuchi'] = $_POST['kinmuchi'];
$_SESSION['kinmuchiid'] = $_POST['kinmuchiid'];
$_SESSION['strat'] = $_POST['strat'];
$_SESSION['end'] = $_POST['end'];
error_reporting(0);
//勤務IDエラーチェック
$belongssrec = $belongssstmt->fetchall(PDO::FETCH_ASSOC);

// foreach($belongssrec as $belongssre){
//     if(empty($_POST['tuika'])==false){
//     if($_SESSION['kinmuchi'] == $belongssre['work_id']){
//         $errflag = 16;
//         }
//     }
// }




// if (empty($_POST['kinmuchi'])){
//     $errflag = 1;
// }elseif (is_numeric($_POST['kinmuchi']) == false) {
//     $errflag = 9;
// }

//勤務地名エラーチェック
if(empty($_POST['kinmuchiid'])){
    $errflag = 2;
}

//始業時間エラーチェック
if (empty($_POST['strat'])){
    $errflag = 3;
}elseif($_POST['strat'] == $_SESSION['end']){
    $errflag = 4;
}elseif(  $_POST['strat'] > $_SESSION['end']){
    $errflag = 5;
}

//終業時間エラーチェック
if (empty($_POST['end'])){
    $errflag = 6;
}elseif($_POST['strat'] == $_SESSION['end']){
    $errflag = 7;
}elseif( $_POST['end'] < $_SESSION['strat'] ){
    $errflag = 8;
}

if($errflag >= 1){
  if(isset($_POST['kousin']) && $_SESSION["kinmuchi"]==""){
    $_SESSION["up_err"]="※勤務地IDが設定されていないため、更新はできません。";
  }
  header('Location: Work_location.php');
}elseif(isset($_POST['kousin'])){
  if($_SESSION["kinmuchi"]!=""){
    header('Location:Workupdate.php');
  }else{
    $_SESSION["up_err"]="※勤務地IDが設定されていないため、更新はできません。";
     header('Location: Work_location.php');
  }
}
else{
    header('Location: Work_location2.php');
}

?>
