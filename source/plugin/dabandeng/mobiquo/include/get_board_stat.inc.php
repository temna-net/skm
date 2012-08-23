<?php

defined('IN_MOBIQUO') or exit;

if(empty($_G['cookie']['onlineusernum'])) {
    $onlinenum = DB::result_first("SELECT count(*) FROM ".DB::table('common_session'));
} else {
    $onlinenum = intval($_G['cookie']['onlineusernum']);
}

$member_count = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('common_session')." WHERE uid>'0' AND invisible='0'"), 0);
$guestnum = max($onlinenum - $member_count, 0);

$totalmembers = $_G['cache']['userstats']['totalmembers'];
$posts = DB::result_first("SELECT sum(posts) FROM ".DB::table('forum_forum')." WHERE status='1' and type!='group'");
$threads = DB::result_first("SELECT sum(threads) FROM ".DB::table('forum_forum')." WHERE status='1' and type!='group'");