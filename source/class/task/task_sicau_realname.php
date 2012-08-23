<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: task_sicau_realname.php 16614 2012-5-5 18:42:10 神猫
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class task_sicau_realname {

	var $version = '1.0';
	var $name = '川农学号认证任务';
	var $description = '鼓励学号认证';
	var $copyright = '<a href="http://www.feeqi.com" target="_blank">神猫</a>';
	var $icon = '';
	var $period = '';
	var $periodtype = 0;
    var $conditions = array();
    
	function preprocess($task) { //申请任务成功后的附加处理
	}

	function csc($task = array()) {//判断任务是否完成 (返回 TRUE:成功 FALSE:失败 0:任务进行中进度未知或尚未开始  大于0的正数:任务进行中返回任务进度)
		global $_G;

		$isSicau = DB::result_first("SELECT verify1 FROM cdb_common_member_verify WHERE uid='$_G[uid]'");
		if($isSicau == 1){
			return true;
		}else{
			return array('csc' => 0, 'remaintime' => 0);
		};
	}
    
    function sufprocess($task) { //完成任务后的附加处理
	}
    
	function view($task, $taskvars) {
        $return = "证明自己地地道道的川农人，快去完成学号认证吧！<a href='home.php?mod=spacecp&ac=plugin&id=sicau_realname:start'>传送门</a>";
		return $return;
	}

}


?>