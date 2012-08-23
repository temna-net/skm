<?php
defined('IN_MOBIQUO') or exit;
$home_data = unserialize($dabandeng_settings['home_data']);

if (!empty($home_data)) {
    $show_home_data = true;
    $home_data = implode(',', $home_data);
} else {
    $show_home_data = false;
    $home_data = '';
}

lang('template');
$mlang = $_G['lang']['template'];

$question_list = array(
    new xmlrpcval(array(
        'id'        => new xmlrpcval('0', 'string'),
        'question'  => new xmlrpcval(basic_clean($mlang['security_question']), 'base64'),
    ), 'struct'),
    new xmlrpcval(array(
        'id'        => new xmlrpcval('1', 'string'),
        'question'  => new xmlrpcval(basic_clean($mlang['security_question_1']), 'base64'),
    ), 'struct'),
    new xmlrpcval(array(
        'id'        => new xmlrpcval('2', 'string'),
        'question'  => new xmlrpcval(basic_clean($mlang['security_question_2']), 'base64'),
    ), 'struct'),
    new xmlrpcval(array(
        'id'        => new xmlrpcval('3', 'string'),
        'question'  => new xmlrpcval(basic_clean($mlang['security_question_3']), 'base64'),
    ), 'struct'),
    new xmlrpcval(array(
        'id'        => new xmlrpcval('4', 'string'),
        'question'  => new xmlrpcval(basic_clean($mlang['security_question_4']), 'base64'),
    ), 'struct'),
    new xmlrpcval(array(
        'id'        => new xmlrpcval('5', 'string'),
        'question'  => new xmlrpcval(basic_clean($mlang['security_question_5']), 'base64'),
    ), 'struct'),
    new xmlrpcval(array(
        'id'        => new xmlrpcval('6', 'string'),
        'question'  => new xmlrpcval(basic_clean($mlang['security_question_6']), 'base64'),
    ), 'struct'),
    new xmlrpcval(array(
        'id'        => new xmlrpcval('7', 'string'),
        'question'  => new xmlrpcval(basic_clean($mlang['security_question_7']), 'base64'),
    ), 'struct'),
);


$forum_stat['members'] = $_G['cache']['userstats']['totalmembers'];
$forum_stat['posts'] = DB::result_first("SELECT sum(posts) FROM ".DB::table('forum_forum')." WHERE status='1' and type!='group'");
$forum_stat['topics'] = DB::result_first("SELECT sum(threads) FROM ".DB::table('forum_forum')." WHERE status='1' and type!='group'");
$postdata = $_G['cache']['historyposts'] ? explode("\t", $_G['cache']['historyposts']) : array(0,0);
$forum_stat['yesterdayposts'] = intval($postdata[0]);
$forum_stat['mostposts'] = intval($postdata[1]);

$filed = array();
$query = DB::query("SELECT showinregister FROM ".DB::table('common_member_profile_setting'));
while($row = DB::fetch($query)) {
    $filed[] = $row;
}
$arr = array();
foreach($filed as $key=>$value)
{
    $arr[] = $filed[$key]['showinregister'];   
}
$result = in_array(1,$arr) ? false : true;