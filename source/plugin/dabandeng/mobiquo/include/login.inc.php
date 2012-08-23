<?php
defined('IN_MOBIQUO') or exit;

define('NOROBOT', TRUE);

if(!in_array($_G['gp_action'], array('login', 'logout', 'seccode'))) {
    get_error('undefined_action');
}
$ctl_obj = new logging_ctl();
$method = 'on_'.$_G['gp_action'];
$ctl_obj->$method();

// build up the group permission
$cachelist = array('usergroup_'.$_G['groupid']);
if($_G['uid'] && $_G['adminid'] > 0 && $_G['groupid'] != $_G['adminid']) {
    $cachelist[] = 'admingroup_'.$_G['adminid'];
}

loadcache($cachelist);

if($_G['uid'] && $_G['group']['radminid'] == 0 && $_G['adminid'] > 0 && $_G['groupid'] != $_G['adminid'] && !empty($_G['cache']['admingroup_'.$_G['adminid']])) {
    $_G['group'] = array_merge($_G['group'], $_G['cache']['admingroup_'.$_G['adminid']]);
}


class logging_ctl {

    var $var = null;

    function logging_ctl() {
        require_once libfile('function/misc');
        loaducenter();
    }

    function on_login() {
        global $_G;
        if($_G['uid']) {
            $ucsynlogin = $_G['setting']['allowsynlogin'] ? uc_user_synlogin($_G['uid']) : '';
            $param = array('username' => $_G['member']['username'], 'uid' => $_G['member']['uid']);
            return;
            //showmessage('login_succeed', dreferer(), $param, array('showdialog' => 1, 'locationtime' => true, 'extrajs' => $ucsynlogin));
        }

        $seccodecheck = $_G['setting']['seccodestatus'] & 2;
        $invite = getinvite();


        if(!($_G['member_loginperm'] = logincheck())) {
            get_error('login_strike');
        }
        if($_G['gp_fastloginfield']) {
            $_G['gp_loginfield'] = $_G['gp_fastloginfield'];
        }
        $_G['uid'] = $_G['member']['uid'] = 0;
        $_G['username'] = $_G['member']['username'] = $_G['member']['password'] = '';
        $_G['setting']['autoidselect'] = 0 ;
        $result = userlogin($_G['gp_username'], $_G['gp_password'], $_G['gp_questionid'], $_G['gp_answer'], $_G['setting']['autoidselect'] ? 'auto' : $_G['gp_loginfield']);

        if($result['status'] > 0) {
            setloginstatus($result['member'], $_G['gp_cookietime'] ? 2592000 : 0);
            DB::query("UPDATE ".DB::table('common_member_status')." SET lastip='".$_G['clientip']."', lastvisit='".time()."' WHERE uid='$_G[uid]'");
            $ucsynlogin = $_G['setting']['allowsynlogin'] ? uc_user_synlogin($_G['uid']) : '';

            include_once libfile('function/stat');
            updatestat('login');
            updatecreditbyaction('daylogin', $_G['uid']);
            checkusergroup($_G['uid']);
            if($invite['id']) {
                DB::update("common_invite", array('fuid'=>$uid, 'fusername'=>$username), array('id'=>$invite['id']));
                updatestat('invite');
            }
            if($invite['uid']) {
                require_once libfile('function/friend');
                friend_make($invite['uid'], $invite['username'], false);
                dsetcookie('invite_auth', '');
                if($invite['appid']) {
                    updatestat('appinvite');
                }
            }

            $param = array('username' => $_G['member']['username'], 'uid' => $_G['member']['uid'], 'syn' => $ucsynlogin ? 1 : 0);
            if($_G['groupid'] == 8) {
                get_error('login_succeed_inactive_member', $param);
            } else {
                return;
                //showmessage('login_succeed', $invite?'home.php?mod=space&do=home':dreferer(), $param, array('extrajs' => $ucsynlogin));
            }
        } elseif($result['status'] == -1) {
            get_error('login_activation');
        } else {
            $password = preg_replace("/^(.{".round(strlen($_G['gp_password']) / 4)."})(.+?)(.{".round(strlen($_G['gp_password']) / 6)."})$/s", "\\1***\\3", $_G['gp_password']);
            $errorlog = dhtmlspecialchars(
                TIMESTAMP."\t".
                ($result['ucresult']['username'] ? $result['ucresult']['username'] : dstripslashes($_G['gp_username']))."\t".
                $password."\t".
                "Ques #".intval($_G['gp_questionid'])."\t".
                $_G['clientip']);
            writelog('illegallog', $errorlog);
            loginfailed($_G['member_loginperm']);
            $fmsg = $result['ucresult']['uid'] == '-3' ? (empty($_G['gp_questionid']) || $answer == '' ? 'login_question_empty' : 'login_question_invalid') : 'login_invalid';
            get_error($fmsg, array('loginperm' => $_G['member_loginperm']));
        }
    }

    function on_logout() {
        global $_G;

        $ucsynlogout = $_G['setting']['allowsynlogin'] ? uc_user_synlogout() : '';

        if($_G['gp_formhash'] != $_G['formhash']) {
            //showmessage('logout_succeed', dreferer(), array('formhash' => FORMHASH, 'ucsynlogout' => $ucsynlogout));
        }

        clearcookies();
        $_G['groupid'] = $_G['member']['groupid'] = 7;
        $_G['uid'] = $_G['member']['uid'] = 0;
        $_G['username'] = $_G['member']['username'] = $_G['member']['password'] = '';
        $_G['setting']['styleid'] = $_G['setting']['styleid'];

        //showmessage('logout_succeed', dreferer(), array('formhash' => FORMHASH, 'ucsynlogout' => $ucsynlogout));
    }

}

