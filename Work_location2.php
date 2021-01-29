<?php
header('Location: session_Workinsert.php');
session_start();
require('kinmu_common.php');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title></title>
</head>
<body>
<?php
error_reporting(8192);
try
{
//$kinmuchi=$_SESSION['kinmuchi'];
$kinmuchiid=$_SESSION['kinmuchiid'];
$strat=$_SESSION['strat'];
$end=$_SESSION['end'];

//$kinmuchi=htmlspecialchars($kinmuchi,ENT_QUOTES,'UTF-8');
$kinmuchiid=htmlspecialchars($kinmuchiid,ENT_QUOTES,'UTF-8');
$strat=htmlspecialchars($strat,ENT_QUOTES,'UTF-8');
$end=htmlspecialchars($end,ENT_QUOTES,'UTF-8');

$dbh = db_connect();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//$sql='INSERT INTO tbl_belongss (work_id,work_name,opening_hours,closing_hours)VALUES (?,?,?,?)';
$sql='INSERT INTO TBL_BELONGSS (work_name,opening_hours,closing_hours)VALUES (?,?,?)';
$stmt = $dbh->prepare($sql);
//$data[] = $kinmuchi;
$data[] = $kinmuchiid;
$data[] = $strat;
$data[] = $end;

$stmt->execute($data);

$dbh = null;



print '勤務地情報を追加しました。<br/>';



} catch (Exception $e)

{
    print 'ただいま障害が発生しております';
    exit();
}

?>


</form>
</body>
</html>
