<?php

/**
 *      dsupaulsign_Task_DSU TEAM
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class task_dsupaulsign {

	var $version = '1.0';
	var $name = '&#27599;&#26085;&#31614;&#21040;&#27425;&#25968;&#22870;&#21169;';
	var $description = '&#26681;&#25454;&#29992;&#25143;&#30340;&#31614;&#21040;&#27425;&#25968;&#21457;&#25918;&#22870;&#21169;';
	var $copyright = '<a href="http://www.dsu.cc" target="_blank">DSU Team</a>';
	var $icon = '';
	var $period = '';
	var $periodtype = 0;
	var $conditions = array(
		'num' => array(
			'title' => '&#33719;&#21462;&#22870;&#21169;&#30340;&#26368;&#23567;&#31614;&#21040;&#27425;&#25968;',
			'description' => '&#36755;&#20837;&#31614;&#21040;&#27425;&#25968;.&#36798;&#21040;&#35813;&#27425;&#25968;&#23601;&#21487;&#39046;&#21462;&#22870;&#21169;.',
			'type' => 'text',
			'value' => '',
			'sort' => 'complete',
		)
	);

	function csc($task = array()) {
		global $_G;

		$num = DB::result_first("SELECT days FROM ".DB::table('dsu_paulsign')." WHERE uid='$_G[uid]'");
		$numlimit = DB::result_first("SELECT value FROM ".DB::table('common_taskvar')." WHERE taskid='$task[taskid]' AND variable='num'");

		if($num && $num >= $numlimit) {
			return TRUE;
		} else {
			return array('csc' => $num > 0 && $numlimit ? sprintf("%01.2f", $num / $numlimit * 100) : 0, 'remaintime' => 0);
		}
	}

}


?>