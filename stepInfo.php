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
        //学号信息不进行检查
        $this->idnum = (int)$idnum;
        $this->showNearTask();
    }

    /**
     * check passkey, if is correct ,return true
     * @param $passkey
     * @return boolean
     */
    private function checkPasskey($passkey)
    {
        if ($this->over == true) return true;//游戏完成一直返回true
        if ($this->passKey == "over") {
            //说明进入晋级项
            $sq = "SELECT * FROM `tbl_finish` WHERE `passkey` = '" . $passkey . "' AND `isUsed` = '0'";
            $result = $this->sqlquery($sq);
            if ($result->num_rows == 0) return false;
            else return true;
        } elseif ($this->passKey == $passkey) {
            //核对passkey
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取下一个项目信息，passkey正确才能进行
     * @param $passkey
     * @return int 0-success 1-其他环节出错 2,3-晋级出错
     */
    public function getNextInfo($passkey)
    {
        //先对$passkey检查
        if (!preg_match("/^[a-z\d]*$/i", $passkey)) $passkey == "reset";
        //如果passkey是reset和空，显示最近的项目
        if ($passkey == "reset" || empty(trim($passkey))) {
            $this->showNearTask();

            return 0;
        }

        //如果passkey为over，判断passkey正确，写入数据库passkey已使用
        if ($this->passKey == "over") {
            if ($this->checkPasskey($passkey)) {
                //密钥正确，记录密钥
                $sq = "UPDATE `tbl_finish` SET `isUsed` = '1',`useClub`= '" . $this->club . "' WHERE `passkey`= '" . $passkey . "'";
                $this->sqlquery($sq);
                //设置记录，完成游戏
                $sq = "UPDATE `tbl_process` SET `finish` = '1' WHERE `passkey`= 'over' AND `clubID`= '" . $this->club . "'";
                $this->sqlquery($sq);

                $this->setNextInfo();
                return 0;
            } else {
                //密钥错误
                //晋级出错
                $sq = "SELECT * FROM `tbl_finish` WHERE `passkey` = '" . $passkey . "' LIMIT 1";
                $result = $this->sqlquery($sq);
                if ($result->num_rows != 0)
                    return 2;//说明失效
                else return 3;//不存在
            }
        }
        //判断passkey是否正确，正确启动下一个，不正确返回错误代码
        if ($this->checkPasskey($passkey)) {
            //设置完成
            $sq = "UPDATE `tbl_process` SET `finish` = '1' WHERE `passkey`= '" . $passkey . "' AND `clubID`= '" . $this->club . "'";
            $this->sqlquery($sq);

            $this->setNextInfo();

            return 0;
        } else {
            return 1;//其他环节出错
        }


    }

    /**
     * 设置下一个关卡
     */
    private function setNextInfo()
    {
        //如果over 设置成通过，不进行响应
        if ($this->over == true) {
            return;
        }
        //如果新建立的用户，然后开始任务
        //其他根据进度安排
        $sq = "SELECT COUNT(*) FROM `tbl_process` WHERE `clubID`='" . $this->club . "'";
        $result = $this->sqlquery($sq);
        $result = $result->fetch_array(MYSQLI_NUM);
        $result = $result[0];

        if ($result == 0) {
            //没有记录，说明刚刚开始，开始分配
            //获取第一步的数据
            $sq = "SELECT `passkey` FROM `tbl_content` WHERE `levelNumber`= '1'";
            $result = $this->sqlquery($sq);

            //随机选取
            $row = $result->fetch_all(MYSQLI_ASSOC);
            $this->passKey = $row[rand(0, $result->num_rows - 1)]['passkey'];
            //更新数据库，加入进程表
            $sq = "INSERT INTO `tbl_process` ( `clubID`, `passkey`, `finish`) VALUES ( '" . $this->club . "', '" . $this->passKey . "', '0')";
            $this->sqlquery($sq);
            $this->showNearTask();//获取最新的信息

        } else {
            //其他根据进度安排
            //最新进度无需获取
            //判断是否进行晋级赛
            if ($this->allStep == $this->gameStep) {
                //是晋级赛
                $this->showNearTask();//获取最新的信息
                //数据库已记录密钥使用

            } else {
                //不是晋级赛
                //下次是否是晋级赛
                if ($this->gameStep + 1 == $this->allStep) {
                    //下次是晋级赛
                    $this->gameStep++;
                    //设置标志
                    $sq = "INSERT INTO `tbl_process` ( `clubID`, `passkey`, `finish`) VALUES ( '" . $this->club . "', 'over', '0')";
                    $this->sqlquery($sq);
                    $this->showNearTask();//获取最新的信息

                } else {
                    //不是晋级赛
                    $this->gameStep++;
                    $sq = "SELECT `passkey` FROM `tbl_content` WHERE `levelNumber`= '" . $this->gameStep . "'";
                    $result = $this->sqlquery($sq);
                    //随机选取
                    $row = $result->fetch_all(MYSQLI_ASSOC);
                    $this->passKey = $row[rand(0, $result->num_rows - 1)]['passkey'];
                    //更新数据库，加入进程表
                    $sq = "INSERT INTO `tbl_process` ( `clubID`, `passkey`, `finish`) VALUES ( '" . $this->club . "', '" . $this->passKey . "', '0')";
                    $this->sqlquery($sq);
                    $this->showNearTask();//获取最新的信息
                }
            }

        }
    }

    /**
     * 设置成最近关卡
     */
    private function showNearTask()
    {

        //获取总步骤
        $sq = "SELECT COUNT(DISTINCT(`levelNumber`)) FROM `tbl_content`";
        $result = $this->sqlquery($sq);
        $result = $result->fetch_array(MYSQLI_NUM);
        $result = $result[0];
        $this->allStep = $result;
        //获取组号，查找数据库，如没有对应，则创建
        $sq = "SELECT * FROM `tbl_club` WHERE `schoolID` = '" . $this->idnum . "' LIMIT 1";
        $result = $this->sqlquery($sq);
        if ($result->num_rows == 0) {
            //没有信息，建立信息，组号不进行检查，采用学号
            $sq = "INSERT INTO `tbl_club` (`schoolID`, `clubNumber`, `innerPlay`) VALUES ('" . $this->idnum . "', '" . $this->idnum . "', '0')";
            $this->sqlquery($sq);
            $this->club = $this->idnum;
            $this->setNextInfo();//分配任务
        } else {
            $row = $result->fetch_array();
            $this->club = $row['clubNumber'];
        }


        //获取进行的passkey，游戏进行的步骤，注意最后晋级的时候passkey等会异常，采用over标志
        //注意passkey得不到的时候可能是游戏刚刚开始
        $sq = "SELECT `passkey` FROM `tbl_process` WHERE `finish`= 0 AND `clubID` = '" . $this->club . "'  LIMIT 1";
        $result = $this->sqlquery($sq);
        //判断是否完成项目
        if ($result->num_rows == 0) {
            $sq = "SELECT COUNT(*) FROM `tbl_process` WHERE `clubID`='" . $this->club . "' AND `passkey`='over'";
            $get = $this->sqlquery($sq);
            $get = $get->fetch_array(MYSQLI_NUM);
            $get = $get[0];
            if($get==0){
                //说明游戏还没分配任务
                //获取第一步的数据
                $sq = "SELECT `passkey` FROM `tbl_content` WHERE `levelNumber`= '1'";
                $results = $this->sqlquery($sq);

                //随机选取
                $row = $results->fetch_all(MYSQLI_ASSOC);
                $this->passKey = $row[rand(0, $results->num_rows - 1)]['passkey'];
                //更新数据库，加入进程表
                $sq = "INSERT INTO `tbl_process` ( `clubID`, `passkey`, `finish`) VALUES ( '" . $this->club . "', '" . $this->passKey . "', '0')";
                $this->sqlquery($sq);
                $this->showNearTask();//获取最新的信息

            }else{
                //完成
                $this->passKey = null;
                $this->gameStep = $this->allStep + 1;

            }
        } else {
            //未完成
            //获取passkey
            $row = $result->fetch_array();
            $this->passKey = $row['passkey'];
            //获取level
            $sq = "SELECT `levelNumber` FROM `tbl_content` WHERE `passkey` = '" . $this->passKey . "'  LIMIT 1";
            $result = $this->sqlquery($sq);
            $row = $result->fetch_array();
            $this->gameStep = $row['levelNumber'];
        }

        //获取显示信息
        if ($this->passKey != null) {
            $sq = "SELECT `content` FROM `tbl_content` WHERE `passkey` = '" . $this->passKey . "'  LIMIT 1";
            $result = $this->sqlquery($sq);
            $row = $result->fetch_array();
            $this->showInfo = $row['content'];
        } else {
            //完成页面
            $this->showInfo = null;
        }

        //游戏是否完成
        if ($this->passKey != null) {
            $this->over = false;
        } else {
            $this->over = true;
        }
        //获取剩余晋级数
        $sq = "SELECT COUNT(*) FROM `tbl_finish` WHERE `isUsed`= '0' ";
        $result = $this->sqlquery($sq);
        $result = $result->fetch_array(MYSQLI_NUM);
        $result = $result[0];
        $this->restNumber = $result;

    }

    /**
     * sql query
     * @param $sql
     * @return bool|mysqli_result
     */
    private function sqlquery($sql)
    {
        require_once "config.php";
        $config = $GLOBALS['config'];//获取$config
        $mysql = new mysqli($config["SQL_URL"], $config["SQL_User"], $config["SQL_Password"], $config["SQL_Database"], $config["SQL_Port"]);
        if (mysqli_connect_errno()) {
            echo 'Database Connect is error - ' . mysqli_connect_error();
            exit();
        }
        $mysql->set_charset("utf8");
        $result = $mysql->query($sql);
        $mysql->close();
        return $result;
    }

    public function getGameStep()
    {
        return $this->gameStep;
    }

    public function getAllStep()
    {
        return $this->allStep;
    }

    public function getshowInfo()
    {
        return $this->showInfo;
    }

    public function getOver()
    {
        return $this->over;
    }

    public function getRestNumber()
    {
        return $this->restNumber;
    }

    public function getIDnum()
    {
        return $this->idnum;
    }

    public function getClub()
    {
        return $this->club;
    }

}