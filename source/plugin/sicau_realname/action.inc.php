<?php
if (!defined('IN_DISCUZ')) {
    dexit('Access Denied');
}
if (!$_G['uid']) {
    showmessage('您还没有登录');
}
session_start();
if (isset($_REQUEST['submit'])) {

    $user = $_POST['user'];
    $pwd = $_POST['pwd'];
    $usertype = $_POST['usertype'];
    $sarea = $_GET['sarea'];
    $area = in_array($_POST['area'], array('jiaowu', 'djyjw', 'yjs')) ? $_POST['area'] : 'jiaowu';
    //中文名
    $areatext = '';
    //学号前缀
    $pre_no = '';
    switch($area){
        case 'jiaowu':
            $pre_no = '';
            break;
        case 'djyjw':
            $pre_no = 'djy';
            break;
        case 'yjs':
            $pre_no = 'yjs';
            break;
        default:
            $pre_no = '';
    }

    $query = DB::query("SELECT field4 FROM " . DB::table(common_member_profile) . " where field4 = '" . $pre_no . $user . "'");
    if (DB::num_rows($query) > 0) {
        showmessage('此学号已经认证，不能重复认证，认证信息请到个人中心查看');
    }

    include "class-snoopy.php";
    $snoopy = new Snoopy;

    if('yjs' == $sarea){
        //将所有post数据提交给action4yan文件处理，并且结束php执行
        $area = 'yjs';
        $areatext = iconv('GBK', 'UTF-8//IGNORE', '研究生用户');

        $formvars = array();
        $formvars['login'] = $_POST['usertype'];
        $formvars['password'] = $_POST['pwd'];
        $formvars['username'] = $_POST['user'];
        $action_url = "http://yan.sicau.edu.cn/system/index.asp";

//获取访问cookie
        $snoopy->fetch("http://yan.sicau.edu.cn/");
        $snoopy->setcookies();
        $snoopy->submit($action_url, $formvars);
        $kebiao = $snoopy->results;
//        $kebiao = iconv('GB2312', 'UTF-8', $kebiao); //转换编码
        if (preg_match('/错误/', $kebiao)) {
            $flag = 0; //未通过验证
        } else {
            $info_url = "http://yan.sicau.edu.cn/system/stustatus/dyxjb.asp?MenuId=1&SubMenuId=14"; //个人信息页面
            $snoopy->fetch($info_url);
            preg_match_all("|宋体'>(.*)<span|U", iconv('GB2312', 'UTF-8', $snoopy->results), $info, PREG_PATTERN_ORDER);
            $result['name'] = $info[1][1];
            $result['shenfen'] = '研究生';
            $flag = $result['name'] != '';
        }
        $result['flag'] = $flag;
    }else{
        $areatext = iconv('GBK', 'UTF-8//IGNORE', '雅安·成都');

        $snoopy->cookies = $_SESSION['jiaowu'];
        $snoopy->referer = "http://" . $area . ".sicau.edu.cn/xuesheng/bangong/main/index1.asp";

        $checkpage = "http://" . $area . ".sicau.edu.cn/jiaoshi/bangong/check.asp";
        $arr["user"] = $user;
        $arr["pwd"] = $pwd;
        $snoopy->submit($checkpage, $arr);

        $snoopy->fetch("http://" . $area . ".sicau.edu.cn/xuesheng/bangong/main/index1.asp");
        $kebiao = $snoopy->results;
        if ($area == 'jiaowu') {
            //雅安、温江校区开始 --START---
            preg_match_all('/<td width=\"99\" align=\"left\">(.+)<\/td>/', $kebiao, $infos);
            preg_match_all('/<td align=\"left\">(.+)<\/td>/', $kebiao, $shenfen);
            if ($infos[1][1] == '') {
                $flag = 0; //未通过验证
            } else {
                $flag = 1;
                if ($infos[1][1]) {
                    $name = iconv('GBK', 'UTF-8//IGNORE', $infos[1][1]);
                    //echo $name;
                } else {
                    $name = '未知姓名';
                    //echo $name;
                }
                $shenfen = iconv('GBK', 'UTF-8', $shenfen[1][0]);
            }
            //雅安、都江堰校区开始 --END---
        } else {
            //下面的是老版本教务管理的
            //都江堰校区开始 --START---
            $kebiao = iconv('GB2312', 'UTF-8', $kebiao); //转换编码
            preg_match("/姓.*名：(.*)<\/td>/", $kebiao, $name);
            preg_match("/身.*份：(.*)<\/td>/", $kebiao, $shenfen);
            if ($name == null) {
                $flag = 0; //未通过验证
            } else {
                $flag = 1;
                $name = $name[1];
                $shenfen = $shenfen[1];
            }
            //都江堰校区开始 --END---
        }
        $result = array('flag' => $flag,
            'name' => $name,
            'no' => $user,
            'shenfen' => $shenfen);
        //print_r($result);
    }
}else{
    showmessage('成功信息', 'home.php?mod=spacecp&ac=plugin&id=sicau_realname:start', array(), array('header' => true));
    $message="网页已经过期，请重新提交";
}
template('sicau_realname:action');
?>

