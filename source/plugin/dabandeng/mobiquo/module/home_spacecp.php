<?php

defined('IN_MOBIQUO') or exit;

require_once libfile('function/spacecp');
require_once libfile('function/magic');

$acs = array('space', 'doing', 'upload', 'comment', 'blog', 'album', 'relatekw', 'common', 'class',
    'swfupload', 'poke', 'friend', 'eccredit', 'favorite',
    'avatar', 'profile', 'theme', 'feed', 'privacy', 'pm', 'share', 'invite','sendmail',
    'credit', 'usergroup', 'domain', 'click','magic', 'top', 'videophoto', 'index', 'plugin', 'search');

$ac = (empty($_GET['ac']) || !in_array($_GET['ac'], $acs))?'profile':$_GET['ac'];
$op = empty($_GET['op'])?'':$_GET['op'];

if(empty($_G['uid'])) {
    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        dsetcookie('_refer', rawurlencode($_SERVER['REQUEST_URI']));
    } else {
        dsetcookie('_refer', rawurlencode('home.php?mod=spacecp&ac='.$ac));
    }
    showmessage('to_login', '', array(), array('showmsg' => true, 'login' => 1));
}

$space = getspace($_G['uid']);
if(empty($space)) {
    showmessage('space_does_not_exist');
}
space_merge($space, 'field_home');

if(($space['status'] == -1 || in_array($space['groupid'], array(4, 5, 6))) && $ac != 'usergroup') {
    showmessage('space_has_been_locked');
}

$actives = array($ac => ' class="a"');

$seccodecheck = $_G['group']['seccode'] ? $_G['setting']['seccodestatus'] & 4 : 0;
$secqaacheck = $_G['group']['seccode'] ? $_G['setting']['secqaa']['status'] & 2 : 0;
