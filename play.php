<?php
/**
 * Created by PhpStorm.
 * User: huqi1
 * Date: 2018/1/15
 * Time: 22:49
 */
//信息收集部分
session_start();
$getkey = isset($_GET['key'])?$_GET['key']:"";//密钥保存
if (isset($_SESSION['IDnum']) && !empty(trim($_SESSION['IDnum']))) {//session信息存在
    $IDnums = $_SESSION['IDnum'];
} else {
    header("Location: id.php?key=" . $getkey);
    exit;
}


?>