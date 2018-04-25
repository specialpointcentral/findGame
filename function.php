<?php
/**
 * Created by PhpStorm.
 * User: huqi1
 * Date: 2018/1/17
 * Time: 21:31
 * function collection
 */
require_once "config.php";
/**
 * 扫码错误统一页面格式
 * @param $reasonTitle <pre>the reason title why give error page</pre>
 * @param $reasonTxt <pre>the reason why give error page (HTML)</pre>
 */
function noCertification($reasonTitle, $reasonTxt)
{
    $config = $GLOBALS['config'];
    $mysql = new mysqli($config["SQL_URL"], $config["SQL_User"], $config["SQL_Password"], $config["SQL_Database"], $config["SQL_Port"]);
    if (mysqli_connect_errno()) {
        echo 'Database Connect is error - ' . mysqli_connect_error();
        exit();
    }
    $mysql->set_charset("utf8");

    $sq = "SELECT * FROM config";
    $result = $mysql->query($sq);
    $row = $result->fetch_array();

    $mainTitle = $row['title'];
    $user = $row['user'];
    $mysql->close();
    echo <<<EOF
    <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8" />
                <title>Oops~-{$mainTitle}</title>
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="shortcut icon" href="/favicon.ico" >
                <link rel="stylesheet" href="css/bootstrap.min.css" />
                <link rel="stylesheet" href="css/footer.css" />
                <!--[if lt IE 9]>
                   <script src="//cdn.bootcss.com/html5shiv/3.7.3/html5shiv.min.js"></script>
                   <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
                <![endif]-->
                <style type="text/css">
                    .helptextol{ padding-left:1em;line-height: 1.8em; font-size: 16px; color: black ;}
                    helptext{ line-height: 1.8em; font-size: 18px; color: black ;}
                    hr {border:solid;border-width: 2px;border-style:solid;color:gray;}
                    h1 {font-size: 80px; font-weight: normal; margin-bottom: 12px;}
                    p {line-height: 1.8em; font-size: 26px}
                </style>
            </head>
            <body>
                <div class="container"> 
                    <h1>:(</h1>
                    <p>{$reasonTitle}</p>
                    <hr />
                    <helptext>发生此错误的可能性有：</helptext>
                    {$reasonTxt}
                    <hr />
                    <p>加油吧，胜利就在前方~</p>
                    <button onclick="location='id.php?key=reset'" type="button" class="btn btn-success btn-block btn-lg">转到最近的任务</button>
                </div>
                <div class="footers">
                    <a href="http://www.miitbeian.gov.cn/" rel="external nofollow" target="_blank">皖ICP备17002097号</a>
                    <br/>
                    <a>&copy;SPC | HITwh CST</a>
                    <br/>
                    <a>{$user}</a>
                </div>
            </body>
    </html>
EOF;
    exit;
}

/**
 * 活动页面统一格式
 * @param $mainTitle 网页标题
 * @param $gameName 活动名称
 * @param $runLevel 正在进行的步骤，最后一步完成为总步骤加一
 * @param $allLevel 总步骤
 * @param $IDnums ID号
 * @param $infoText 主体内容
 * @param $user 举办者
 * @param $restNumber 剩余晋级数
 * @param $infotitle 提示标题
 * @return string 页面
 */
function gamePage($mainTitle, $gameName, int $runLevel, int $allLevel, $IDnums, $infoText, $user, $restNumber, $infotitle = "")
{
    $finishBar = ($runLevel == $allLevel + 1) ? "progress-bar-success" : "progress-bar-info";//进度条颜色
    $finishIco = ($runLevel == $allLevel + 1) ? "glyphicon-ok" : "glyphicon-map-marker";//图形样式
    if ($allLevel != 0) {
        $process = (int)($runLevel / $allLevel * 100);//进度条长短
        if ($process > 100) $process = 100;
    } else $process = 0;
    $processNote = ($runLevel == $allLevel + 1) ? "完成任务" : " Step " . $runLevel;//进度条说明文字
    $infoTitle = ($runLevel == $allLevel + 1) ? $infotitle : "你现在位于第" . $runLevel . "步，共" . $allLevel . "步，加油~";//进度提示说明文字
    if ($runLevel == $allLevel) {//晋级名额剩余提示
        if ($restNumber < 0)
            $haveFinish = "";
        else $haveFinish = <<<EOF
        <br />
        <span class="glyphicon glyphicon-tags" aria-hidden="true"></span>
		<span class="sr-only">cardreset:</span>
		晋级名额剩余：{$restNumber}
EOF;
    } else $haveFinish = "";
    return <<<EOF
    <!DOCTYPE html>
    <html lang="zh-CN">
        <head>
            <meta charset="utf-8" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>{$mainTitle}</title>
            <link rel="shortcut icon" href="/favicon.ico" >
            <link rel="stylesheet" href="css/bootstrap.min.css" />
            <link rel="stylesheet" href="css/footer.css" />
            <!--[if lt IE 9]>
              <script src="//cdn.bootcss.com/html5shiv/3.7.3/html5shiv.min.js"></script>
              <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
            <![endif]-->
        </head>
        <body>
            <div class="container">
                <div class="page-header">
                    <h1>{$gameName}&nbsp;<small>活动进度</small></h1>
                </div>
    
                <div class="progress">
                    <div class="progress-bar {$finishBar}" role="progressbar" style="width: {$process}%; min-width:3em">
                        {$processNote}
                    </div>
                </div>
                
                <div class="alert alert-info" role="info">
                    <span class="glyphicon {$finishIco}" aria-hidden="true"></span>
                    <span class="sr-only">info:</span>
                      {$infoTitle}<br />
                     <span class="glyphicon glyphicon-user"aria-hidden="true"></span>
                     <span class="sr-only">user:</span>
                     你的学号是{$IDnums}，如有错误请<a style="color:goldenrod;" href="id.php?key=clean">点击这里</a>。
                     {$haveFinish}
                </div>
                
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h2 class="panel-title">地点提示及任务说明</h3>
                  </div>
                  <div class="panel-body">
                        {$infoText}
                  </div>
                </div>
            </div>
            
            <div class="footers">
                <a href="http://www.miitbeian.gov.cn/" rel="external nofollow" target="_blank">皖ICP备17002097号</a>
                <br/>
                <a>&copy;SPC | HITwh CST</a>
                <br/>
                <a>{$user}</a>
            </div>
        </body>
    </html>
EOF;
}

/**
 * 完成游戏
 * @param $IDnums this is the ID numbers that used to help gamers to identify who is
 */
function finishGame($IDnums)
{
    $config = $GLOBALS['config'];
    $mysql = new mysqli($config["SQL_URL"], $config["SQL_User"], $config["SQL_Password"], $config["SQL_Database"], $config["SQL_Port"]);
    if (mysqli_connect_errno()) {
        echo 'Database Connect is error - ' . mysqli_connect_error();
        exit();
    }
    $mysql->set_charset("utf8");

    $sq = "SELECT * FROM `config` LIMIT 1";
    $result = $mysql->query($sq);
    $row = $result->fetch_array();

    $mainTitle = $row['title'];
    $user = $row['user'];
    $gameName = $row['gamename'];

    $sq = "SELECT * FROM `finishConfig` LIMIT 1";
    $result = $mysql->query($sq);
    $row = $result->fetch_array();

    $infoText = $row['infotext'];
    $infotitle = $row['infotitle'];
    $mysql->close();

    echo gamePage("祝贺 - " . $mainTitle, $gameName, 2, 1, $IDnums, $infoText, $user, 0, $infotitle);
    exit;
}


?>
