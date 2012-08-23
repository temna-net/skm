<?php

defined('IN_MOBIQUO') or exit;

require_once libfile('function/forumlist');

$hide_forums = '';
$mobiquo_config['hide_forum_id'] = unserialize($_G['cache']['plugin']['dabandeng']['hide_forum_id']);
if (!empty($mobiquo_config['hide_forum_id']))
{
    $fids = join(',', $mobiquo_config['hide_forum_id']);
    if($fids){
        $hide_forums = "AND f.fid NOT IN ($fids) ";
    }
}

$sql = !empty($_G['member']['accessmasks']) ?
    "SELECT f.fid, f.fup, f.type, f.name, f.threads, f.posts, f.todayposts, f.lastpost, f.inheritedmod, f.domain,
        f.forumcolumns, f.simple, ff.description, ff.moderators, ff.icon, ff.viewperm, ff.redirect, ff.extra, ff.password, a.allowview,
        hf.favid
        FROM ".DB::table('forum_forum')." f
        LEFT JOIN ".DB::table('forum_forumfield')." ff ON ff.fid=f.fid
        LEFT JOIN ".DB::table('forum_access')." a ON a.uid='$_G[uid]' AND a.fid=f.fid
        LEFT JOIN ".DB::table('home_favorite')." hf ON hf.uid='$_G[uid]' AND hf.id=f.fid AND hf.idtype='fid'
        WHERE f.status='1' $hide_forums ORDER BY f.type, f.displayorder"
    : "SELECT f.fid, f.fup, f.type, f.name, f.threads, f.posts, f.todayposts, f.lastpost, f.inheritedmod, f.domain,
        f.forumcolumns, f.simple, ff.description, ff.moderators, ff.icon, ff.viewperm, ff.redirect, ff.extra, ff.password,
        hf.favid
        FROM ".DB::table('forum_forum')." f
        LEFT JOIN ".DB::table('forum_forumfield')." ff USING(fid)
        LEFT JOIN ".DB::table('home_favorite')." hf ON hf.uid='$_G[uid]' AND hf.id=f.fid AND hf.idtype='fid'
        WHERE f.status='1' $hide_forums ORDER BY f.type, f.displayorder";

$query = DB::query($sql);
$forum_root = array(0 => array('fid' => 0, 'child' => array()));
$forum_g = $froum_f = $forum_s = array();
while($forum = DB::fetch($query)) {
    
    if ($forum['type'] != 'group') {
        $forum_icon = $forum['icon'];
        if(forum($forum)) {
            $forum['icon'] = get_forumimg($forum_icon);
        } else {
            continue;
        }
    }
    
    switch ($forum['type'])
    {
        case   'sub': $forum_s[] = $forum; break;
        case 'group': $forum_g[] = $forum; break;
        case 'forum': $froum_f[] = $forum; break;
    }

}
foreach($forum_s as $s_forum) {
    insert_forum($froum_f, $s_forum);
}

foreach($froum_f as $f_forum) {
    insert_forum($forum_g, $f_forum);
}

foreach($forum_g as $g_forum) {
    if ($g_forum['child']) {
        insert_forum($forum_root, $g_forum);
    }
}

$forum_tree = $forum_root[0]['child'];
//unset($xml_iso88591_Entities);
//error_log(print_r($GLOBALS, true), 3, 'my.log');
function insert_forum(&$forum_ups, $forum)
{
    global $_G;
    
    if ($forum['type'] == 'group' && !isset($forum['child'])) return;
    foreach($forum_ups as $id => $forum_up)
    {
        if ($forum_up['fid'] == $forum['fup'])
        {
            $forum_ups[$id]['todayposts'] += $forum['todayposts'];
            $subforumonly = $forum['simple'] & 1;
            if($forum['icon'] && strpos($forum['icon'], "ttp://"))
            {
                
                 $forum['icon'] = $forum['icon'];
            }else if($forum['icon'])
            {
                $forum['icon'] =  $_G['setting']['discuzurl'].'/'.$forum['icon'];  
            }else
            {
                $forum['icon'] = "";   
            }
            $xmlrpc_forum = new xmlrpcval(array(
                'forum_id'      => new xmlrpcval($forum['fid'], 'string'),
                'forum_name'    => new xmlrpcval(basic_clean($forum['name']), 'base64'),
                'description'   => new xmlrpcval(basic_clean($forum['description']), 'base64'),
                'parent_id'     => new xmlrpcval($forum['fup'] ? $forum['fup'] : '-1', 'string'),
                'logo_url'      => new xmlrpcval($forum['icon'], 'string'),
                'is_protected'  => new xmlrpcval($forum['password'] ? true : false, 'boolean'),
                'is_subscribed' => new xmlrpcval($forum['favid'] ? true : false, 'boolean'),
                'can_subscribe' => new xmlrpcval($_G['uid'] && $forum['type'] != 'group' ? true : false, 'boolean'),
                'url'           => new xmlrpcval('', 'string'),
                'sub_only'      => new xmlrpcval(($forum['type'] == 'group' || $subforumonly) ? true : false, 'boolean'),
                
                'new_post'      => new xmlrpcval($forum['todayposts'] ? true : false, 'boolean'),
                'today_post'    => new xmlrpcval(intval($forum['todayposts']), 'int'),
             ), 'struct');

            if (isset($forum['child']))
            {
                $xmlrpc_forum->addStruct(array('child' => new xmlrpcval($forum['child'], 'array')));
            }

            $forum_ups[$id]['child'][] = $xmlrpc_forum;
            continue;
        }
    }
}
