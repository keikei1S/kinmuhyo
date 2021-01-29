<?php
header('Location:sesion_Workupdate.php');
session_start();
require('kinmu_common.php');
try
{
    $kinmuchi = $_SESSION['kinmuchi'];
    $kinmuchiid = $_SESSION['kinmuchiid'];
    $strat = $_SESSION['strat'];
    $end = $_SESSION['end'];

    $kinmuchi = htmlspecialchars($kinmuchi,ENT_QUOTES,'UTF-8');
    $kinmuchiid = htmlspecialchars($kinmuchiid,ENT_QUOTES,'UTF-8');
    $strat = htmlspecialchars($strat,ENT_QUOTES,'UTF-8');
    $end = htmlspecialchars($end,ENT_QUOTES,'UTF-8');

    $dbh = db_connect();
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // $sql = "UPDATE tbl_belongss SET work_name=?,opening_hours=?,closing_hours=? WHERE work_id=?";
    $sql = "UPDATE TBL_BELONGSS SET work_name=?,opening_hours=?,closing_hours=? WHERE work_id=?";
    $stmt = $dbh->prepare($sql);
    $data[] = $kinmuchiid;
    $data[] = $strat;
    $data[] = $end;
    $data[] = $kinmuchi;
    $stmt->execute($data);
$dbh = null;

print '勤務地情報を更新しました。<br/>';

}
catch (Exception $e){
    print 'ただいま障害が発生しております';
    exit();
}
?>
