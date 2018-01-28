<?php
/**
 * Created by PhpStorm.
 * User: huqi1
 * Date: 2018/1/17
 * Time: 21:31
 * TODO
 * function collection
 * this include that:
 * 1* error page:'noCertification'
 * 2* finish page:'finishGame'
 */
/**
 * @param $reasonTitle the reason title why give error page
 * @param $reasonTxt the reason why give error page
 */
require_once "config.php";
function noCertification($reasonTitle,$reasonTxt)
{
    $config=$GLOBALS['config'];
    $mysql=new mysqli($config["SQL_URL"], $config["SQL_User"], $config["SQL_Password"], $config["SQL_Database"], $config["SQL_Port"]);
    if(mysqli_connect_errno()){
        echo 'mysqli Connect is error'.mysqli_connect_error();
        exit();
    }
    $mysql->set_charset("utf8");

    $sq = "SELECT * FROM config";
    $result = $mysql->query($sq);
    $row = $result->fetch_array();

    $mainTitle=$row['title'];
    $user=$row['user'];
    echo<<<EOF
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
                    <div style="display:none">
                    <script src="https://s4.cnzz.com/z_stat.php?id=1261688187&web_id=1261688187" language="JavaScript"></script>
                    </div>
                </div>
            </body>
    </html>
EOF;
    exit;
}

/**
 * @param $IDnums this is the ID numbers that used to help gamers to identify who is
 */
function finishGame($IDnums){
    $config=$GLOBALS['config'];
    $mysql=new mysqli($config["SQL_URL"], $config["SQL_User"], $config["SQL_Password"], $config["SQL_Database"], $config["SQL_Port"]);
    if(mysqli_connect_errno()){
        echo 'mysqli Connect is error'.mysqli_connect_error();
        exit();
    }
    $mysql->set_charset("utf8");

    $sq = "SELECT * FROM config";
    $result = $mysql->query($sq);
    $row = $result->fetch_array();

    $mainTitle=$row['title'];
    $user=$row['user'];
    $gameName=$row['gamename'];

    $sq = "SELECT * FROM finishconfig";
    $result = $mysql->query($sq);
    $row = $result->fetch_array();

    $infoTitle=$row['infotitle'];
    $infoText=$row['infotext'];

    echo<<<EOF
    <!DOCTYPE html>
    <html lang="zh-CN">
        <head>
            <meta charset="utf-8" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>祝贺！-{$mainTitle}</title>
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
                    <div class="progress-bar progress-bar-success" role="progressbar" style="width: 100%; min-width:3em">
                        完成任务
                    </div>
                </div>
                
                <div class="alert alert-info" role="info">
                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                    <span class="sr-only">info:</span>
                      {$infoTitle}<br />
                     <span class="glyphicon glyphicon-user"aria-hidden="true"></span>
                     <span class="sr-only">user:</span>
                     你的学号是{$IDnums}，如有错误请<a style="color:goldenrod;" href="/id.php?key=clean">点击这里</a>。
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
                <div style="display:none">
                <script src="https://s4.cnzz.com/z_stat.php?id=1261688187&web_id=1261688187" language="JavaScript"></script>
                </div>
            </div>
        </body>
    </html>
EOF;
    exit;
}


?>
