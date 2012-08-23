<?php

defined('IN_MOBIQUO') or exit;

$space = getspace($_G['uid']);

$wherearr = $list = array();
$wherearr[] = "hf.uid='$_G[uid]'";
$wherearr[] = "hf.idtype='fid'";
$wheresql = implode(' AND ', $wherearr);

$count = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('home_favorite')." hf WHERE $wheresql"),0);
$list = array();
if($count) {
    $query = DB::query("SELECT hf.id, hf.title, fff.icon, fff.password, ff.type, ff.simple, ff.todayposts
        FROM ".DB::table('home_favorite')." hf
        LEFT JOIN ".DB::table('forum_forum')." ff ON hf.id=ff.fid
        LEFT JOIN ".DB::table('forum_forumfield')." fff ON hf.id=fff.fid
        WHERE $wheresql
        ORDER BY hf.dateline DESC
        LIMIT 0, 20");
    while ($value = DB::fetch($query)) {
        $list[] = $value;
    }
}
