<?php
//社員情報の変更
//セッションが開始されていなければセッションを開始する。
if(!isset($_SESSION)){
  session_start();
  session_regenerate_id(true);
}
if (isset($_SESSION["login"])==false)
{
  header("Location: staff_login.php");
  exit();
}
require_once("kinmu_common.php");

//変数に格納
if(isset($_SESSION["post"])){
  $staff = $_SESSION["post"]['staff_number'];
  $familyname = $_SESSION["post"]['familyname'];
  $firstname = $_SESSION["post"]['firstname'];
  $familyname_kana = $_SESSION["post"]['familyname_kana'];
  $firstname_kana = $_SESSION["post"]['firstname_kana'];
  $email = $_SESSION["post"]['email'];
  $nyuusha = $_SESSION["post"]['hire_date'];
  $taisha = $_SESSION["post"]['retirement_date'];
  $admin_flag = $_SESSION["post"]['admin_flag'];
  $new_work_id = $_SESSION["post"]['new_work_id'];
  $new_start_month = $_SESSION["post"]['new_start_month'];
  $old_work_id = $_SESSION["post"]['old_work_id'];
  $old_start_month = $_SESSION["post"]['old_start_month'];
  $old_end_month = $_SESSION["post"]['old_end_month'];
  $paid_grant = $_SESSION["post"]['paid_grant'];
}else{
  header('Location: err_report.php');
  exit();
}
unset($_SESSION["post"]);

//変数のデータ処理
$staff=htmlspecialchars($staff,ENT_QUOTES,'UTF-8');
$firstname=htmlspecialchars($firstname,ENT_QUOTES,'UTF-8');
$familyname=htmlspecialchars($familyname,ENT_QUOTES,'UTF-8');
$familyname_kana=htmlspecialchars($familyname_kana,ENT_QUOTES,'UTF-8');
$firstname_kana=htmlspecialchars($firstname_kana,ENT_QUOTES,'UTF-8');
$email=htmlspecialchars($email,ENT_QUOTES,'UTF-8');
$nyuusha=htmlspecialchars($nyuusha,ENT_QUOTES,'UTF-8');
$taisha=htmlspecialchars($taisha,ENT_QUOTES,'UTF-8');
$admin_flag=htmlspecialchars($admin_flag,ENT_QUOTES,'UTF-8');
$new_work_id=htmlspecialchars($new_work_id,ENT_QUOTES,'UTF-8');
$new_start_month=htmlspecialchars($new_start_month,ENT_QUOTES,'UTF-8');
$old_work_id=htmlspecialchars($old_work_id,ENT_QUOTES,'UTF-8');
$old_start_month=htmlspecialchars($old_start_month,ENT_QUOTES,'UTF-8');
$old_end_month=htmlspecialchars($old_end_month,ENT_QUOTES,'UTF-8');
$paid_grant=htmlspecialchars($paid_grant,ENT_QUOTES,'UTF-8');

//DB接続
try
{
  $dbh = db_connect();
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  if($old_work_id!=""){
    $sql = "UPDATE TBL_STAFF SET familyname=?,familyname_kana=?,firstname=?,firstname_kana=?,email=?,hire_date=?,retirement_date=?,holiday_with_pay=?,admin_flag=?,new_work_id=?,new_start_month=?,old_work_id=?,old_start_month=?,old_end_month=? WHERE staff_number=?";
    $stmt = $dbh->prepare($sql);
    $data[] = $familyname;
    $data[] = $familyname_kana;
    $data[] = $firstname;
    $data[] = $firstname_kana;
    $data[] = $email;
    $data[] = $nyuusha;
    $data[] = $taisha;
    $data[] = $paid_grant;
    $data[] = $admin_flag;
    $data[] = $new_work_id;
    $data[] = $new_start_month;
    $data[] = $old_work_id;
    $data[] = $old_start_month;
    $data[] = $old_end_month;
    $data[] = $staff;
  }else{
    $sql = "UPDATE TBL_STAFF SET familyname=?,familyname_kana=?,firstname=?,firstname_kana=?,email=?,hire_date=?,retirement_date=?,holiday_with_pay=?,admin_flag=?,new_work_id=?,new_start_month=? WHERE staff_number=?";
    $stmt = $dbh->prepare($sql);
    $data[] = $familyname;
    $data[] = $familyname_kana;
    $data[] = $firstname;
    $data[] = $firstname_kana;
    $data[] = $email;
    $data[] = $nyuusha;
    $data[] = $taisha;
    $data[] = $paid_grant;
    $data[] = $admin_flag;
    $data[] = $new_work_id;
    $data[] = $new_start_month;
    $data[] = $staff;
  }
  $stmt->execute($data);
  $dbh = null;
} catch (Exception $e)
{
  header('Location: err_report.php');
  exit();
}
?>
<form method="post" action="unset_session.php">
  <div class="entry">
    <h2>社員情報を更新しました。</h2>
    <input type="submit" name="back" value="社員情報一覧画面へ">
  </div>
</form>
<style>

.entry{
  width: 100%;
  margin-top: 50px;
  display: block;
  text-align: center;
}
input{
  margin: 30px;
  background : #87cefa;
  width: 150px;
  height: 40px;
}
</style>
