<?php
    session_start();
    
    //変数をリセット
    unset ($_SESSION['staff_number']);
    unset ($_SESSION['familyname']);
    unset ($_SESSION['firstname']);
    unset ($_SESSION['familyname_kana']);
    unset ($_SESSION['firstname_kana']);
    unset ($_SESSION['email']);
    unset ($_SESSION['nyuusha']);
    unset ($_SESSION['taisha']);
    unset ($_SESSION['yuukyuu']);
    unset ($_SESSION['admin_flag']);
    unset ($_SESSION["passwords"]);
    unset ($_SESSION['new_work_id']);
    unset ($_SESSION['new_start_month']);
    unset ($_SESSION['old_work_id']);
    unset ($_SESSION['old_start_month']);
    unset ($_SESSION['old_end_month']);
    unset ($_SESSION['newRegister']);
    unset ($_SESSION['hensyuu']);
 
    header('Location: list_of_members.php');

?>