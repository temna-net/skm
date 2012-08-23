<?php

defined('IN_MOBIQUO') or exit;

loadforum();

if($_G['forum']['type'] == 'group' || $_G['forum']['simple'] & 1) {
    get_error('Can not get topic list on sub only forum!');
} elseif(empty($_G['forum']['fid']) || $_G['forum']['status'] != 1) {
    get_error('forum_nonexistence');
}

require_once libfile('function/forumlist');

$_G['action']['fid'] = $_G['fid'];
$_G['gp_dateline'] = isset($_G['gp_dateline']) ? intval($_G['gp_dateline']) : 0;
$_G['gp_digest'] = isset($_G['gp_digest']) ? 1 : '';
$_G['gp_archiveid'] = isset($_G['gp_archiveid']) ? intval($_G['gp_archiveid']) : 0;

$_G['forum']['name'] = strip_tags($_G['forum']['name']) ? strip_tags($_G['forum']['name']) : $_G['forum']['name'];

if($_G['forum']['viewperm'] && !forumperm($_G['forum']['viewperm']) && !$_G['forum']['allowview']) {
    get_error('viewperm_none_nopermission');
} elseif($_G['forum']['formulaperm']) {
    formulaperm($_G['forum']['formulaperm']);
}

if($_G['forum']['password']) {
    if($_G['gp_action'] == 'pwverify') {
        if($_G['gp_pw'] != $_G['forum']['password']) {
            get_error('forum_passwd_incorrect');
        } else {
            dsetcookie('fidpw'.$_G['fid'], $_G['gp_pw']);
        }
    } elseif($_G['forum']['password'] != $_G['cookie']['fidpw'.$_G['fid']]) {
        get_error('forum_passwd');
    }
}

$threadtableids = !empty($_G['cache']['threadtableids']) ? $_G['cache']['threadtableids'] : array();
$threadtable = $_G['gp_archiveid'] && in_array($_G['gp_archiveid'], $threadtableids) ? "forum_thread_{$_G['gp_archiveid']}" : 'forum_thread';

if($_G['forum']['autoclose']) {
    $closedby = $_G['forum']['autoclose'] > 0 ? 'dateline' : 'lastpost';
    $_G['forum']['autoclose'] = abs($_G['forum']['autoclose']) * 86400;
}

$start_limit = $start;
$_G['tpp'] = $limit ? $limit : 20;

$_G['gp_orderby'] = isset($_G['cache']['forums'][$_G['fid']]['orderby']) ? $_G['cache']['forums'][$_G['fid']]['orderby'] : 'lastpost';
$_G['gp_ascdesc'] = isset($_G['cache']['forums'][$_G['fid']]['ascdesc']) ? $_G['cache']['forums'][$_G['fid']]['ascdesc'] : 'DESC';

