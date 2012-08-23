<?php
set_time_limit(2);
//加注释版本
/*
1、检测传来的各种参数是否合法
2、记录到历史，转换积分
3、对各种动作进行处理
*/
if (!preg_match("/^uTorrent|^μTorrent|^transmission/i", $_SERVER["HTTP_USER_AGENT"])){
    header("HTTP/1.0 500 Bad Request");
    die("This a a bittorrent application and can't be loaded into a browser");
}

//高峰限流
$load = sys_getloadavg();
if($load[0] > 30){
    header("HTTP/1.1 429 Too Many Requests");
    header("Retry-After: 3600");
    show_error("服务器压力太大，暂时停止tracker。");
}

include './config.inc.php';
include './include/db_mysql.class.php';
ignore_user_abort(1);		//忽略与用户的断开
error_reporting(E_ALL ^ E_NOTICE);
if (isset ($_GET["pid"]))
    $pid = $_GET["pid"];
else
    $pid = "";
if (get_magic_quotes_gpc()){
    $info_hash = bin2hex(stripslashes($_GET["info_hash"]));
}
else{
    $info_hash = bin2hex($_GET["info_hash"]);
}
$iscompact=(isset($_GET["compact"])?$_GET["compact"]=='1':false);
// 检测是否所有数据客户端都发送了
if (!isset($_GET["port"]) || !isset($_GET["downloaded"]) || !isset($_GET["uploaded"]) || !isset($_GET["left"]))
    show_error("BT客户端发送了错误的数据。");
$downloaded = (float)($_GET["downloaded"]);
$uploaded = (float)($_GET["uploaded"]);
$left = (float)($_GET["left"]);
$port = $_GET["port"];
$ip = getip();
$pid = AddSlashes(StripSlashes($pid));
if ($pid=="" || !$pid)
    show_error("请重新下载种子，种子的tracker是不合法的。");
// connect to db 连接数据库
$db = new dbstuff;
$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
// connect to db 连接slave数据库
if($dbhost2 && $load[0] < 4){
    $db_r = new dbstuff;
    $db_r->connect($dbhost2, $dbuser2, $dbpw2, $dbname2, $pconnect2);
}else{
    $db_r = &$db;
}
// connection is done ok 连接完成

$agent = mysql_real_escape_string($_SERVER["HTTP_USER_AGENT"]);
$respid = $db_r->query("SELECT pid,uid FROM {$tablepre}xbtit_users  WHERE pid='".$pid."' LIMIT 1");
if (!$respid || mysql_num_rows($respid)!=1)
    show_error("错误的pid值，用户不存在。请重新下载。");
$rowpid=mysql_fetch_assoc($respid);
$pid=$rowpid["pid"];
$uid=$rowpid["uid"];


//检查是否使用skm客户端，并修改为正确的useragent
if($_GET['feeqi_auth'] == substr($_GET['info_hash'], 0, 1).substr($_GET['peer_id'], 0, 1).'=='){
    $agent = "skmClent";
}

//检查用户积分是否为负分，低于-30无法下载
$credits =$db_r->query("SELECT credits FROM {$tablepre}common_member WHERE uid=".$uid." limit 1");
$credit = mysql_fetch_assoc($credits);
if ($credit['credits'] < -30) {
    show_error("您的积分少于-30，暂时无法下载，先去思可觅灌水去吧");
}
//积分验证结束
$res_tor =$db_r->query("SELECT * FROM {$tablepre}xbtit_files WHERE info_hash='".$info_hash."' limit 1");
if (mysql_num_rows($res_tor)==0){
    show_error("种子还未上传到服务器，请到论坛重新上传。");//种子不在服务器上面
}else{
    $results=mysql_fetch_assoc($res_tor);
    $tid=$results['tid'];
}
//获取事件
if (isset($_GET["event"]))
    $event = $_GET["event"];
else
    $event = "";
if (!is_numeric($port) || !is_numeric($downloaded) || !is_numeric($uploaded) || !is_numeric($left))
    show_error("下载客户端发送了错误的参数！");//数据字段发送错误
//获取种子类型，用于统计流量
$rstype=$db_r->query("SELECT highlight,displayorder FROM {$tablepre}forum_thread WHERE tid={$tid} LIMIT 1");
$typearray=mysql_fetch_assoc($rstype);
$type=$typearray['displayorder']>0 ? "top" : ($typearray['highlight']>0?"highlight":"normal");

header("Content-type: text/plain");
header("Pragma: no-cache");

