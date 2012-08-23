<?php
if(!defined('IN_DISCUZ')) {
exit('Access Denied');
}

class plugin_showimg_dzx{





	function common() {
        global $_G;
		$listpicforums = (array)unserialize($_G['cache']['plugin']['showimg_dzx']['listpic_forums']);
		if(in_array('', $listpicforums)) {
			$listpicforums = array();
		}
		//判断选择的版块或模块来打开picstyle开关
		if(in_array($_G['fid'], $listpicforums) && ($_G['gp_mod'] == 'viewthread' || $_G['gp_topicsubmit'] == 'yes')){
			$_G['forum']['picstyle'] = 1;
		}
		
	}


	function forumdisplay_thread_output(){
	
		global $_G;
		loadcache('plugin');
		
		//调用选择版块变量
		$listpicforums = (array)unserialize($_G['cache']['plugin']['showimg_dzx']['listpic_forums']);
		if(in_array('', $listpicforums)) {
			$listpicforums = array();
		}
		//判断选择的版块来调用图片
		if(in_array($_G['fid'], $listpicforums)){
			$threadlist = array();
			$threadlist = $_G['forum_threadlist'];
			$piclist = array();
			foreach($threadlist as $key => $value){
				//
				$value['coverpath'] = getthreadcover($value['tid'], $value['cover']);
				if($value['coverpath']!=""){
					$piclist[$key] = '<a href="forum.php?mod=viewthread&tid='.$value['tid'].'&extra=page%3D1"><img src="'.$value['coverpath'].'" align="absmiddle" height="68" border="0" style="padding:2px; border:1px solid #e0e0e0;"></a>';
				}
			}
			$return = $piclist;
		}
		return $return;
	}
	

	
}

class plugin_showimg_dzx_forum extends plugin_showimg_dzx{

}

?>
