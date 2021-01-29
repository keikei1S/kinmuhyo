<?php
session_start();



?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title></title>
</head>
<body>
<?php

try
{  
$staff = $_SESSION['staffcode'];
$username = $_POST['username'];
$username2 = $_POST['username2'];
$username3 = $_POST['username3'];
$username4 = $_POST['username4'];
$email = $_POST['email'];
$nyuusha = $_POST['nyuusha'];
$taisha = $_POST['taisha'];
$yuukyuu = $_POST['yuukyuu'];
$admin_flag = $_POST['check'];


$staff=htmlspecialchars($staff,ENT_QUOTES,'UTF-8');
$username=htmlspecialchars($username,ENT_QUOTES,'UTF-8');
$username2=htmlspecialchars($username2,ENT_QUOTES,'UTF-8');
$username3=htmlspecialchars($username3,ENT_QUOTES,'UTF-8');
$username4=htmlspecialchars($username4,ENT_QUOTES,'UTF-8');
$email=htmlspecialchars($email,ENT_QUOTES,'UTF-8');
$nyuusha=htmlspecialchars($nyuusha,ENT_QUOTES,'UTF-8');
$taisha=htmlspecialchars($taisha,ENT_QUOTES,'UTF-8');
$yuukyuu=htmlspecialchars($yuukyuu,ENT_QUOTES,'UTF-8');
$admin_flag=htmlspecialchars($admin_flag,ENT_QUOTES,'UTF-8');

$dsn='mysql:dbname=勤務表;host=localhost;charset=utf8';
$user='root';
$password='';
$dbh=new PDO($dsn,$user,$password);
$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

$sql = "UPDATE tbl_staff SET familyname=?,familyname_kana=?,firstname=?,firstname_kana=?,email=?,hire_date=?,retirement_date=?,holiday_with_pay=?,admin_flag=? WHERE staff_number=?";
$stmt = $dbh->prepare($sql);
$data[] = $username;
$data[] = $username2;
$data[] = $username3;
$data[] = $username4;
$data[] = $email;
$data[] = $nyuusha;
$data[] = $taisha;
$data[] = $admin_flag;
$data[] = $yuukyuu;
$data[] = $staff;

$stmt->execute($data);

$dbh = null;

print '社員情報を更新しました。<br/>';

} catch (Exception $e)
{
    print 'ただいま障害が発生しております';
    exit();
}
header('Location:DBtest.php');
?>
<form action="DBtest.php">
<input type="submit" value="戻る">
</form>
</body>
</html>