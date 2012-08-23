<?php
/*
	dsu_paulfamehall_UPDATE By shy9000 2010-07-15
*/
if(!defined('IN_DISCUZ')) exit('Access Denied');
require_once DISCUZ_ROOT.'./source/plugin/dsu_paulfamehall/fame.func.php';
$query = DB::query("SELECT * FROM ".DB::table('mingrentanggroup')." ORDER BY groupd");
while($gx = DB::fetch($query)) {
	SILVER_writeFameHallXML($gx[id]);
	$gxs[] = $gx;
}
?>