<?php
/**
 * Created by PhpStorm.
 * User: huqi1
 * Date: 2018/1/13
 * Time: 19:42
 * TODO
 * This is the first page before the game.
 * This page is used to tips player game is not begging.
 *
 * database is 'config'AND'indexconfig'
 */
require_once "config.php";
date_default_timezone_set('Asia/Shanghai');
$mysql=new mysqli($config["SQL_URL"], $config["SQL_User"], $config["SQL_Password"], $config["SQL_Database"], $config["SQL_Port"]);
if(mysqli_connect_errno()){
    echo 'mysqli Connect is error'.mysqli_connect_error();
    exit();
}
$mysql->set_charset("utf8");

$sq = "SELECT * FROM config";
$result = $mysql->query($sq);
$row = $result->fetch_array();
$startTime = $row["startTime"];
$user=$row['user'];
$mainTitle=$row['title'];

$sq = "SELECT * FROM indexConfig";
$result = $mysql->query($sq);
$row = $result->fetch_array();

$mysql->close();

if (strtotime(date("y-m-d H:i:s"))>=strtotime($startTime)) {
    //时间到了，自动跳转
    header("Location: id.php");
    exit;
} else {
    $title = $row["title"];
    $head = $row["head"];
    $subHead = $row["subHead"];
    $display = $row["displayHitokoto"] ? "" : "display:none";
    try{
        $hitokoto = file_get_contents("https://sslapi.hitokoto.cn/?c=f&encode=text ");
    }catch (Exception $e){
        print $e->getMessage();
        $display=false;//cannot connect server, let do not show
    }

    $startTimes = date("m月d日H点", strtotime($startTime));
    $contain = $row["contain"];
    $lastTime = date("Y-m-d H:i", strtotime($startTime));
    /**
     * under this is html
     */
    echo <<<EOF
<!DOCTYPE html>
<html>
	<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	    <title>{$title}-{$mainTitle}</title>
	    <link rel="shortcut icon" href="/favicon.ico" >
	    <link rel="stylesheet" href="css/bootstrap.min.css" />
		<link rel="stylesheet" href="css/footer.css" />
			<!--[if lt IE 9]>
		      <script src="//cdn.bootcss.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		      <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
		    <![endif]-->
	    <link rel="stylesheet" href="css/style.css"/>
	    <style>
	    	strong{
	    		color: #CC9900;
	    	}
	    </style>
	</head>
	<body>
	<div class="container">
		<div class="page-header">
		  <h1>{$head}</h1>
		  <p><small>{$subHead}</small></p>
		</div>
	    <div class="spinner">
		  <div class="rect1"></div>
		  <div class="rect2"></div>
		  <div class="rect3"></div>
		  <div class="rect4"></div>
		  <div class="rect5"></div>
		</div>
		<div class="wrapper" >
			<p style="color: gray; {$display}"><small>{$hitokoto}</small></p>
			<p>活动即将开始，请耐心等待...</p>
			<p>{$startTimes}准时开启</p>
		    <div class="clock">
		        <div class="column days">
		            <div class="timer" id="days"></div>
		            <div class="text">DAYS</div>
		        </div>
		        <div class="timer days">:</div>
		        <div class="column">
		            <div class="timer" id="hours"></div>
		            <div class="text">HOURS</div>
		        </div>
		        <div class="timer">:</div>
		        <div class="column">
		            <div class="timer" id="minutes"></div>
		            <div class="text">MINUTES</div>
		        </div>
		        <div class="timer">:</div>
		        <div class="column">
		            <div class="timer" id="seconds"></div>
		            <div class="text">SECONDS</div>
		        </div>
		    </div>
		    <br />
		</div>
		
		<div class="container">
			{$contain}
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
	<script  src="js/jquery-2.1.0.js"></script>
	<script type="text/javascript" src="js/moment.js"></script>
	<script type="text/javascript" src="//cdn.bootcss.com/moment-timezone/0.5.11/moment-timezone-with-data.min.js"></script>
	<script type="text/javascript">
	\$(function(){
    function timer(settings){
        var config = {
            endDate: '{$lastTime}',
            timeZone: 'Asia/Shanghai',
            hours: \$('#hours'),
            minutes: \$('#minutes'),
            seconds: \$('#seconds'),
            newSubMessage: 'please wait a few minutes...'
        };
        function prependZero(number){
            return number < 10 ? '0' + number : number;
        }
        \$.extend(true, config, settings || {});
        var currentTime = moment();
        var endDate = moment.tz(config.endDate, config.timeZone);
        var diffTime = endDate.valueOf() - currentTime.valueOf();
        var duration = moment.duration(diffTime, 'milliseconds');
        var days = duration.days();
        var interval = 1000;
        var subMessage = \$('.sub-message');
        var clock = \$('.clock');
        if(diffTime < 0){
            endEvent(subMessage, config.newSubMessage, clock);
            return;
        }
        if(days > 0){
            \$('#days').text(prependZero(days));
            \$('.days').css('display', 'inline-block');
        }
        var intervalID = setInterval(function(){
            duration = moment.duration(duration - interval, 'milliseconds');
            var hours = duration.hours(),
                minutes = duration.minutes(),
                seconds = duration.seconds();
            days = duration.days();
            if(hours  <= 0 && minutes <= 0 && seconds  <= 0 && days <= 0){
                clearInterval(intervalID);
                endEvent(subMessage, config.newSubMessage, clock);
                window.location.reload();
            }
            if(days === 0){
                \$('.days').hide();
            }
            \$('#days').text(prependZero(days));
            config.hours.text(prependZero(hours));
            config.minutes.text(prependZero(minutes));
            config.seconds.text(prependZero(seconds));
        }, interval);
    }
    function endEvent(\$el, newText, hideEl){
        \$el.text(newText);
        hideEl.hide();
    }
    timer();
});
	</script>
	
	</body>
</html>
EOF;
}

?>