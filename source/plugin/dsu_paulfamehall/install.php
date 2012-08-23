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
$sql = <<<EOF

DROP TABLE IF EXISTS `cdb_mingrentang`;
CREATE TABLE `cdb_mingrentang` (
  `uid` mediumint(8) NOT NULL,
  `username` varchar(20) NOT NULL default '',
  `value` varchar(100) NOT NULL,
  `dateline` int(11) unsigned NOT NULL,
  `gid` mediumint(8) NOT NULL,
  KEY `uid` (`uid`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `cdb_mingrentanggroup`;
CREATE TABLE `cdb_mingrentanggroup` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `groupname` varchar(50) NOT NULL,
  `grouppic` varchar(50) NOT NULL,
  `groupd` int(8) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `cdb_mingrentangset`;
CREATE TABLE `cdb_mingrentangset` (
  `variable` varchar(32) NOT NULL default '',
  `value` text NOT NULL,
  PRIMARY KEY  (`variable`)
) ENGINE=MyISAM;

INSERT INTO `cdb_mingrentangset` (`variable`, `value`) VALUES 
('fame_frmcolor', '#666666'),
('fame_frmcoloract', '#FF9900'),
('fame_txtcolor', '#000000'),
('fame_dftshape', '1'),
('fame_solidshape', '1'),
('fame_openfamereg', '1'),
('fame_blackname', ''),
('fame_minday', '30'),
('fame_mincredit', '100'),
('fame_minposts', '100'),
('fame_maxtext', '200'),
('fame_kcrx', '2'),
('fame_kcrz', '10'),
('fame_flashbg', '');

DROP TABLE IF EXISTS `cdb_mingrentangreg`;
CREATE TABLE `cdb_mingrentangreg` (
  `appid` smallint(5) unsigned NOT NULL auto_increment,
  `userid` smallint(4) NOT NULL,
  `username` varchar(100) NOT NULL,
  `dateline` int(11) unsigned NOT NULL,
  `credits` int(11) unsigned NOT NULL,
  `regdate` int(11) unsigned NOT NULL,
  `posts` int(11) unsigned NOT NULL,
  `about` varchar(200) NOT NULL,
  `gid` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`appid`)
) TYPE=MyISAM; 

DELETE FROM `cdb_common_cron` WHERE name='hallfameupdate';
INSERT INTO `cdb_common_cron` (`cronid`, `available`, `type`, `name`, `filename`, `lastrun`, `nextrun`, `weekday`, `day`, `hour`, `minute`) VALUES 
('NULL', '1', 'system', 'hallfameupdate', 'fame_hourly.inc.php', 1226077174, 1226079000, -1, -1, -1, '0	30');
EOF;
runquery($sql);
@copy(DISCUZ_ROOT.'./source/plugin/dsu_paulfamehall/fame_hourly.inc.php',DISCUZ_ROOT.'./source/include/cron/fame_hourly.inc.php');
$finish = TRUE;
?>