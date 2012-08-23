<?php

defined('IN_MOBIQUO') or exit;

$space = getspace($_G['uid']);

$id = empty($_GET['id'])?0:intval($_GET['id']);

$perpage = $limit;

ckstart($start, $perpage);

$idtypes = array('thread'=>'tid', 'forum'=>'fid', 'blog'=>'blogid', 'group'=>'gid', 'album'=>'albumid', 'space'=>'uid', 'article'=>'aid');
$_GET['type'] = isset($idtypes[$_GET['type']]) ? $_GET['type'] : 'all';
$actives = array($_GET['type'] => ' class="a"');

$gets = array(
    'mod' => 'space',
    'uid' => $space['uid'],
    'do' => 'favorite',
    'view' => 'me',
    'type' => $_GET['type'],
    'from' => $_GET['from']
);
$theurl = 'home.php?'.url_implode($gets);


$wherearr = $list = array();
$favid = empty($_GET['favid'])?0:intval($_GET['favid']);
if($favid) {
    $wherearr[] = "hf.favid='$favid'";
}
$wherearr[] = "hf.uid='$_G[uid]'";
$idtype = isset($idtypes[$_GET['type']]) ? $idtypes[$_GET['type']] : '';
if($idtype) {
    $wherearr[] = "hf.idtype='$idtype'";
}
$wheresql = implode(' AND ', $wherearr);

$count = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('home_favorite')." hf WHERE $wheresql"),0);
$f_list = array();
if($count && $idtype == 'tid') {
    $query = DB::query("SELECT ft.*, ff.name FROM ".DB::table('home_favorite')." hf
        LEFT JOIN ".DB::table('forum_thread')." ft ON(hf.id=ft.tid)
        LEFT JOIN ".DB::table('forum_forum')." ff USING(fid)
        WHERE $wheresql
        ORDER BY hf.dateline DESC
        LIMIT $start,$perpage");
    while ($value = DB::fetch($query)) {
        $f_list[] = $value;
    }
}