if(isset($mode) && $mode == 'TOP')    {
    $thisgid = $_G['forum']['type'] == 'forum' ? $_G['forum']['fup'] : (!empty($_G['cache']['forums'][$_G['forum']['fup']]['fup']) ? $_G['cache']['forums'][$_G['forum']['fup']]['fup'] : 0);
    $forumstickycount = $stickycount = $stickytids = 0;
    if($_G['setting']['globalstick'] && $_G['forum']['allowglobalstick']) {
        $stickytids = $_G['cache']['globalstick']['global']['tids'].(empty($_G['cache']['globalstick']['categories'][$thisgid]['count']) ? '' : ','.$_G['cache']['globalstick']['categories'][$thisgid]['tids']);
    
        $stickytids = trim($stickytids, ', ');
        if ($stickytids === ''){
            $stickytids = '0';
        }
    
        if($_G['forum']['status'] != 3) {
            $stickycount = $_G['cache']['globalstick']['global']['count'];
            if(!empty($_G['cache']['globalstick']['categories'][$thisgid])) {
                $stickycount += $_G['cache']['globalstick']['categories'][$thisgid]['count'];
            }
        }
    }
    
    $forumstickytids = array();
    loadcache('forumstick');
    
    $_G['cache']['forumstick'][$_G['fid']] = isset($_G['cache']['forumstick'][$_G['fid']]) ? $_G['cache']['forumstick'][$_G['fid']] : array();
    $forumstickycount = count($_G['cache']['forumstick'][$_G['fid']]);
    if ($forumstickycount) {
        foreach($_G['cache']['forumstick'][$_G['fid']] as $forumstickthread) {
            $forumstickytids[] = $forumstickthread['tid'];
        }
        if(!empty($forumstickytids)) {
            $forumstickytids = dimplode($forumstickytids);
            $stickytids .= ", $forumstickytids";
        }
        $query = DB::query("SELECT t.* FROM ".DB::table($threadtable)." t
            WHERE t.tid IN ($stickytids) AND (t.displayorder IN (1, 2, 3, 4))
            ORDER BY displayorder DESC, $_G[gp_orderby] $_G[gp_ascdesc]
            LIMIT $start_limit, $_G[tpp]");
    } else {
        $forumstickycount = DB::result_first("SELECT COUNT(*) FROM ".DB::table($threadtable)." t WHERE t.fid='{$_G['fid']}' AND t.displayorder='1'");
        $query = DB::query("SELECT t.* FROM ".DB::table($threadtable)." t
            WHERE (t.fid='{$_G['fid']}' AND t.displayorder='1') OR (t.tid IN ($stickytids) AND (t.displayorder IN (2, 3, 4)))
            ORDER BY displayorder DESC, $_G[gp_orderby] $_G[gp_ascdesc]
            LIMIT $start_limit, $_G[tpp]");
    }
    $stickycount += $forumstickycount;
    
    $_G['forum_threadcount'] = $stickycount;
    
} else {
    $_G['forum_threadcount'] = DB::result_first("SELECT COUNT(*) FROM ".DB::table($threadtable)." t WHERE t.fid='{$_G['fid']}' AND t.displayorder='0'");
    $query = DB::query("SELECT t.* FROM ".DB::table($threadtable)." t 
        WHERE t.fid='{$_G['fid']}' AND t.displayorder='0'
        ORDER BY t.displayorder DESC, t.$_G[gp_orderby] $_G[gp_ascdesc]
        LIMIT $start_limit, $_G[tpp]");
}

$_G['forum_threadlist'] = $threadids = array();

while(($querysticky && $thread = DB::fetch($querysticky)) || ($query && $thread = DB::fetch($query))) {
    $thread['moved'] = $thread['heatlevel'] = 0;
    if($_G['forum']['status'] != 3 && ($thread['closed'] || ($_G['forum']['autoclose'] && TIMESTAMP - $thread[$closedby] > $_G['forum']['autoclose']))) {
        $thread['new'] = 0;
        if($thread['isgroup'] == 1) {
            $thread['folder'] = 'common';
            $grouptids[] = $thread['closed'];
        } else {
            if($thread['closed'] > 1) {
                $thread['moved'] = $thread['tid'];
                //$thread['replies'] = '-';
                //$thread['views'] = '-';
            }
            $thread['folder'] = 'lock';
        }
    } elseif($_G['forum']['status'] == 3 && $thread['closed'] == 1) {
        $thread['folder'] = 'lock';
    } else {
        $thread['folder'] = 'common';
        if(empty($_G['cookie']['oldtopics']) || strpos($_G['cookie']['oldtopics'], 'D'.$thread['tid'].'D') === FALSE) {
            $thread['new'] = 1;
            $thread['folder'] = 'new';
        } else {
            $thread['new'] = 0;
        }
        $thread['weeknew'] = $thread['new'] && TIMESTAMP - 604800 <= $thread['dateline'];
        if($thread['replies'] > $thread['views']) {
            $thread['views'] = $thread['replies'];
        }
        if($_G['setting']['heatthread']['iconlevels']) {
            foreach($_G['setting']['heatthread']['iconlevels'] as $k => $i) {
                if($thread['heats'] > $i) {
                    $thread['heatlevel'] = $k + 1;
                    break;
                }
            }
        }
    }
    
    $posttableid = $thread['posttableid'];
    $thread['posttable'] = $posttableid ? "forum_post_$posttableid" : 'forum_post';

    $threadids[] = $thread['tid'];
    $_G['forum_threadlist'][] = $thread;

}

$_G['group']['allowpost'] = (!$_G['forum']['postperm'] && $_G['group']['allowpost']) || ($_G['forum']['postperm'] && forumperm($_G['forum']['postperm'])) || (isset($_G['forum']['allowpost']) && $_G['forum']['allowpost'] == 1 && $_G['group']['allowpost']);
$_G['group']['allowpost'] = isset($_G['forum']['allowpost']) && $_G['forum']['allowpost'] == -1 ?  false : $_G['group']['allowpost'];

