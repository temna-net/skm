<?php

defined('IN_MOBIQUO') or exit;

loadforum();

if(empty($_G['forum']['fid']) || $_G['forum']['status'] != 1) {
    get_error('forum_nonexistence');
}

if($_G['forum']['password']) {
    if($_G['gp_action'] == 'pwverify') {
        if($_G['gp_pw'] != $_G['forum']['password']) {
            showmessage('forum_passwd_incorrect', NULL);
        } else {
            dsetcookie('fidpw'.$_G['fid'], $_G['gp_pw']);
        }
    }
}