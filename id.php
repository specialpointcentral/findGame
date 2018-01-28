<?php
/**
 * Created by PhpStorm.
 * User: huqi1
 * Date: 2018/1/15
 * Time: 12:40
 * TODO
 * this page is used to identify the player
 * players need to input their id number and page will redirect to the play page
 * also if players input error id, this page will process the error requests
 */
require_once "config.php";
date_default_timezone_set('Asia/Shanghai');
session_start();
if ($_GET['key']=="clean")
{
    //clear session, marked id number is wrong and user require.
    unset($_SESSION['IDnum']);
    //postkey is used to transfer to the play.php
    //this is the key identify this step is the needed
    $postkey="reset";
}
else
{
    $postkey=$_GET['key'];
}
if (isset($_SESSION['IDnum'])&&!empty($_SESSION['IDnum']))
{
    //session is exist
    //this need we direct to play.php to process the require
    header("Location: /play.php?key=".$postkey);
    exit;
}

/**
 * this is show the page that no ID number
 */

$mysql=new mysqli($config["SQL_URL"], $config["SQL_User"], $config["SQL_Password"], $config["SQL_Database"], $config["SQL_Port"]);
if(mysqli_connect_errno()){
    echo 'mysqli Connect is error'.mysqli_connect_error();
    exit();
}
$mysql->set_charset("utf8");
//first is the start time
$sql="SELECT * FROM config";
$result=$mysql->query($sql);
$row=$result->fetch_array();
$user=$row['user'];
$startTime=$row['startTime'];
$mainTitle=$row['title'];

if (strtotime(date("y-m-d H:i:s"))<=strtotime($startTime)){
    // is not touch the time
    $mysql->close();
    header("Location: index.php");
    exit;
}


$sql="SELECT * FROM idConfig";
$result=$mysql->query($sql);
$row=$result->fetch_array();
$title=$row['title'];
$btn=$row['btn'];
$mysql->close();
echo<<<EOF
<!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>{$title}-{$mainTitle}</title>
		<link rel="shortcut icon" href="/favicon.ico" >
		<link rel="stylesheet" href="css/bootstrap.min.css" />
		<link rel="stylesheet" href="css/footer.css" />
		<!--[if lt IE 9]>
	      <script src="//cdn.bootcss.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	      <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
	    <![endif]-->
	</head>
	<body>
		<div class="container" style="padding-top: 5px;">
			<div class="jumbotron">
				<h1>Hello!欢迎参加活动</h1>
				<p>让我们先做一点准备工作，看看你是谁吧</p>
				<p>请填写你的学号，然后进入游戏</p>
				<div class="alert alert-info" role="alert">
					可能你已经完成了此准备工作，但现在仍然需要输入学号。没关系，服务器已经保存了你的活动进度，只要再次输入学号就可继续。
				</div>
			</div>
			
			<form action="play.php?key={$postkey}" method="post">
				<label for="IDnum">这儿填写你的学号</label>
				<div class="input-group input-group-lg">
				  <span class="input-group-addon" id="addon"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></span>
				  <input type="text" class="form-control" id="IDnum" name="IDnum" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" class="form-control" aria-describedby="addon" />
				</div>
				<br />
				<button type="submit" class="btn btn-success btn-block btn-lg" value="Submit">{$btn}</button>
			</form>
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


?>