// 记录到历史，转换积分
$resstat=$db_r->query("SELECT realup,realdown FROM {$tablepre}xbtit_peers WHERE uid={$uid} AND infohash=\"$info_hash\"");
//初始化 
if ($resstat){
    if(mysql_num_rows($resstat)>0){
        $livestat=mysql_fetch_assoc($resstat);
    }else{
        $livestat=array("realdown"=>0,"realup"=>0);
    }
    $new_download_true=max(0,$downloaded-$livestat["realdown"]);
    $new_upload_true=max(0,$uploaded-$livestat["realup"]);
    $new_download=$new_download_true*$down_weight[$type];
    $new_upload=$new_upload_true*$upload_weight[$type];
    //添加上传的积分记录
    if($new_upload>0){
        addtraffic($uid,$new_upload/1048576,$upload_credit);
    }
    //添加下载的积分记录
    if($new_download>0){
        addtraffic($uid,$new_download/1048576,$down_credit);
    }
}
mysql_free_result($resstat);
// begin history - 历史记录
$resu=$db_r->query("SELECT uid,realdown FROM {$tablepre}xbtit_history WHERE uid={$uid} AND infohash='$info_hash' limit 1");
if (mysql_num_rows($resu)==0){
    $db->query("INSERT INTO {$tablepre}xbtit_history (uid,infohash,active,agent,makedate,tid) VALUES ($uid,'$info_hash','yes','$agent',UNIX_TIMESTAMP(),{$tid})");
}
$db->query("UPDATE {$tablepre}xbtit_history set uploaded=IFNULL(uploaded,0)+$new_upload,realup=IFNULL(realup,0)+$new_upload_true,downloaded=IFNULL(downloaded,0)+$new_download,realdown=IFNULL(realdown,0)+$new_download_true,date=UNIX_TIMESTAMP(),tid={$tid} WHERE uid={$uid} AND infohash='$info_hash' limit 1");
mysql_free_result($resu);
// end history   
// 记录到peers
$db->query("UPDATE {$tablepre}xbtit_peers set realup={$uploaded},realdown={$downloaded} WHERE uid={$uid} AND infohash='$info_hash'");
//更新活动时间
$db->query("UPDATE {$tablepre}xbtit_files set lastactive=UNIX_TIMESTAMP() WHERE info_hash='$info_hash' limit 1");
switch ($event){
    case "started":
        $start = start($info_hash, $ip, $port,$uid,$tid);
        sendRandomPeers($info_hash);
        break;
    case "stopped":
        killPeer($uid, $info_hash);
        sendRandomPeers($info_hash);
        break;
    case "completed":
        $peer_exists = getPeerInfo($uid, $info_hash);
        if (!is_array($peer_exists)) {
            start($info_hash, $ip, $port, $uid, $tid);
        }
        else {
            $db->query("UPDATE {$tablepre}xbtit_peers SET status=\"seeder\", lastupdate=UNIX_TIMESTAMP() WHERE uid={$uid} AND infohash=\"$info_hash\"");
            if (mysql_affected_rows() == 1){
                add_finished($info_hash);
            }
        }
        sendRandomPeers($info_hash);
        break;
    case "":
        $peer_exists = getPeerInfo($uid, $info_hash);
        if (!is_array($peer_exists)) {
            start($info_hash, $ip, $port, $uid, $tid);
        }
        if ($left == 0){
            $db->query("UPDATE {$tablepre}xbtit_peers SET status=\"seeder\", lastupdate=UNIX_TIMESTAMP() WHERE uid={$uid} AND infohash=\"$info_hash\"");
        }
        sendRandomPeers($info_hash);
        break;
    default:
        show_error("客户端发送未定义的事件。");
}
mysql_close();

//*********************函数*****************//
//******************************************//
function sendRandomPeers($info_hash){
    global $tablepre,$db,$db_r;
    $query = "SELECT * FROM {$tablepre}xbtit_peers WHERE infohash=\"$info_hash\" ORDER BY RAND() LIMIT 30";
    echo "d";
    echo "8:intervali1800e";
    echo "12:min intervali300e";
    echo "5:peers";
    $result = @$db_r->query($query);
    if (isset($_GET["compact"]) && $_GET["compact"] == '1'){
        $p='';
        while ($row = mysql_fetch_assoc($result))
            $p .= str_pad(pack("Nn", ip2long($row["ip"]), $row["port"]), 6);//将ip，端口转换为二进制字符串，填充长度为6的长度
        echo strlen($p).':'.$p;
    }
    else{ // no_peer_id or no feature supported没有peer_id的时候发送
        echo 'l';
        while ($row = mysql_fetch_assoc($result))
        {
            echo "d2:ip".strlen($row["ip"]).":".$row["ip"];
            echo "4:porti".$row["port"]."ee";
        }
        echo "e";
    }
    echo "e";
    mysql_free_result($result);
}
// 删除一个种子
function killPeer($uid, $hash){
    global $tablepre,$db;
    @$db->query("DELETE FROM {$tablepre}xbtit_peers WHERE uid=\"$uid\" AND infohash=\"$hash\"");
}

