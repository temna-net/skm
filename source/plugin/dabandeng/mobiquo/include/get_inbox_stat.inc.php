<?php
defined('IN_MOBIQUO') or exit;

if(!$_G['uid']) {
    get_error('to_login');
}
loaducenter();
require_once UC_ROOT.'lib/db.class.php';
$filter = in_array($_GET['filter'], array('newpm', 'privatepm', 'announcepm')) ? $_GET['filter'] : 'privatepm';
$perpage = 15;
$perpage = 10;
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
	$list = $result['data'];
}

$newpmnum = array();
foreach($list as $value)
{
    $newpmnum[] = $value['isnew']; 
}

$unreadnum = array_sum($newpmnum);