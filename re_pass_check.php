<?php
//error_reporting(0);
session_start();
require_once "Mail.php";
require_once "kinmu_common.php";
$staff_number=$_POST['staff_number'];
$email = $_POST['email'];
$staff_number=htmlspecialchars($staff_number,ENT_QUOTES,'UTF-8');
$email=htmlspecialchars($email,ENT_QUOTES,'UTF-8');
$reg_str = "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/";
$err = [];
//空欄チェック
if(empty($staff_number)){
  $err['staff'] ='入力必須項目です。';
}
//半角数字チェック
elseif(!preg_match("/^[0-9]+$/", $staff_number)){
  $err['staff'] ='半角数字で入力してください。';
}
//桁数チェック（4桁以外エラー）
elseif(preg_match('/^([0-9]{4})$/', $staff_number) == false ){
  $err['staff'] ='4桁で入力してください。';
}


if($email=="")
{
  $err['email'] ='入力必須項目です。';
}
elseif( 30 < mb_strlen($email))
{
  $err['email'] ='30桁以内で入力してください。';
}
elseif(!preg_match($reg_str, $email))
{
  $err['email'] ='使用できない文字が含まれています。';
}
//エラーがあればパスワード再登録画面に遷移させる
if(!empty($err)) {
  $_SESSION["err"] = $err;
  $_SESSION['staff_number'] =$staff_number;
  $_SESSION['email'] =$email;
  header('Location:re_pass.php');
  exit();
}else{
  $rec= kinmu_common::staff_table($staff_number);
  if($rec==false)
  {
    $err['staff'] = '入力された社員番号は存在しません。<br/>';
  }
  if($rec['email']!=$email)
  {
    if(!isset($err['staff'])){
      $err['email'] = 'メールアドレスが違います。ご確認ください。<br/>';
    }
  }
}
if(!empty($err)){
  $_SESSION["err"] = $err;
  $_SESSION['staff_number'] =$staff_number;
  $_SESSION['email'] =$email;
  header('Location:re_pass.php');
  exit();
}else{
  $encString = openssl_encrypt($staff_number, 'AES-256-CBC', '社員番号');
if(strpos($encString,'+') !== false){
  $encString = openssl_encrypt($staff_number, 'AES-128-CBC', '社員番号');
}

//URLの時間を制限する。
date_default_timezone_set('Asia/Tokyo');
$domain = "pros-service.co.jp"; //ドメイン名
//$domain = "localhost:8080/";
$key = "YOURSECRETKEY"; //SecretKey
$path = "/kinmu/pass_change.php"; //配信ファイルのフルパス
//$path = "/kinmuhyo/pass_change.php";
$token_lifetime = 3600;  //有効期限を指定する。（秒単位）

    $expiration = time() + $token_lifetime;
    $string_to_sign = $path . $expiration;
    $signature = hash_hmac("sha256", $string_to_sign, $key);
    $token = $expiration . "_" . $signature;
    $URL= "http://" . $domain . $path . "?p=$encString" ."&t=" . $token;
    $limit=date('Y-m-d H:i:s',$expiration)."まで有効";

    // mb_language("Japanese");
    // mb_internal_encoding("UTF-8");
    $from = 'prossystem.test@gmail.com';
    $host = "ssl://smtp.gmail.com";
    $port = "465";
    $username = "prossystem.test@gmail.com";
    $password = 'prostest';

    $subject = 'ログインパスワード再設定の確認';
    $body = $rec["familyname"].$rec["firstname"]."さん

    お疲れ様です。

    勤務表ログインパスワード再設定方法についてお知らせいたします。

    ──────────────────────────────
    ◆ パスワードの再設定について
    ──────────────────────────────

    下記のアドレスにアクセスしますと、パスワードの再設定をおこなえます。

    $URL

    ※$limit
    パスワード再発行の手続きにお心当たりの無い場合は
    このメールを破棄していただきますようお願い申し上げます。

    ※本メールは送信専用のため、ご返信いただけません。";

    $headers = array ('From' => $from, 'To' => $email,'Subject' => $subject);
    $smtp = Mail::factory('smtp',
    array ('host' => $host,
    'port' => $port,
    'auth' => true,
    'username' => $username,
    'password' => $password));

    $mail = $smtp->send($email, $headers, $body);
    if (PEAR::isError($mail)) {
      echo($mail->getMessage());
    } else {
      header('Location:pass_change_done.php');
      exit;
    }
  }
?>
