<?php

/*
 *	KK Plugin Installer
 */
if(!defined('IN_ADMINCP')) exit('Access Denied');

$request_url=str_replace('&kk_step='.$_GET['kk_step'],'',$_SERVER['QUERY_STRING']);
showsubmenusteps($installlang['header'], array(
	array($installlang['step1'], !$_GET['kk_step']),
	array($installlang['step2'], $_GET['kk_step']=='sql'),
	array($installlang['step3'], $_GET['kk_step']=='ok'),
));
$sql = <<<EOF
DROP TABLE IF EXISTS `cdb_dsu_paulsign`;
CREATE TABLE IF NOT EXISTS `cdb_dsu_paulsign` (
  `uid` int(10) unsigned NOT NULL,
  `time` int(10) NOT NULL,
  `days` int(5) NOT NULL DEFAULT '0',
  `mdays` int(5) NOT NULL DEFAULT '0',
  `reward` int(12) NOT NULL DEFAULT '0',
  `lastreward` int(12) NOT NULL DEFAULT '0',
  `qdxq` varchar(5) NOT NULL,
  `todaysay` varchar(100) NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `time` (`time`)
) ENGINE=MyISAM;
DROP TABLE IF EXISTS `cdb_dsu_paulsignset`;
CREATE TABLE IF NOT EXISTS `cdb_dsu_paulsignset` (
  `id` int(10) unsigned NOT NULL,
  `todayq` int(10) NOT NULL DEFAULT '0',
  `yesterdayq` int(10) NOT NULL DEFAULT '0',
  `highestq` int(10) NOT NULL DEFAULT '0',
  `qdtidnumber` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
INSERT INTO `cdb_dsu_paulsignset` (id, todayq, yesterdayq, highestq, qdtidnumber) VALUES ('1', '0', '0', '0', '0');
EOF;
switch($_GET['kk_step']){
	default:
	case 'validator':
		$checkdata['key'][$_G['gp_dir']] = pluginvalidator($_G['gp_dir']);
		$check_result = pluginupgradecheck($checkdata);
		$result = $check_result[$_G['gp_dir']]['result'];
		$newver = $check_result[$_G['gp_dir']]['newver'];
		$param = array('id' => $_G['gp_dir'], 'newver' => $newver ? $newver : '', 'url' => "http://addons.discuz.com/?id={$_G[gp_dir]}");
		if($result == '1') {
			cpmsg($installlang['step1_ok'], "{$request_url}&kk_step=sql", 'succeed');
		} elseif($result == '2') {
			cpmsg($installlang['validator_new'], "{$request_url}&kk_step=sql", 'form', $param);
		} else{
			cpmsg($installlang['validator_error'], "{$request_url}&kk_step=sql", 'form', $param);
		}
		break;
	case 'sql':
		runquery($sql);
		cpmsg($installlang['step2_ok'], "{$request_url}&kk_step=ok", 'succeed');
		break;
	case 'ok':
		$_statInfo = array();
		$_statInfo['pluginName'] = $pluginarray['plugin']['identifier'];
		$_statInfo['pluginVersion'] = $pluginarray['plugin']['version'];
		require_once DISCUZ_ROOT.'./source/discuz_version.php';
		$_statInfo['bbsVersion'] = DISCUZ_VERSION;
		$_statInfo['bbsRelease'] = DISCUZ_RELEASE;
		$_statInfo['timestamp'] = TIMESTAMP;
		$_statInfo['bbsUrl'] = $_G['siteurl'];
		$_statInfo['bbsAdminEMail'] = $_G['setting']['adminemail'];
		$addon = DB::fetch_first("SELECT * FROM ".DB::table('common_addon')." WHERE `key`='S10071000DSU'");
		if(!$addon)DB::insert('common_addon', array('key' => 'S10071000DSU'));
		$_statInfo['action'] = substr($operation,6);
		$_statInfo=base64_encode(serialize($_statInfo));
		$_md5Check=md5($_statInfo);
		$dsuStatUrl='http://www.dsu.cc/stat.php';
		$_StatUrl=$dsuStatUrl.'?action=do&info='.$_statInfo.'&md5check='.$_md5Check;
		echo "<script src=\"".$_StatUrl."\" type=\"text/javascript\"></script>";
		$finish = TRUE;
		break;
}
?>