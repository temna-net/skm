<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

include_once libfile('function/block');

set_request('dabandeng_latest_picture', 111, 'thread', 'forum_thread', 3600, 20);
set_request('dabandeng_latest_topic',   85, 'threadnew');
set_request('dabandeng_latest_post',    85, 'threadnew');
set_request('dabandeng_digest_topic',   85, 'threaddigest', 'forum_thread', 3600);
set_request('dabandeng_hot_topic',      85, 'threadhot',    'forum_thread', 3600);
set_request('dabandeng_stick_topic',    85, 'threadstick',  'forum_thread', 3600);
set_request('dabandeng_member_new',     77, 'membernew',    'member_member');
set_request('dabandeng_member_post',    77, 'memberposts',  'member_member');
set_request('dabandeng_stat',           1,  'stat',         'html_html');

function set_request ($name, $styleid, $script, $blockclass='forum_thread', $cachetime=600, $shownum=10)
{
    global $_G;
    
    $data = DB::fetch_first("SELECT bid FROM ".DB::table('common_block')." WHERE name='$name'");
    $bid = $data['bid'];
    
    $setarr = array(
        'name' => $name,
        'summary' => '',
        'styleid' => $styleid,
        'script' => $script,
        'param' => get_param($name),
        'cachetime' => $cachetime,
        'punctualupdate' => '0',
        'shownum' => $shownum,
        'picwidth' => 0,
        'picheight' => 0,
        'target' => 'blank',
        'dateuformat' => '0',
        'dateformat' => 'Y-m-d H:i',
        'hidedisplay' => '1',
        'dateline' => TIMESTAMP
    );
    
    if($bid) {
        DB::update('common_block', $setarr, array('bid' => $bid));
    } else {
        $setarr['blockclass'] = $blockclass;
        $setarr['uid'] = $_G['uid'];
        $setarr['username'] = $_G['username'];
        $setarr['notinherited'] = 0;
        $setarr['blocktype'] = '1';
        $bid = DB::insert('common_block', $setarr, true);
    }
    $_G['block'][$bid] = DB::fetch_first("SELECT * FROM ".DB::table('common_block')." WHERE bid='$bid'");
    
    block_updatecache($bid, true);
}

function get_param($name)
{
    include(DISCUZ_ROOT.'/source/language/block/lang_stat.php');
    
    $param = array(
        'dabandeng_latest_picture' => array(
            'rewardstatus' => 0,
            'picrequired' => 1,
            'orderby' => 'dateline',
            'titlelength' => 40,
            'summarylength' => 80,
            'startrow' => 0,
            'items' => 20,
        ),
        
        'dabandeng_latest_topic' => array(
            'special' => array(0),
            'viewmod' => 0,
            'rewardstatus' => 0,
            'picrequired' => 0,
            'orderby' => 'dateline',
            'lastpost' => 0,
            'titlelength' => 40,
            'summarylength' => 80,
            'startrow' => 0,
            'items' => 10,
        ),
        
        'dabandeng_latest_post' => array(
            'special' => array(0),
            'viewmod' => 0,
            'rewardstatus' => 0,
            'picrequired' => 0,
            'orderby' => 'lastpost',
            'lastpost' => 0,
            'titlelength' => 40,
            'summarylength' => 80,
            'startrow' => 0,
            'items' => 10,
        ),
        
        'dabandeng_digest_topic' => array(
            'digest' => array(1, 2, 3),
            'special' => array(0),
            'viewmod' => 0,
            'rewardstatus' => 0,
            'picrequired' => 0,
            'titlelength' => 40,
            'summarylength' => 80,
            'items' => 10,
        ),
        
        'dabandeng_hot_topic' => array(
            'special' => array(0),
            'viewmod' => 0,
            'rewardstatus' => 0,
            'picrequired' => 0,
            'orderby' => 'heats',
            'lastpost' => 0,
            'titlelength' => 40,
            'summarylength' => 80,
            'items' => 10,
        ),
        
        'dabandeng_stick_topic' => array(
            'stick' => array(1, 2, 3),
            'special' => array(0),
            'viewmod' => 0,
            'rewardstatus' => 0,
            'picrequired' => 0,
            'titlelength' => 40,
            'summarylength' => 80,
            'items' => 10,
        ),
        
        'dabandeng_member_new' => array(
            'gender' => '',
            'xbirthprovince' => '',
            'xbirthcity' => '',
            'xresideprovince' => '',
            'xresidecity' => '',
            'xresidedist' => '',
            'xresidecommunity' => '',
            'avatarstatus' => 0,
            'startrow' => 0,
            'items' => 10,
        ),
        
        'dabandeng_member_post' => array(
            'orderby' => 'threads',
            'lastpost' => '',
            'startrow' => 0,
            'items' => 10,
        ),
        
        'dabandeng_stat' => array(
            'option' => array(
                'posts',
                'members',
                'bbsnewposts',
                'bbslastposts',
                'onlinemembers',
                'maxmembers',
            ),
        
            'posts_title' => $lang['stat_posts'],
            'groups_title' => $lang['stat_groups'],
            'members_title' => $lang['stat_members'],
            'groupmembers_title' => $lang['stat_groupmembers'],
            'groupnewposts_title' => $lang['stat_groupnewposts'],
            'bbsnewposts_title' => $lang['stat_bbsnewposts'],
            'bbslastposts_title' => $lang['stat_bbslastposts'],
            'onlinemembers_title' => $lang['stat_onlinemembers'],
            'maxmembers_title' => $lang['stat_maxmembers'],
            'doings_title' => $lang['stat_doings'],
            'blogs_title' => $lang['stat_blogs'],
            'albums_title' => $lang['stat_albums'],
            'pics_title' => $lang['stat_pics'],
            'shares_title' => $lang['stat_shares'],
            'items' => 10,
        ),
    );
    
    return addslashes(serialize($param[$name]));
}

$finish = TRUE;
