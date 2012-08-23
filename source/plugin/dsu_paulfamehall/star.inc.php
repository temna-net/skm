<?php
/*
	dsu_paulfamehall_VIEW By shy9000 2011-06-08
*/
!defined('IN_DISCUZ') && exit('Access Denied');
require_once DISCUZ_ROOT.'./source/plugin/dsu_paulfamehall/fame.func.php';
loadcache('pluginlanguage_script');
$lang = $_G['cache']['pluginlanguage_script']['dsu_paulfamehall'];
$navigation = "{$lang['php_01']}";
$navtitle = "$navigation";
$famever = '6.0';
$fameBuild = 'E0608';
$fameadd = 'http://www.dsu.cc/thread-75335-1-1.html';
$fameflash = 'source/plugin/dsu_paulfamehall/star.swf';
function shy9000_famehall_updateCache() {
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
if($_G['gp_group'] == 'add'){
	if($_G['adminid'] != '1')showmessage("{$lang['php_02']}","plugin.php?id=dsu_paulfamehall:star");
	if($_G['gp_formhash'] != FORMHASH)showmessage('undefined_action', NULL);
		$groupdnew1 = trim($_G['gp_groupdnew']);
		$groupnew1 = trim($_G['gp_groupnew']);
		$grouppicnew1 = trim($_G['gp_grouppicnew']);
		$security = DB::fetch_first("SELECT * FROM ".DB::table('mingrentanggroup')." where groupname='$groupnew1'");
		if($security)showmessage("{$lang['php_05']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=addgroup");
		DB::query("INSERT INTO ".DB::table('mingrentanggroup')." (id, groupname, groupd, grouppic) VALUES ('NULL', '$groupnew1', '$groupdnew1', '$grouppicnew1')");
		$query = DB::query("SELECT * FROM ".DB::table('mingrentanggroup')." ORDER BY groupd");
		while($gx = DB::fetch($query)) {
			SILVER_writeFameHallXML($gx[id]);
			shy9000_famehall_updateCache();
			$gxs[] = $gx;
		}
		showmessage("{$lang['php_06']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=edituser");
} elseif($_G['gp_group'] == 'edit') {
	if($_G['adminid'] != '1')showmessage("{$lang['php_02']}","plugin.php?id=dsu_paulfamehall:star");
	if($_G['gp_formhash'] != FORMHASH)showmessage('undefined_action', NULL);
	$mrtgid=intval($_G['gp_mrtgid']);
	$editvalue=$_G['gp_editvalue'];
	$editvalue1=$_G['gp_editvalue1'];
	$editvalue2=$_G['gp_editvalue2'];
	$iteminfo=DB::fetch_first("SELECT * FROM ".DB::table('mingrentanggroup')." where id='$mrtgid'");
	if(!$iteminfo){
		showmessage("{$lang['php_03']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=edituser");
	}else{
		DB::query("UPDATE ".DB::table('mingrentanggroup')." SET groupname='$editvalue',groupd='$editvalue1',grouppic='$editvalue2' WHERE id='$mrtgid'");
		$query = DB::query("SELECT * FROM ".DB::table('mingrentanggroup')." ORDER BY groupd");
		while($gx = DB::fetch($query)) {
			SILVER_writeFameHallXML($gx[id]);
			shy9000_famehall_updateCache();
			$gxs[] = $gx;
		}
		showmessage("{$lang['php_04']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=edituser");
	}
} elseif($_G['gp_group'] == 'del') {
	if($_G['adminid'] != '1')showmessage("{$lang['php_02']}","plugin.php?id=dsu_paulfamehall:star");
	if($_G['gp_formhash'] != FORMHASH)showmessage('undefined_action', NULL);
	$mrtgid=intval($_G['gp_mrtgid']);
	$iteminfo=DB::fetch_first("SELECT * FROM ".DB::table('mingrentanggroup')." where id='$mrtgid'");
	if(!$iteminfo){
		showmessage("{$lang['php_03']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=edituser");
	}else{
		DB::query("DELETE FROM ".DB::table('mingrentanggroup')." WHERE id='$mrtgid'");
		DB::query("DELETE FROM ".DB::table('mingrentang')." WHERE gid='$mrtgid'");
		@unlink(DISCUZ_ROOT."./forumdata/cache/cache_famehall_{$mrtgid}.xml");
		$query = DB::query("SELECT * FROM ".DB::table('mingrentanggroup')." ORDER BY groupd");
		while($gx = DB::fetch($query)) {
			SILVER_writeFameHallXML($gx[id]);
			shy9000_famehall_updateCache();
			$gxs[] = $gx;
		}
		showmessage("{$lang['php_07']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=edituser");
	}
}
if($_G['gp_user'] == 'add'){
	if($_G['adminid'] != '1')showmessage("{$lang['php_02']}","plugin.php?id=dsu_paulfamehall:star");
	if($_G['gp_formhash'] != FORMHASH)showmessage('undefined_action', NULL);
		$usernamenew1 = trim($_G['gp_usernamenew']);
		$uservaluenew1 = trim($_G['gp_uservaluenew']);
		$groupsnew = $_G['gp_groupsnew'];
		if($groupsnew == '0')showmessage("{$lang['php_08']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=addgroup");
		$security1 = DB::fetch_first("SELECT uid FROM ".DB::table('common_member')." where username='$usernamenew1'");
		if(!$security1)showmessage("{$lang['php_09']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=adduser");
		$security2 = DB::fetch_first("SELECT * FROM ".DB::table('mingrentang')." where username='$usernamenew1'");
		if($security2)showmessage("{$lang['php_10']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=adduser");
		DB::query("INSERT INTO ".DB::table('mingrentang')." (uid, username, value, dateline, gid) VALUES ('$security1[uid]', '$usernamenew1', '$uservaluenew1', '$_G[timestamp]', '$groupsnew')");
		$query = DB::query("SELECT * FROM ".DB::table('mingrentanggroup')." ORDER BY groupd");
		while($gx = DB::fetch($query)) {
			SILVER_writeFameHallXML($gx[id]);
			shy9000_famehall_updateCache();
			$gxs[] = $gx;
		}
		showmessage("{$lang['php_11']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=edituser");
} elseif($_G['gp_user'] == 'edit') {
	if($_G['adminid'] != '1')showmessage("{$lang['php_02']}","plugin.php?id=dsu_paulfamehall:star");
	if($_G['gp_formhash'] != FORMHASH)showmessage('undefined_action', NULL);
	$useruid=intval($_G['gp_useruid']);
	$editvalue=$_G['gp_editvalue'];
	$iteminfo=DB::fetch_first("SELECT * FROM ".DB::table('mingrentang')." where uid='$useruid'");
	if(!$iteminfo){
		showmessage("{$lang['php_03']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=edituser");
	}else{
		DB::query("UPDATE ".DB::table('mingrentang')." SET value='$editvalue' WHERE uid='$useruid'");
		$query = DB::query("SELECT * FROM ".DB::table('mingrentanggroup')." ORDER BY groupd");
		while($gx = DB::fetch($query)) {
			SILVER_writeFameHallXML($gx[id]);
			shy9000_famehall_updateCache();
			$gxs[] = $gx;
		}
		showmessage("{$lang['php_04']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=edituser");
	}
} elseif($_G['gp_user'] == 'pass') {
	if($_G['adminid'] != '1')showmessage("{$lang['php_02']}","plugin.php?id=dsu_paulfamehall:star");
	if($_G['gp_formhash'] != FORMHASH)showmessage('undefined_action', NULL);
	$useruid=intval($_G['gp_useruid']);
	$about=$_G['gp_about'];
	$name=$_G['gp_name'];
	$gidu=$_G['gp_gidu'];
	$iteminfo=DB::fetch_first("SELECT * FROM ".DB::table('mingrentangreg')." where userid='$useruid'");
	if(!$iteminfo){
		showmessage("{$lang['php_03']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=regedit");
	}else{
		DB::query("INSERT INTO ".DB::table('mingrentang')." (uid, username, value, dateline, gid) VALUES ('$useruid', '$name', '$about', '$_G[timestamp]', '$gidu')");
		DB::query("DELETE FROM ".DB::table('mingrentangreg')." WHERE userid='$useruid'");
		sendpm($useruid, "{$lang['php_12']}", "{$lang['php_13']}");
		$query = DB::query("SELECT * FROM ".DB::table('mingrentanggroup')." ORDER BY groupd");
		while($gx = DB::fetch($query)) {
			SILVER_writeFameHallXML($gx[id]);
			shy9000_famehall_updateCache();
			$gxs[] = $gx;
		}
		showmessage("{$lang['php_14']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=regedit");
	}
} elseif($_G['gp_user'] == 'nopass') {
	if($_G['adminid'] != '1')showmessage("{$lang['php_02']}","plugin.php?id=dsu_paulfamehall:star");
	if($_G['gp_formhash'] != FORMHASH)showmessage('undefined_action', NULL);
	$useruid=intval($_G['gp_useruid']);
	$iteminfo=DB::fetch_first("SELECT * FROM ".DB::table('mingrentangreg')." where userid='$useruid'");
	if(!$iteminfo){
		showmessage("{$lang['php_03']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=regedit");
	}else{
		DB::query("DELETE FROM ".DB::table('mingrentangreg')." WHERE userid='$useruid'");
		sendpm($useruid, "{$lang['php_15']}", "{$lang['php_16']}");
		showmessage("{$lang['php_17']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=regedit");
	}
} elseif($_G['gp_user'] == 'del') {
	if($_G['adminid'] != '1')showmessage("{$lang['php_02']}","plugin.php?id=dsu_paulfamehall:star");
	if($_G['gp_formhash'] != FORMHASH)showmessage('undefined_action', NULL);
	$useruid=intval($_G['gp_useruid']);
	$iteminfo=DB::fetch_first("SELECT * FROM ".DB::table('mingrentang')." where uid='$useruid'");
	if(!$iteminfo){
		showmessage("{$lang['php_03']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=edituser");
	}else{
		DB::query("DELETE FROM ".DB::table('mingrentang')." WHERE uid='$useruid'");
		@unlink(DISCUZ_ROOT.'./data/cache/mrt_img/'.$useruid.'.jpg');
		$query = DB::query("SELECT * FROM ".DB::table('mingrentanggroup')." ORDER BY groupd");
		while($gx = DB::fetch($query)) {
			SILVER_writeFameHallXML($gx[id]);
			shy9000_famehall_updateCache();
			$gxs[] = $gx;
		}
		showmessage("{$lang['php_07']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=edituser");
	}
}
if($_G['gp_set'] == 'color'){
	if($_G['adminid'] != '1')showmessage("{$lang['php_02']}","plugin.php?id=dsu_paulfamehall:star");
	if($_G['gp_formhash'] != FORMHASH)showmessage('undefined_action', NULL);
	$newvalue1=$_G['gp_newvalue1'];
	$newvalue2=$_G['gp_newvalue2'];
	$newvalue3=$_G['gp_newvalue3'];
	$newvalue4=$_G['gp_newvalue4'];
	$newvalue5=$_G['gp_newvalue5'];
	$newvalue6=$_G['gp_newvalue6'];
	DB::query("UPDATE ".DB::table('mingrentangset')." SET value = '$newvalue1' WHERE variable = 'fame_frmcolor' LIMIT 1 ;");
	DB::query("UPDATE ".DB::table('mingrentangset')." SET value = '$newvalue2' WHERE variable = 'fame_frmcoloract' LIMIT 1 ;");
	DB::query("UPDATE ".DB::table('mingrentangset')." SET value = '$newvalue3' WHERE variable = 'fame_txtcolor' LIMIT 1 ;");
	DB::query("UPDATE ".DB::table('mingrentangset')." SET value = '$newvalue4' WHERE variable = 'fame_dftshape' LIMIT 1 ;");
	DB::query("UPDATE ".DB::table('mingrentangset')." SET value = '$newvalue5' WHERE variable = 'fame_solidshape' LIMIT 1 ;");
	DB::query("UPDATE ".DB::table('mingrentangset')." SET value = '$newvalue6' WHERE variable = 'fame_flashbg' LIMIT 1 ;");
	$query = DB::query("SELECT * FROM ".DB::table('mingrentanggroup')." ORDER BY groupd");
	while($gx = DB::fetch($query)) {
		SILVER_writeFameHallXML($gx[id]);
		shy9000_famehall_updateCache();
		$gxs[] = $gx;
	}
	showmessage("{$lang['php_18']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=color");
} elseif($_G['gp_set'] == 'update') {
	if($_G['adminid'] != '1')showmessage("{$lang['php_02']}","plugin.php?id=dsu_paulfamehall:star");
	if($_G['gp_formhash'] != FORMHASH)showmessage('undefined_action', NULL);
	$query = DB::query("SELECT * FROM ".DB::table('mingrentanggroup')." ORDER BY groupd");
	while($gx = DB::fetch($query)) {
		SILVER_writeFameHallXML($gx[id]);
		shy9000_famehall_updateCache();
		$gxs[] = $gx;
	}
	showmessage("{$lang['php_18']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=box");
} elseif($_G['gp_set'] == 'box') {
	if($_G['adminid'] != '1')showmessage("{$lang['php_02']}","plugin.php?id=dsu_paulfamehall:star");
	if($_G['gp_formhash'] != FORMHASH)showmessage('undefined_action', NULL);
	$openfamereg=$_G['gp_openfamereg'];
	DB::query("UPDATE ".DB::table('mingrentangset')." SET value = '$openfamereg' WHERE variable = 'fame_openfamereg' LIMIT 1 ;");
	DB::query("UPDATE ".DB::table('mingrentangset')." SET value = '$_G[gp_blackname]' WHERE variable = 'fame_blackname' LIMIT 1 ;");
	DB::query("UPDATE ".DB::table('mingrentangset')." SET value = '$_G[gp_minday]' WHERE variable = 'fame_minday' LIMIT 1 ;");
	DB::query("UPDATE ".DB::table('mingrentangset')." SET value = '$_G[gp_mincredit]' WHERE variable = 'fame_mincredit' LIMIT 1 ;");
	DB::query("UPDATE ".DB::table('mingrentangset')." SET value = '$_G[gp_minposts]' WHERE variable = 'fame_minposts' LIMIT 1 ;");
	DB::query("UPDATE ".DB::table('mingrentangset')." SET value = '$_G[gp_maxtext]' WHERE variable = 'fame_maxtext' LIMIT 1 ;");
	DB::query("UPDATE ".DB::table('mingrentangset')." SET value = '$_G[gp_kcrx]' WHERE variable = 'fame_kcrx' LIMIT 1 ;");
	DB::query("UPDATE ".DB::table('mingrentangset')." SET value = '$_G[gp_kcrz]' WHERE variable = 'fame_kcrz' LIMIT 1 ;");
	$query = DB::query("SELECT * FROM ".DB::table('mingrentanggroup')." ORDER BY groupd");
	while($gx = DB::fetch($query)) {
		SILVER_writeFameHallXML($gx[id]);
		shy9000_famehall_updateCache();
		$gxs[] = $gx;
	}
	showmessage("{$lang['php_19']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=box");
} elseif($_G['gp_set'] == 'del') {
	if($_G['adminid'] != '1')showmessage("{$lang['php_02']}","plugin.php?id=dsu_paulfamehall:star");
	if($_G['gp_formhash'] != FORMHASH)showmessage('undefined_action', NULL);
	DB::query("TRUNCATE TABLE ".DB::table('mingrentang')."");
	@rmdir(DISCUZ_ROOT.'./data/cache/mrt_img/');
	$query = DB::query("SELECT * FROM ".DB::table('mingrentanggroup')." ORDER BY groupd");
	while($gx = DB::fetch($query)) {
		SILVER_writeFameHallXML($gx[id]);
		shy9000_famehall_updateCache();
		$gxs[] = $gx;
	}
	showmessage("{$lang['php_20']}","plugin.php?id=dsu_paulfamehall:star&action=adminop&operation=box");
}
if($_G['gp_action'] == ''){
	$query = DB::query("SELECT variable, value FROM ".DB::table('mingrentangset')." WHERE variable IN ('fame_openfamereg', 'fame_flashbg')");
	while($setting = DB::fetch($query)) {
		$settings[$setting['variable']] = $setting['value'];
	}
	$query = DB::query("SELECT id, groupname, groupd FROM ".DB::table('mingrentanggroup')." ORDER BY groupd");
	while($mingrentang = DB::fetch($query)) {
		$mingrentangs[] = $mingrentang;
	}
	$gid = $_G['gp_gid'];
	if(!$gid){
		$query = DB::query("SELECT id, groupname, groupd FROM ".DB::table('mingrentanggroup')." ORDER BY groupd LIMIT 0, 1");
		while($trc = DB::fetch($query)) {
			$trcs[] = $trc;
		}
	} else {
		$giddb = DB::fetch_first("SELECT * FROM ".DB::table('mingrentanggroup')." WHERE id='$gid'");
	}
	$randid = random(3);
	include template('dsu_paulfamehall:star');
} elseif($_G['gp_action'] == 'adminop') {
	if($_G['adminid'] != '1')showmessage("{$lang['php_02']}","plugin.php?id=dsu_paulfamehall:star");
	if($_G['gp_operation'] == 'edituser'){
	$mrtgid = $_G['gp_mrtgid'];
	if($mrtgid == ''){
		$query = DB::query("SELECT id, groupname, grouppic, groupd FROM ".DB::table('mingrentanggroup')." ORDER BY groupd");
		while($mingrentang = DB::fetch($query)) {
			$mingrentangs[] = $mingrentang;
		}
	} else {
		$giddb = DB::fetch_first("SELECT groupname FROM ".DB::table('mingrentanggroup')." where id='$mrtgid'");
		$query = DB::query("SELECT uid,username,value,dateline,gid FROM ".DB::table('mingrentang')." where gid='$mrtgid'");
		while($mingrentang = DB::fetch($query)) {
			$mingrentang['dateline'] = dgmdate($mingrentang['dateline'], 'Y-m-d H:i');
			$mingrentangs[] = $mingrentang;
		}
	}
	}elseif($_G['gp_operation'] == 'regedit'){
		$query = DB::query("SELECT userid,username,dateline,about,posts,credits,regdate,gid FROM ".DB::table('mingrentangreg')."");
		while($mingrentang = DB::fetch($query)) {
			$mingrentang['dateline'] = dgmdate($mingrentang['dateline'], 'Y-m-d H:i');
			$mingrentang['regdate'] = dgmdate($mingrentang['regdate'], 'Y-m-d H:i');
			$mingrentang['groupn'] = DB::fetch_first("SELECT * FROM ".DB::table('mingrentanggroup')." WHERE id='{$mingrentang['gid']}'");
			$mingrentang['groupn'] = $mingrentang['groupn']['groupname'];
			$mingrentangs[] = $mingrentang;
		}
		$query2 = DB::query("SELECT id, groupname, groupd FROM ".DB::table('mingrentanggroup')." ORDER BY groupd");
		while($rr = DB::fetch($query2)) {
			$rrs[] = $rr;
		}
	}elseif($_G['gp_operation'] == 'adduser'){
		$query = DB::query("SELECT id, groupname, groupd FROM ".DB::table('mingrentanggroup')." ORDER BY groupd");
		while($mingrentang = DB::fetch($query)) {
			$mingrentangs[] = $mingrentang;
		}
	}elseif($_G['gp_operation'] == 'box'){
	$query = DB::query("SELECT variable, value FROM ".DB::table('mingrentangset')." WHERE variable IN ('fame_openmember', 'fame_openfamereg', 'fame_blackname', 'fame_minday', 'fame_mincredit', 'fame_minposts', 'fame_maxtext', 'fame_kcrx', 'fame_kcrz')");
	while($setting = DB::fetch($query)) {
		$settings[$setting['variable']] = $setting['value'];
	}
	}elseif($_G['gp_operation'] == 'color'){
	$query = DB::query("SELECT variable, value FROM ".DB::table('mingrentangset')." WHERE variable IN ('fame_frmcolor', 'fame_frmcoloract', 'fame_txtcolor', 'fame_dftshape', 'fame_solidshape', 'fame_flashbg')");
	while($setting = DB::fetch($query)) {
		$settings[$setting['variable']] = $setting['value'];
	}
	}
	include template('dsu_paulfamehall:star_admin');
} elseif($_G['gp_action'] == 'famereg') {
	if(!$_G['uid']) showmessage('not_loggedin', 'logging.php?action=login');
	$query = DB::query("SELECT variable, value FROM ".DB::table('mingrentangset')." WHERE variable IN ('fame_openfamereg', 'fame_blackname', 'fame_minday', 'fame_mincredit', 'fame_minposts', 'fame_maxtext', 'fame_kcrx', 'fame_kcrz')");
	while($setting = DB::fetch($query)) {
		$settings[$setting['variable']] = $setting['value'];
	}
	$blackname = $settings['fame_blackname'];
	$blackname = explode(",", $blackname);
	$blackname = is_array($blackname) ? $blackname : array();
	$ch_credit = trim($settings['fame_mincredit']);
	$maxabout = trim($settings['fame_maxtext']);
	$maxregdate = trim($settings['fame_minday']);
	$maxposts = trim($settings['fame_minposts']);
	$kcrx = trim($settings['fame_kcrx']);
	$kcrz = trim($settings['fame_kcrz']);
	$post = DB::fetch_first("SELECT p.*,x.* FROM ".DB::table('common_member_count')." p LEFT JOIN ".DB::table('common_member')." x on x.uid=p.uid WHERE p.uid='$_G[uid]'");
	$regdate = intval(($_G['timestamp'] - $post['regdate'])/(3600*24));
	$query = DB::query("SELECT id, groupname, groupd FROM ".DB::table('mingrentanggroup')." ORDER BY groupd");
	while($mingrentang = DB::fetch($query)) {
		$mingrentangs[] = $mingrentang;
	}
	if($_G['gp_reg']== '') {
		$security = DB::fetch_first("SELECT * FROM ".DB::table('mingrentangreg')." where userid='$_G[uid]'");
		if($security)showmessage("{$lang['php_21']}","plugin.php?id=dsu_paulfamehall:star");
		$security2 = DB::fetch_first("SELECT * FROM ".DB::table('mingrentang')." where uid='$_G[uid]'");
		if($security2)showmessage("{$lang['php_22']}","plugin.php?id=dsu_paulfamehall:star");
	} elseif($_G['gp_reg'] == 'thesecondstep') {
		if($maxposts > $post[posts])showmessage("{$lang['php_23']} {$maxposts} {$lang['php_24']}","plugin.php?id=dsu_paulfamehall:star");
		if($maxregdate > $regdate)showmessage("{$lang['php_25']}","plugin.php?id=dsu_paulfamehall:star");
		if($ch_credit > $post[credits])showmessage("{$lang['php_26']}","plugin.php?id=dsu_paulfamehall:star");
		if($post[extcredits.$kcrx] < $kcrz && $kcrz != '0')showmessage("{$lang['sss_01']}","plugin.php?id=dsu_paulfamehall:star");
		$security = DB::fetch_first("SELECT * FROM ".DB::table('mingrentangreg')." where userid='$_G[uid]'");
		if($security)showmessage("{$lang['php_27']}","plugin.php?id=dsu_paulfamehall:star");
		$security2 = DB::fetch_first("SELECT * FROM ".DB::table('mingrentang')." where uid='$_G[uid]'");
		if($security2)showmessage("{$lang['php_28']}","plugin.php?id=dsu_paulfamehall:star");
	} elseif($_G['gp_reg'] == 'regnow') {
		if($_G['gp_formhash'] != FORMHASH)showmessage('undefined_action', NULL);
		if($_G['gp_about']=='')showmessage("{$lang['php_29']}");
		$groupsnew = $_G['gp_groupsnew'];
		if($groupsnew == '0')showmessage("{$lang['php_30']}","plugin.php?id=dsu_paulfamehall:star");
		if(strlen($_G['gp_about']) > $maxabout)showmessage("{$lang['php_31']} {$maxabout} {$lang['php_32']}");
		$security = DB::fetch_first("SELECT * FROM ".DB::table('mingrentangreg')." where userid='$_G[uid]'");
		if($security)showmessage("{$lang['php_33']}","plugin.php?id=dsu_paulfamehall:star");
		$security2 = DB::fetch_first("SELECT * FROM ".DB::table('mingrentang')." where uid='$_G[uid]'");
		if($security2)showmessage("{$lang['php_34']}","plugin.php?id=dsu_paulfamehall:star");
		if($post[extcredits.$kcrx] < $kcrz && $kcrz != '0')showmessage("{$lang['sss_01']}","plugin.php?id=dsu_paulfamehall:star");
		if($kcrz != '0')DB::query("UPDATE ".DB::table('common_member_count')." SET extcredits{$kcrx}=extcredits{$kcrx}-{$kcrz} WHERE uid='$_G[uid]'");
		$about = dhtmlspecialchars($_G['gp_about']);
		$about = preg_replace("/\n/is", "<br>", $about);
		DB::query("INSERT into ".DB::table('mingrentangreg')." (appid,userid,username,dateline,about,credits,regdate,posts,gid) VALUES ('NULL','$_G[uid]','$_G[username]','$_G[timestamp]','$about','{$post[credits]}','{$post[regdate]}','{$post[posts]}','$groupsnew')");
		showmessage("{$lang['php_35']}","plugin.php?id=dsu_paulfamehall:star");
	}
	if(in_array($_G['uid'], $blackname))showmessage("{$lang['php_36']}","plugin.php?id=dsu_paulfamehall:star");
	include template('dsu_paulfamehall:star_reg');
}
?>