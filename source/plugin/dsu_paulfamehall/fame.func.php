<?php

/*
	dsu_paulfamehall_XML By SILVER And shy9000[DSU Team] 2011-06-08
*/
!defined('IN_DISCUZ') && exit('Access Denied');
function SILVER_writeFameHallXML($gid) {

	global $_G,$_config;
	$famexml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
	$famexml .= "<root>\r\n";

	$settings = array();
	$query = DB::query("SELECT variable, value FROM ".DB::table('mingrentangset')." WHERE variable IN ('fame_frmcolor', 'fame_frmcoloract', 'fame_txtcolor', 'fame_dftshape', 'fame_solidshape', 'fame_ucadd')");
	while($setting = DB::fetch($query)) {
		$settings[$setting['variable']] = $setting['value'];
	}

	$famexml .= "\t<config>\r\n";
	$famexml .= "\t\t<frameDefaultColor>".$settings['fame_frmcolor']."</frameDefaultColor>\r\n";
	$famexml .= "\t\t<frameActiveColor>".$settings['fame_frmcoloract']."</frameActiveColor>\r\n";
	$famexml .= "\t\t<textcolor>".$settings['fame_txtcolor']."</textcolor>\r\n";
	$famexml .= "\t\t<defaultShape>".$settings['fame_dftshape']."</defaultShape>\r\n";
	$famexml .= "\t\t<solidShape>".$settings['fame_solidshape']."</solidShape>\r\n";
	$famexml .= "\t</config>\r\n";

	$create_dir = DISCUZ_ROOT.'./data/cache/mrt_img/';
	if(!is_dir($create_dir)) {
		@mkdir($create_dir, 0777);
	}

	$query = DB::query("SELECT * FROM ".DB::table('mingrentang')." where gid='{$gid}'");
	if($query){
		while($fh = DB::fetch($query)) {
			$avatarurl = DISCUZ_ROOT.'./data/cache/mrt_img/'.basename($fh['uid'].'.jpg');
			if(!is_file($avatarurl) || (time() - filemtime($avatarurl)) > 604800){
				if((time() - filemtime($avatarurl)) > 604800) @unlink($avatarurl);
				$avatar = avatar($fh['uid'], 'middle', TRUE, False, TRUE);
				file_put_contents($avatarurl, file_get_contents($avatar));
			}
			$famexml .= "\n\t<record>\r\n";
			$charset = CHARSET;
			if($charset == 'gbk'){
				$famexml .= "\t\t<shortname>".iconv("gbk", "utf-8",strip_tags($fh['username']))."</shortname>\r\n";
				$famexml .= "\t\t<name><![CDATA[".iconv("gbk", "utf-8",$fh['username'])."]]></name>\r\n";
				$famexml .= "\t\t<intro><![CDATA[".iconv("gbk", "utf-8",strip_tags($fh['value']))."]]></intro>\r\n";
			}elseif($charset == 'utf-8'){
				$famexml .= "\t\t<shortname>".$fh['username']."</shortname>\r\n";
				$famexml .= "\t\t<name><![CDATA[".$fh['username']."]]></name>\r\n";
				$famexml .= "\t\t<intro><![CDATA[".$fh['value']."]]></intro>\r\n";
			}elseif($charset == 'big5'){
				$famexml .= "\t\t<shortname>".iconv("big5", "utf-8",strip_tags($fh['username']))."</shortname>\r\n";
				$famexml .= "\t\t<name><![CDATA[".iconv("big5", "utf-8",$fh['username'])."]]></name>\r\n";
				$famexml .= "\t\t<intro><![CDATA[".iconv("big5", "utf-8",strip_tags($fh['value']))."]]></intro>\r\n";
			}
			$famexml .= "\t\t<image>data/cache/mrt_img/".$fh['uid'].".jpg</image>\r\n";
			$famexml .= "\t\t<link><![CDATA[./home.php?mod=space&uid=".$fh['uid']."]]></link>\r\n";
			$online = DB::result_first("SELECT uid FROM ".DB::table('common_session')." WHERE uid = '$fh[uid]' AND invisible = 0");
			$famexml .= "\t\t<online>".($online ? 1 : 0)."</online>\r\n";
			$famexml .= "\t</record>\r\n";
		}
	} else {
		$famexml .= "\t<record />\r\n";
	}
	$famexml .= "\n</root>";
	$dir = DISCUZ_ROOT.'./data/cache/';
	if(!is_dir($dir)) @mkdir($dir, 0777);
	if($fp = @fopen($dir."cache_famehall_{$gid}.xml", "wb")) {
		fwrite($fp, $famexml);
		fclose($fp);
	} else {
		exit('Can not write to cache files, please check directory ./data/ and ./data/cache/ .');
	}
}
?>