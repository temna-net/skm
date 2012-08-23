<?php

defined('IN_MOBIQUO') or exit;

loaducenter();

require_once UC_ROOT.'lib/db.class.php';
$boxid = $_GET['boxid'];
if (class_exists(ucclient_db)) {
    $uc_db = new ucclient_db();
} else {
    $uc_db = new db();
}
$uc_db->connect(UC_DBHOST, UC_DBUSER, UC_DBPW, '', UC_DBCHARSET, UC_DBCONNECT, UC_DBTABLEPRE);

$pm = uc_pm_viewnode($uid, 0, $msg_id);

if (!$pm) {
    showmessage(lang('forum/wap', 'pm_nonexistence'));
} elseif ($pm['new']) {
    $_ENV['pm']->set_pm_status($pm['msgtoid'], '', $pm['pmid']);
}

$displayid = $pm['folder'] == 'inbox' ? $pm['msgfromid'] : $pm['msgtoid'];

$pm['msgto'] = get_user_name_by_id($pm['msgtoid']);
$pm['icon_url'] = get_user_avatar($displayid, '', true);
if($boxid == 'inbox')
{
    $pm['icon_url'] = get_user_avatar($pm['msgfromid']); 
}

$pm['is_online'] = false;
if($_G['setting']['vtonlinestatus']) {
    if($author = DB::fetch_first("SELECT s.uid, ms.lastactivity, ms.invisible AS authorinvisible
                        FROM ".DB::table('common_session')." s
                        LEFT JOIN ".DB::table('common_member_status')." ms ON s.uid=ms.uid
                        WHERE s.uid=$displayid AND s.invisible=0"))
    {
        if ($_G['setting']['vtonlinestatus'] == 2) {
            $pm['is_online'] = true;
        } elseif ($_G['setting']['vtonlinestatus'] == 1 && (TIMESTAMP - $author['lastactivity'] <= 10800) && !$author['authorinvisible']) {
            $pm['is_online'] = true;
        }
    }
}
