<?php

defined('IN_MOBIQUO') or exit;

loaducenter();

$waittime = interval_check('post');
if($waittime > 0) {
    showmessage('operating_too_fast', '', array('waittime' => $waittime), array('return' => true));
}

cknewuser();

if(!checkperm('allowsendpm')) {
    showmessage('no_privilege', '', array(), array('return' => true));
}

$username = empty($_POST['username']) ? '' : $_POST['username'];
$coef = 1;
if(!empty($username)) {
    $users = $userarr = daddslashes(explode(',', dstripslashes($username)));
    foreach($users as $key => $value) {
        if(!empty($value)) {
            $users[$key] = $value;
        }
    }
    $coef = count($users);
}

!($_G['group']['exempt'] & 1) && checklowerlimit('sendpm', 0, $coef);

//$message = (!empty($_POST['messageappend']) ? $_POST['messageappend']."\n" : '').trim($_POST['message']);
$message = trim($_G['gp_message']);
$message = str_replace('QUOTE]', 'quote]', $message);

if(empty($message)) {
    showmessage('unable_to_send_air_news', '', array(), array('return' => true));
}
$message = censor($message);
loadcache(array('smilies', 'smileytypes'));
foreach($_G['cache']['smilies']['replacearray'] AS $key => $smiley) {
    $_G['cache']['smilies']['replacearray'][$key] = '[img]'.$_G['siteurl'].'static/image/smiley/'.$_G['cache']['smileytypes'][$_G['cache']['smilies']['typearray'][$key]]['directory'].'/'.$smiley.'[/img]';
}
$message = preg_replace($_G['cache']['smilies']['searcharray'], $_G['cache']['smilies']['replacearray'], $message);
$subject = '';

$return = 0;

$newusers = array();
if($users) {
    $query = DB::query('SELECT uid, username FROM '.DB::table('common_member')." WHERE username IN (".to_local(dimplode($users)).')');
    while($value = DB::fetch($query)) {
        $newusers[$value['uid']] = $value['username'];
        unset($users[array_search($value['username'], $users)]);
    }
}
if(empty($newusers)) {
    showmessage('message_bad_touser', dreferer(), array(), array('return' => true));
}
if(isset($newusers[$_G['uid']])) {
    showmessage('message_can_not_send_to_self', dreferer(), array(), array('return' => true));
}

foreach($newusers as $key=>$value) {
    if(isblacklist($key)) {
        showmessage('is_blacklist', dreferer(), array(), array('return' => true));
    }
}
$coef = count($newusers);

$return = uc_pm_send($_G['uid'], implode(',', $newusers), $subject, $message, 1, $pmid, 1);

if($return > 0) {
    DB::query("UPDATE ".DB::table('common_member_status')." SET lastpost='$_G[timestamp]' WHERE uid='$_G[uid]'");
    !($_G['group']['exempt'] & 1) && updatecreditbyaction('sendpm', 0, array(), '', $coef);
//    if(!empty($username)) {
//        showmessage(count($users) ? 'message_send_result' : 'do_success', 'home.php?mod=space&do=pm', array('users' => implode(',', $users), 'succeed' => count($newusers)));
//    } else {
//        showmessage('do_success', 'home.php?mod=space&do=pm&subop=view&pmid='.$_GET['pmid'].'&touid='.$_GET['touid'].'&daterange='.$_GET['daterange'].'#bottom', array('pmid' => $return), array('msgtype' => 3, 'showmsg' => false));
//    }
} else {
    if(in_array($return, array(-1,-2,-3,-4))) {
        showmessage('message_can_not_send'.abs($return), '', array(), array('return' => true));
    } else {
        showmessage('message_can_not_send', '', array(), array('return' => true));
    }
}
