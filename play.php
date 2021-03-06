<?php
/**
 * Created by PhpStorm.
 * User: huqi1
 * Date: 2018/1/15
 * Time: 22:49
 */
//信息收集部分
date_default_timezone_set('Asia/Shanghai');
session_start();
require_once "function.php";
require_once "stepInfo.php";
$getkey = isset($_GET['key']) ? $_GET['key'] : "";//密钥保存
$getkey= preg_replace('/[^0-9a-zA-Z]+/','',$getkey);//去除危险字符
if (isset($_SESSION['IDnum']) && !empty(trim($_SESSION['IDnum']))) {//session信息存在
    $IDnums = (int)$_SESSION['IDnum'];
} else {
    header("Location: id.php?key=" . $getkey);
    exit;
}
require_once "config.php";
$config = $GLOBALS['config'];
$mysql = new mysqli($config["SQL_URL"], $config["SQL_User"], $config["SQL_Password"], $config["SQL_Database"], $config["SQL_Port"]);
if (mysqli_connect_errno()) {
    echo 'Database Connect is error - ' . mysqli_connect_error();
    exit();
}
$mysql->set_charset("utf8");

$sq = "SELECT * FROM config LIMIT 1";
$result = $mysql->query($sq);
$row = $result->fetch_array();

$mainTitle = $row['title'];
$user = $row['user'];
$gameName = $row['gamename'];
$startTime=$row['startTime'];
$endTime=$row['endTime'];
$mysql->close();
if (strtotime(date("y-m-d H:i:s"))<=strtotime($startTime)){
    // is not touch the time
    header("Location: index.php");
    exit;
}elseif(strtotime(date("y-m-d H:i:s"))>=strtotime($endTime)){
    // game is over
    header("http/1.1 403 forbidden");
    noCertification("游戏已结束",
        "<div class=\"helptextol\"><p>游戏已结束，让我们期待即将到来的读书节吧~</p></div>");
    exit;
}


$step = new stepInfo($IDnums);

if ($step->getClub()==-1){
    //拒绝参加
    noCertification("你不能参加这次游戏",
        "<ol class=\"helptextol\"><li>你没有报名参加此次游戏</li><li>你输错了学号</li></ol><a class=\"btn btn-success\" href='id.php?key=clean'>这里更改学号</a>");
}

if (empty(trim($getkey)) || $getkey == "reset") {
    //空的key，展示界面
    if ($step->getOver()) {
        finishGame($step->getIDnum());
        exit;
    } else {
        //未结束
        echo gamePage($mainTitle, $gameName, $step->getGameStep(), $step->getAllStep(), $step->getIDnum(), $step->getshowInfo(), $user, $step->getRestNumber());
        exit;
    }

} else {
    $res = $step->getNextInfo($getkey);
    switch ($res) {
        case 0://成功
            if ($step->getOver()) {
                finishGame($step->getIDnum());
            } else
                echo gamePage($mainTitle, $gameName, $step->getGameStep(), $step->getAllStep(), $step->getIDnum(), $step->getshowInfo(), $user, $step->getRestNumber());
            break;
        case 1://其他步骤出错
        default:
            noCertification("上条线索提示的不是这件物品哦",
                "<ol class=\"helptextol\"><li>你已经完成了此步骤</li><li>上一个步骤提示信息指向的不是此物品</li><li>你还没有进行到此步骤</li></ol>");
            break;
        case 2://晋级出错，失效
            noCertification("此二维码已失效",
                "<ol class=\"helptextol\"><li>此二维码已被他人扫过</li><li>二维码不是书签上印制的</li></ol>");
            break;
        case 3://晋级出错，无二维码
            noCertification("二维码错误",
                "<ol class=\"helptextol\"><li>二维码不是印制在书签上的</li><li>可能有污物遮盖二维码引发错误</li></ol>");
            break;
    }
    exit;

}

?>