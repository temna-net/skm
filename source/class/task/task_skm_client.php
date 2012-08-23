<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: task_skm_client.php 2012-8-23 13:13:37 神猫
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class task_skm_client {

	var $version = '1.0';
	var $name = '思可觅客户端任务';
	var $description = '鼓励使用新的思可觅客户端，只要使用新客户端下载种子即可完成任务。';
	var $copyright = '<a href="http://www.feeqi.com" target="_blank">神猫</a>';
	var $icon = '';
	var $period = '';
	var $periodtype = 0;
    var $conditions = array();
    
	function preprocess($task) { //申请任务成功后的附加处理
	}

	function csc($task = array()) {//判断任务是否完成 (返回 TRUE:成功 FALSE:失败 0:任务进行中进度未知或尚未开始  大于0的正数:任务进行中返回任务进度)
		global $_G;

        $time = getdate();
        $today = mktime(0, 0, 0, $time['mon'], $time['mday'], $time['year']);

		$res = DB::result_first("SELECT date FROM cdb_xbtit_history WHERE uid='$_G[uid]' AND date > $today AND agent='skmClent' LIMIT 1");
		if($res){
			return true;
		}else{
			return array('csc' => 0, 'remaintime' => 0);
		};
	}
    
    function sufprocess($task) { //完成任务后的附加处理
	}
    
	function view($task, $taskvars) {
        $return = "使用思可觅自己的客户端，更洋气！快下载吧！<a href='/thread-206364-1-1.html'>传送门</a>";
		return $return;
	}

}


?>