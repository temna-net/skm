<?php
/*
 * =============================================
 * Discuz!X2 Extend pack
 * ---------------------------------------------
 * Fieah Workshop [www.fieah.com] (C)Forever.
 * Author: Kinwei 'Fieah' Lim
 * Email : f@12345.la
 * For Ashlyn Cheng & DDB, love you all.
 * =============================================
*/

!defined('IN_DISCUZ') && exit('Access Denied');

function build_cache_epack($return) {
	$data = array();
	$query = DB::query("SELECT m.uid, m.fid, f.name, f.type FROM ".DB::table('forum_moderator')." m, ".DB::table('forum_forum')." f WHERE inherited=0 AND f.status=1 AND m.fid=f.fid ORDER BY f.type, f.displayorder");

	while($mod = DB::fetch($query)) {
		$data[$mod['uid']] .= '<a href="forum.php?'.($mod['type'] != 'group' ? 'mod=forumdisplay&f' : 'g').'id='.$mod['fid'].'" target="_blank">'.$mod['name'].'</a>';
	}

	writetocache('epack', getcachevars(array('modarea' => $data)));
	if($return) {
		return $data;
	}
}
?>