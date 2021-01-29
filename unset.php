<?php
session_start();
if(isset($_SESSION['hyouji'])){
    header('Location: seisansho.php');
    unset($_SESSION['houmon']);
}
?>