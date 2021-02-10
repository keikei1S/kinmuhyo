<?php
session_start();
require('kinmu_common.php');
try
{
    $kinmuchi = $_SESSION['kinmuchi'];
    $kinmuchiid = $_SESSION['kinmuchiid'];
    $strat = $_SESSION['strat'];
    $end = $_SESSION['end'];
    $modify = $_SESSION["result"]["staff_number"];

    $kinmuchi = htmlspecialchars($kinmuchi,ENT_QUOTES,'UTF-8');
    $kinmuchiid = htmlspecialchars($kinmuchiid,ENT_QUOTES,'UTF-8');
    $strat = htmlspecialchars($strat,ENT_QUOTES,'UTF-8');
    $end = htmlspecialchars($end,ENT_QUOTES,'UTF-8');

    $dbh = db_connect();
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // $sql = "UPDATE tbl_belongss SET work_name=?,opening_hours=?,closing_hours=? WHERE work_id=?";
    $sql = "UPDATE TBL_BELONGSS SET work_name=?,opening_hours=?,closing_hours=?,last_modified=?,update_date=? WHERE work_id=?";
    $stmt = $dbh->prepare($sql);
    $data[] = $kinmuchiid;
    $data[] = $strat;
    $data[] = $end;
    $data[] = $modify;
    $data[] = date("Y-m-d H:i:s");
    $data[] = $kinmuchi;
    $stmt->execute($data);
$dbh = null;

header('Location: Workupdate2.php');
exit();
}
catch (Exception $e){
    print 'ただいま障害が発生しております';
    exit();
}
?>
