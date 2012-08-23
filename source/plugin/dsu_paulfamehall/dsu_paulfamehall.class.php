<?php
/*
	dsu_paulfamehall_ECHO By shy9000 2011-07-21
*/
!defined('IN_DISCUZ') && exit('Access Denied');
class plugin_dsu_paulfamehall{
	function plugin_dsu_paulfamehall() {
		if(!@include DISCUZ_ROOT.'./data/cache/cache_famehall_cache.php') {
			$_FHCACHE['fhc'] = $this->_famehall_updateCache();
		}
		$this->fhc = $_FHCACHE['gname'];
	}
	function _famehall_updateCache() {
		$return = array();
		$query = DB::query("SELECT g.groupname, g.grouppic, m.uid, m.gid, m.value FROM ".DB::table('mingrentang')." m, ".DB::table('mingrentanggroup')." g WHERE m.gid=g.id ORDER BY m.uid");
		while($famehall = DB::fetch($query)) {
			$return[$famehall['uid']][] = $famehall['groupname'];
			$return2[$famehall['uid']][] = $famehall['gid'];
			$return3[$famehall['uid']][] = cutstr($famehall['value'], 40);
			$return4[$famehall['uid']][] = $famehall['grouppic'] ? $famehall['grouppic'] : 'def.gif';
		}
		require_once libfile('function/cache');
		writetocache('famehall_cache', getcachevars(array('_FHCACHE' => array('gname' => $return,'gid' => $return2,'js' => $return3,'pic' => $return4))));
		return $return;
	}
}
class plugin_dsu_paulfamehall_home extends plugin_dsu_paulfamehall {
	function space_profile_baseinfo_bottom() {
		global $_G;
		$lang = $scriptlang['dsu_paulfamehall'];
		$security2 = DB::fetch_first("SELECT * FROM ".DB::table('mingrentang')." where uid='{$_G[gp_uid]}'");
		if($security2){
			$giddb = DB::fetch_first("SELECT * FROM ".DB::table('mingrentanggroup')." WHERE id='{$security2['gid']}'");
			$giddb = $giddb['groupname'];
			return "<div class='pbm mbm bbda cl'><h2 class='mbn'>".lang('plugin/dsu_paulfamehall','echo_01')."</h2><p><font color=red>".lang('plugin/dsu_paulfamehall','echo_02')."</font></p><p>".lang('plugin/dsu_paulfamehall','echo_03')." <font color=#ff00cc>$giddb</font></p><p>".lang('plugin/dsu_paulfamehall','admin_54').": <font color=#ff00cc>".$security2['value']."</font></p></div>";
		}else{
			return "";
		}
	}
}
class plugin_dsu_paulfamehall_forum extends plugin_dsu_paulfamehall {
	function viewthread_sidebottom_output() {
		global $postlist;
		@include DISCUZ_ROOT.'./data/cache/cache_famehall_cache.php';
		$return = array();
		if(is_array($postlist)) {
			foreach($postlist as $key => $val) {
				if($val['author'] && isset($this->fhc[$val['authorid']])) {
					$pfc = $_FHCACHE['gname'][$val['authorid']][0];
					$pgid = $_FHCACHE['gid'][$val['authorid']][0];
					$pjs = $_FHCACHE['js'][$val['authorid']][0];
					$pic = $_FHCACHE['pic'][$val['authorid']][0];
					$return[] = "<p id=\"famehall_".$val['pid']."\" onmouseover=\"showMenu({'ctrlid':this.id, 'pos':'12'});\"><a href=\"plugin.php?id=dsu_paulfamehall:star&gid={$pgid}\"><img src=\"source/plugin/dsu_paulfamehall/gpic/".$pic."\" /></a></p><div id=\"famehall_".$val['pid']."_menu\" class=\"tip tip_4\" style=\"display:none\"><div class=\"tip_horn\"></div><div class=\"tip_c\"><center>".lang('plugin/dsu_paulfamehall','gxs_01')." <font color=\"red\">{$pfc}</font>.<br>".lang('plugin/dsu_paulfamehall','admin_54').": <font color=\"red\">{$pjs}</font></center></div></div>";
				} else {
					$return[] = '';
				}
			}
		}
		return $return;
	}
}
?>