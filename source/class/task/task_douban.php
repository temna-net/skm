<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: task_douban.php 16614 2012-02-20 06:26:06Z ratwu
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class task_douban {

	var $version = '1.0';
	var $name = '编辑电影帖的豆瓣链接';
	var $description = '为没有豆瓣链接的电影帖添加豆瓣链接';
	var $copyright = '<a href="http://ratwu.com" target="_blank">耗子吴</a>';
	var $icon = '';
	var $period = '';
	var $periodtype = 0;
    var $conditions = array(
		'num' => array(
			'title' => '编辑完成旧帖子豆瓣链接数量下限',
			'type' => 'text',
			'value' => '',
			'default' => 10,
			'sort' => 'complete',
		),
	);
    
	function preprocess($task) { //申请任务成功后的附加处理
	}

	function csc($task = array()) {//判断任务是否完成 (返回 TRUE:成功 FALSE:失败 0:任务进行中进度未知或尚未开始  大于0的正数:任务进行中返回任务进度)
		global $_G;

		$num = DB::result_first("SELECT COUNT(*) FROM cdb_xbtit_douban_task WHERE uid='$_G[uid]'") ;
		$numlimit = DB::result_first("SELECT value FROM cdb_common_taskvar WHERE taskid='$task[taskid]' AND variable='num'");
		if($num && $num >= $numlimit) {
			return TRUE;
		} else {
			return array('csc' => $num > 0 && $numlimit ? sprintf("%01.2f", $num / $numlimit * 100) : 0, 'remaintime' => 0);
		}
	}
    
    function sufprocess($task) { //完成任务后的附加处理
		global $_G;
		DB::query("DELETE FROM cdb_xbtit_douban_task WHERE uid='$_G[uid]' ");
	}
    
	function view($task, $taskvars) {
        $return = "编辑{$taskvars['complete']['num']['value']}个旧电影帖的豆瓣链接";
		return $return;
	}

}


?>