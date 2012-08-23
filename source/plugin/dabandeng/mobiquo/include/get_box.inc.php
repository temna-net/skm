<?php

defined('IN_MOBIQUO') or exit;
$boxid = $_GET['boxid'];
loaducenter();
require_once UC_ROOT.'lib/db.class.php';
$filter = in_array($_GET['filter'], array('newpm', 'privatepm', 'announcepm')) ? $_GET['filter'] : 'privatepm';
$perpage = 15;
$perpage = mob_perpage($perpage);

$page = empty($_GET['page'])?0:intval($_GET['page']);
if($page<1) $page = 1;

$grouppms = $gpmids = $gpmstatus = array();
$newpm = 0;

if($filter == 'privatepm' && $page == 1 || $filter == 'announcepm') {

	$status = $filter == 'announcepm' ? "`status`>='0'" : "`status`='0'";
	$query = DB::query("SELECT gpmid, status FROM ".DB::table("common_member_grouppm")." WHERE uid='$_G[uid]' AND $status");
	while($gpuser = DB::fetch($query)) {
		$gpmids[] = $gpuser['gpmid'];
		$gpmstatus[$gpuser['gpmid']] = $gpuser['status'];
	}
	if($gpmids) {
		$query = DB::query("SELECT * FROM ".DB::table("common_grouppm")." WHERE id IN (".dimplode($gpmids).") ORDER BY id DESC");
		while($grouppm = DB::fetch($query)) {
			$grouppm['message'] = cutstr(strip_tags($grouppm['message']), 100, '');
			$grouppms[] = $grouppm;
		}
	}
}

if($filter == 'privatepm' || $filter == 'newpm') {
	$result = uc_pm_list($_G['uid'], $page, $perpage, 'inbox', $filter, 200);
	$count = $result['count'];
	$arr = $result['data'];
}

$uid = empty($_GET['uid']) ? 0 : intval($_GET['uid']);
if($_GET['username']) {
	$member = DB::fetch_first("SELECT uid FROM ".DB::table('common_member')." WHERE username='$_GET[username]' LIMIT 1");
	if(empty($member)) {
		showmessage('space_does_not_exist');
	}
	$uid = $member['uid'];
}

$dos = array('index', 'doing', 'blog', 'album', 'friend', 'wall',
	'notice', 'share', 'home', 'pm', 'videophoto', 'favorite',
	'thread', 'trade', 'poll', 'activity', 'debate', 'reward', 'profile', 'plugin');

$do = (!empty($_GET['do']) && in_array($_GET['do'], $dos))?$_GET['do']:'index';
if($do == 'index' && ($_G['inajax'] || !$_G['setting']['homestatus'])) {
	$do = 'profile';
}

if(in_array($do, array('home', 'doing', 'blog', 'album', 'share', 'wall'))) {
	if(!$_G['setting']['homestatus']) {
		showmessage('home_status_off');
	}
} else {
	$_G['mnid'] = 'mn_common';
}

if(empty($uid) || in_array($do, array('notice', 'pm'))) $uid = $_G['uid'];
if($uid) {
	$space = getspace($uid);
	if(empty($space)) {
		showmessage('space_does_not_exist');
	}
}

if(empty($space)) {
	if(in_array($do, array('doing', 'blog', 'album', 'share', 'home', 'thread', 'trade', 'poll', 'activity', 'debate', 'reward', 'group'))) {
		$_GET['view'] = 'all';
		$space['uid'] = 0;
	} else {
		showmessage('login_before_enter_home', null, array(), array('showmsg' => true, 'login' => 1));
	}
} else {
	$navtitle = $space['username'];
	if($space['status'] == -1 && $_G['adminid'] != 1) {
		showmessage('space_has_been_locked');
	}

	if(in_array($space['groupid'], array(4, 5, 6)) && ($_G['adminid'] != 1 && $space['uid'] != $_G['uid'])) {
		$_GET['do'] = $do = 'profile';
	}

	if($do != 'profile' && $do != 'index' && !ckprivacy($do, 'view')) {
		$_G['privacy'] = 1;
		require_once libfile('space/profile', 'include');
		include template('home/space_privacy');
		exit();
	}
	if(!$space['self'] && $_GET['view'] != 'eccredit') $_GET['view'] = 'me';

	get_my_userapp();

	get_my_app();
}

$diymode = 0;

$seccodecheck = $_G['setting']['seccodestatus'] & 4;
$secqaacheck = $_G['setting']['secqaa']['status'] & 2;
//require_once libfile('space/'.$do, 'include');





loaducenter();

$list = array();

$plid = empty($_GET['plid'])?0:intval($_GET['plid']);
$daterange = empty($_GET['daterange'])?0:intval($_GET['daterange']);
$touid = empty($_GET['touid'])?0:intval($_GET['touid']);
$opactives['pm'] = 'class="a"';


$type = $_GET['type'];
$page = empty($_GET['page']) ? 0 : intval($_GET['page']);

$chatpmmember = intval($_GET['chatpmmember']);
$chatpmmemberlist = array();




$b = array();
foreach($arr as $value)
{
   if($value['touid']) {
	$ols = array();
	if(defined('IN_MOBILE')) {
		$perpage = 5;
	} else {
		$perpage = 10;
	}
	$perpage = mob_perpage($perpage);
	if(!$daterange) {
		$tousername = DB::result_first("SELECT username FROM ".DB::table('common_member')." WHERE uid='".$value['touid']."'");
		$count = uc_pm_view_num($_G['uid'], $touid, 0);
		if(!$page) {
			$page = ceil($count/$perpage);
		}
		$b[] = uc_pm_view($_G['uid'], 0, $value['touid'], 5, ceil($count/$perpage)-$page+1, $perpage, 0, 0);
	} else {
		showmessage('parameters_error');
	}
}
   
   
   
}


$box = array();

foreach($b as $key=>$value)
{
    foreach($value as $k=>$v)
    {
        $box[] = $v;   
    } 
}
$inbox_array = array();
$outbox_array = array();

foreach($box as $key=>$value)
{
    if($box[$key]['authorid'] == $_G['uid'])
    {
       $outbox_array[] = $box[$key];
    }else
    {
       $inbox_array[] = $box[$key];   
    }
}

if($boxid == 'inbox')
{
   $pmlist = $inbox_array;
    foreach($pmlist as $key=>$value)
    {
       $pmlist[$key]['icon_url'] = get_user_avatar($value['msgfromid']);
       $pmlist[$key]['msg_from']['username'] = $value['msgfrom'];
    }
}else
{
   $pmlist = $outbox_array;
   foreach($pmlist as $key=>$value)
   {
         $pmlist[$key]['msg_from'] = DB::fetch(DB::query("SELECT username FROM ".UC_DBTABLEPRE."members WHERE uid=".$value['touid']));
         $pmlist[$key]['icon_url'] = get_user_avatar($value['touid']);
   }    
}