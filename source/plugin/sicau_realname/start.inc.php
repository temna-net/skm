<?php
/*
 * 川农实名，表单页面
 */
if(!defined('IN_DISCUZ')) {
        exit('Access Denied');
}
$isVerify = getuserprofile('field5') == '是';
if($isVerify){
	showmessage('您已经认证，不能重复认证，认证信息请到<a href="home.php?mod=spacecp&ac=profile">个人资料</a>查看', 'home.php?mod=spacecp&ac=profile');
}else{
	$sarea = $_GET['sarea'];
	$area='';
	//根据不同的域名来抓取不同的页面
	switch($sarea){
		case 'yjs':
			$area='yjs';
			break;
		case 'cd':
			$area='jiaowu';
            $url="http://".$area.".sicau.edu.cn/web/web/web/jwlogin09.asp";
            $script_url="http://".$area.".sicau.edu.cn/jiaoshi/bangong/js/";
            break;
		case 'djy':
			$area='djyjw';
            $url="http://".$area.".sicau.edu.cn/web/web/web/jwlogin09.asp";
            $script_url="http://".$area.".sicau.edu.cn/jiaoshi/bangong/js/";
            break;
		default:
            $area='jiaowu';
            $url="http://".$area.".sicau.edu.cn/web/web/web/jwlogin09.asp";
            $script_url="http://".$area.".sicau.edu.cn/jiaoshi/bangong/js/";
	}

	session_start();
			
	if('yjs' != $area){
		//如果不是研究生用户需要抓取js
		
		include dirname(__FILE__).'/class-snoopy.php';
		$snoopy = new Snoopy;
		if($snoopy->fetch($url)){
				$header=$snoopy->headers;
				$cookie_id=substr($header[9],12,20);
				$cookie=substr($header[9],33,24);
				$snoopy->setcookies();
				$_SESSION['jiaowu']=$snoopy->cookies;
		}else{
			   //echo "error fetching document: ".$snoopy->error."\n";
		}
		$snoopy->referer = $url;
		$script_url="http://".$area.".sicau.edu.cn/jiaoshi/bangong/js/";
		$snoopy->fetch($script_url);
        $jsScript = $snoopy->results;
	}
			

}
template('sicau_realname:start');
?>