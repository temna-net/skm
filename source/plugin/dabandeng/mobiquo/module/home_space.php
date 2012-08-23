<?php

defined('IN_MOBIQUO') or exit;

$uid = empty($_GET['uid']) ? 0 : intval($_GET['uid']);


if($_GET['username']) {
    $username = to_local($_GET['username']);
    $query = DB::query('SELECT uid,username FROM '.DB::table('common_member')." WHERE username = (".dimplode($username).')');
    $member = DB::fetch($query);
    if(empty($member)) {
        showmessage('space_does_not_exist');
    }   
    $uid = $member['uid'];
}

$dos = array('index', 'doing', 'blog', 'album', 'friend', 'wall',
    'notice', 'share', 'home', 'pm', 'videophoto', 'favorite',
    'thread', 'trade', 'poll', 'activity', 'debate', 'reward', 'profile');

$do = (!empty($_GET['do']) && in_array($_GET['do'], $dos))?$_GET['do']:'index';
if($do == 'index' && $_G['inajax']) {
    $do = 'profile';
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

    if($do != 'profile' && !ckprivacy($do, 'view')) {
        showmessage(lang('home/template', 'set_privacy', array('$space[username]' => $space['username'])));
    }

    if(!$space['self'] && $_GET['view'] != 'eccredit') $_GET['view'] = 'me';

    get_my_userapp();

    get_my_app();
}

$diymode = 0;

$seccodecheck = $_G['setting']['seccodestatus'] & 4;
$secqaacheck = $_G['setting']['secqaa']['status'] & 2;