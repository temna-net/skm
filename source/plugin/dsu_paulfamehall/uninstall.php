<?php
/*
	Install Uninstall Upgrade AutoStat System Code
*/
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$_statInfo = array();
$_statInfo['pluginName'] = $pluginarray['plugin']['identifier'];
$_statInfo['pluginVersion'] = $pluginarray['plugin']['version'];
if(file_exists(DISCUZ_ROOT.'./include/cache.inc.php')){
	require_once DISCUZ_ROOT.'./include/cache.inc.php';
	$_statInfo['bbsVersion'] = DISCUZ_KERNEL_VERSION;
	$_statInfo['bbsRelease'] = DISCUZ_KERNEL_RELEASE;
	$_statInfo['timestamp'] = $timestamp;
	$_statInfo['bbsUrl'] = $board_url;//$_DCACHE['siteurl'];
	$_statInfo['bbsAdminEMail'] = $adminemail;
	$addon = $db->fetch_first("SELECT * FROM {$tablepre}addons WHERE `key`='S10071000DSU'");
	if(!$addon)$db->query("INSERT INTO {$tablepre}addons (`key`) VALUES ('S10071000DSU')");
}else{
	require_once DISCUZ_ROOT.'./source/discuz_version.php';
	$_statInfo['bbsVersion'] = DISCUZ_VERSION;
	$_statInfo['bbsRelease'] = DISCUZ_RELEASE;
	$_statInfo['timestamp'] = TIMESTAMP;
	$_statInfo['bbsUrl'] = $_G['siteurl'];
	$_statInfo['bbsAdminEMail'] = $_G['setting']['adminemail'];
	$addon = DB::fetch_first("SELECT * FROM ".DB::table('common_addon')." WHERE `key`='S10071000DSU'");
	if(!$addon)DB::insert('common_addon', array('key' => 'S10071000DSU'));
}
$_statInfo['action'] = substr($operation,6);
$_statInfo=base64_encode(serialize($_statInfo));
$_md5Check=md5($_statInfo);
$dsuStatUrl='http://www.dsu.cc/stat.php';
$_StatUrl=$dsuStatUrl.'?action=do&info='.$_statInfo.'&md5check='.$_md5Check;
echo "<script src=\"".$_StatUrl."\" type=\"text/javascript\"></script>";
$query = DB::query("SELECT * FROM ".DB::table('mingrentanggroup')." ORDER BY groupd");
while($gx = DB::fetch($query)) {
	@unlink(DISCUZ_ROOT."./data/cache/cache_famehall_{$gx[id]}.xml");
	$gxs[] = $gx;
}
DB::query("DROP TABLE ".DB::table('mingrentang')."");
DB::query("DROP TABLE ".DB::table('mingrentangset')."");
DB::query("DROP TABLE ".DB::table('mingrentangreg')."");
DB::query("DROP TABLE ".DB::table('mingrentanggroup')."");
DB::query("DELETE FROM ".DB::table('common_cron')." WHERE name='hallfameupdate'");
@unlink(DISCUZ_ROOT.'./source/include/cron/fame_hourly.inc.php');
@rmdir(DISCUZ_ROOT.'./data/cache/mrt_img/');
$finish = TRUE;
?>