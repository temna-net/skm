<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if(isset($_REQUEST['submit'])){

	$name=$_POST['name'];
	$no=$_POST['no'];
	
	if(DB::update('common_member_profile', array('field3'=>$name, 'field4'=>$pre.$no, 'field5'=>'是'), array('uid'=>$_G['uid']))){
		//更新认证表，请确保川农认证是第一个！
		//verify表可能会没有这个uid
        $isOK = DB::update('common_member_verify', array('verify1'=>1), array('uid'=>$_G['uid']));
		if(!isOK){
			DB::insert('common_member_verify', array('uid' => $_G['uid'], 'verify1'=>1));
		}
		$message='认证成功！请到<a href="memcp.php">个人中心</a>查看认证信息，您的认证资料不会被公开';
	}else{
		$message="不好，出错了！请重试或者给管理员反映";
	}

}else{
    showmessage('成功信息', 'home.php?mod=spacecp&ac=plugin&id=sicau_realname:start', array(), array('header' => true));
	$message="网页已经过期，请重新提交";
}
template('sicau_realname:result');
?>