function add_finished($hash){
    global $tablepre,$db;
    $db->query("UPDATE {$tablepre}xbtit_files SET finished=finished+1,lastactive=UNIX_TIMESTAMP() where info_hash='{$hash}' limit 1");
}
// Returns info on one peer //返回种子信息
function getPeerInfo($uid, $hash){
    global $tablepre,$db,$db_r;
    $query = "SELECT * from {$tablepre}xbtit_peers where uid=\"$uid\" AND infohash=\"$hash\" limit 1";
    $results = $db_r->query($query) or show_error("tracker服务器报告：错误的种子");
    $data = mysql_fetch_assoc($results);
    if (!($data))
        return false;
    return $data;
}

function start($info_hash, $ip, $port, $uid,$tid){
    global $tablepre,$db,$db_r,$left;
    $ip = getip();
    $ip = mysql_real_escape_string($ip);
    $agent = mysql_real_escape_string($_SERVER["HTTP_USER_AGENT"]);
    if ($left == 0)
        $status = "seeder";
    else
        $status = "leecher";
    $query=$db_r->query("SELECT * from {$tablepre}xbtit_peers where infohash=\"$info_hash\" and uid=\"$uid\"");
    $peer = mysql_fetch_array($query);
    if(empty($peer)){
        $db->query("INSERT INTO {$tablepre}xbtit_peers (infohash,port,ip,lastupdate,status,tid,client,uid) values ('$info_hash',$port,'$ip',UNIX_TIMESTAMP(),'$status',$tid,'$agent',$uid)");
    }
}
function show_error($message, $log=false) {
    if ($log)
        error_log("BtiTracker: ERROR ($message)");
    echo 'd14:failure reason'.strlen($message).":$message".'e';
    die();
}
function getip() {
    if($_SERVER["HTTP_X_REAL_IP"]){
        return $_SERVER["HTTP_X_REAL_IP"];
    }
    if (getenv('HTTP_CLIENT_IP') && long2ip(ip2long(getenv('HTTP_CLIENT_IP')))==getenv('HTTP_CLIENT_IP') && validip(getenv('HTTP_CLIENT_IP')))
        return getenv('HTTP_CLIENT_IP');
    if (getenv('HTTP_X_FORWARDED_FOR') && long2ip(ip2long(getenv('HTTP_X_FORWARDED_FOR')))==getenv('HTTP_X_FORWARDED_FOR') && validip(getenv('HTTP_X_FORWARDED_FOR')))
        return getenv('HTTP_X_FORWARDED_FOR');
    if (getenv('HTTP_X_FORWARDED') && long2ip(ip2long(getenv('HTTP_X_FORWARDED')))==getenv('HTTP_X_FORWARDED') && validip(getenv('HTTP_X_FORWARDED')))
        return getenv('HTTP_X_FORWARDED');
    if (getenv('HTTP_FORWARDED_FOR') && long2ip(ip2long(getenv('HTTP_FORWARDED_FOR')))==getenv('HTTP_FORWARDED_FOR') && validip(getenv('HTTP_FORWARDED_FOR')))
        return getenv('HTTP_FORWARDED_FOR');
    if (getenv('HTTP_FORWARDED') && long2ip(ip2long(getenv('HTTP_FORWARDED')))==getenv('HTTP_FORWARDED') && validip(getenv('HTTP_FORWARDED')))
        return getenv('HTTP_FORWARDED');
    return long2ip(ip2long($_SERVER['REMOTE_ADDR']));
}
function addtraffic($uid,$size,$credit_no){
    global $db,$tablepre;
    $extcredits1=$extcredits2=$extcredits3=$extcredits4=$extcredits5=$extcredits6=$extcredits7=$extcredits8=0;
    $temp="extcredits".$credit_no;
    $$temp=$size;
    $db->query("insert into {$tablepre}common_credit_log (uid,operation,relatedid,dateline,extcredits1,extcredits2,extcredits3,extcredits4,extcredits5,extcredits6,extcredits7,extcredits8) values ({$uid},'RCV',{$uid},UNIX_TIMESTAMP(),{$extcredits1},{$extcredits2},{$extcredits3},{$extcredits4},{$extcredits5},{$extcredits6},{$extcredits7},{$extcredits8})");
    $db->query("UPDATE {$tablepre}common_member_count set {$temp}={$temp}+{$size} WHERE uid={$uid} limit 1");
}
?>
