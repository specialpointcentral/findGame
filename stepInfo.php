<?php
/**
 * Created by PhpStorm.
 * User: huqi1
 * Date: 2018/3/8
 * Time: 21:57
 */

class stepInfo
{
    private $gameStep;//游戏进行的步骤
    private $allStep;//游戏总步骤
    private $showInfo;//显示信息
    private $passKey;//passkey
    private $over;//游戏完成
    private $restNumber;//剩余晋级数
    private $idnum;//id号
    private $club;//组

    function __construct($idnum)
    {
        $this->idnum=(int)$idnum;
        //读取数据库，开始得到信息
        require_once "config.php";
        $config=$GLOBALS['config'];//获取$config
        $mysql=new mysqli($config["SQL_URL"], $config["SQL_User"], $config["SQL_Password"], $config["SQL_Database"], $config["SQL_Port"]);
        if(mysqli_connect_errno()){
            echo 'Database Connect is error - '.mysqli_connect_error();
            exit();
        }
        $mysql->set_charset("utf8");
        //获取总步骤
        $sq = "SELECT COUNT(DISTINCT(`levelNumber`)) FROM `tbl_content`";
        $this->allStep = $mysql->query($sq);
        //获取组号，查找数据库，如没有对应，则创建
        //todo

        //获取进行的passkey，游戏进行的步骤
        $sq = "SELECT `passkey`,`level` FROM `tbl_process` WHERE `finish`= 0 AND `clubID` = ".$this->idnum;
        $result = $mysql->query($sq);
        $row = $result->fetch_array();
        $this->passKey=$row['passkey'];
        $this->gameStep=$row['level'];
        //获取显示信息
        $sq = "SELECT `content` FROM `tbl_content` WHERE `passkey`= ".$this->passKey;
        $result = $mysql->query($sq);
        $row = $result->fetch_array();
        $this->showInfo=$row['content'];
        //游戏是否完成


        //获取剩余晋级数
        $sq = "SELECT COUNT(*) FROM `tbl_process` WHERE `finish`= 0 ";
        $this->restNumber = $mysql->query($sq);


        $mysql->close();
    }
    public function getGameStep(){
        return $this->gameStep;
    }
    public function getAllStep(){
        return $this->allStep;
    }
    public function getShowInfo(){
        return $this->showInfo;
    }
    public function getPassKey(){
        return $this->passKey;
    }
    public function getOver(){
        return $this->over;
    }
    public function getRestNumber(){
        return $this->restNumber;
    }
    public function getIDnum(){
        return $this->idnum;
    }
    public function getClub(){
        return $this->club;
    }

}