<?php
header('Location:seisansho_done.php');
//セッションの開始
session_start();
// エラー表示を停止
error_reporting(8192);

//変数をリセット
unset ($_SESSION['date1_1']);
unset ($_SESSION['houmon1_1']);
unset ($_SESSION['keiro1_1']);
unset ($_SESSION['keiros1_1']);
unset ($_SESSION['kingaku1_1']);
unset ($_SESSION['check1_1']);
unset ($_SESSION['errmsg1']);
unset ($_SESSION['errmsg2']);
unset ($_SESSION['errmsg3']);
unset ($_SESSION['errmsg4']);
unset ($_SESSION['errmsg5']);
unset ($_SESSION['errmsg6']);
?>