<?php
defined('IN_MOBIQUO') or exit;
mobi_parse_requrest();
$mobi_mod = 'forum';
if (!$request_method && isset($_POST['method_name'])) $request_method = $_POST['method_name'];
switch ($request_method) {
    case 'upload_attach':
        $_GET['action'] = 'swfupload';
        $_GET['operation'] = 'upload';
        $_GET['fid'] = intval($_POST['forum_id']);
        $_GET['mod'] = 'swfupload';
        $_POST['Upload'] = 'Submit Query';
        $mobi_mod = 'misc';
        
        if (isset($_FILES['attachment']['name'])){
            $_FILES['Filedata'] = array(
                'name' => $_FILES['attachment']['name'][0],
                'type' => $_FILES['attachment']['type'][0],
                'tmp_name' => $_FILES['attachment']['tmp_name'][0],
                'error' => $_FILES['attachment']['error'][0],
                'size' => $_FILES['attachment']['size'][0],
            );
        }
        
        break;
    case 'attach_image':
        if ($params_num >= 3) {
            $_GET['action'] = 'swfupload';
            $_GET['operation'] = 'upload';
            $_GET['fid'] = $request_params[3];
            $_GET['mod'] = 'swfupload';
            $_POST['Filename'] = $request_params[1];
            $_POST['Upload'] = 'Submit Query';
            $mobi_mod = 'misc';
            
            $fp = tmpfile();
            fwrite($fp, $request_params[0]);
            $file_info = stream_get_meta_data($fp);
            $tmp_name = $file_info['uri'];
            $filesize = @filesize($tmp_name);
            
            $_FILES['Filedata'] = array(
                'name'      => $request_params[1],
                'type'      => $request_params[2] == 'JPG' ? 'image/jpeg' : 'image/png',
                'tmp_name'  => $tmp_name,
                'error'     => 0,
                'size'      => $filesize ? $filesize : strlen($request_params[0])
            );
        } else {
            get_error('param_error');
        }
        break;
    case 'login':
    case 'authorize_user':
        if ($params_num == 2 || $params_num == 4) {
            $_POST['username'] = $request_params[0];
            $_POST['password'] = $request_params[1];
            $_POST['loginfield'] = 'username';
            $_GET['mod'] = 'logging';
            $_GET['action'] = 'login';
            $_GET['loginsubmit'] = 'yes';
            $mobi_mod = 'member';
            if ($params_num == 4) {
                $_POST['questionid'] = $request_params[2];
                $_POST['answer'] = $request_params[3];
            }
        } else {
            get_error('param_error');
        }
        break;
    case 'logout_user':
        $_GET['mod'] = 'logging';
        $_GET['action'] = 'logout';
        break;
        
    case 'register':
        $_POST['username'] = $request_params[0];
        $_POST['password'] = $request_params[1];
        $_POST['email']    = $request_params[2];
        break;
    case 'get_bookmarked_topic': 
        $start_num = intval(isset($request_params[0]) ? $request_params[0] : '0');
        $end_num = intval(isset($request_params[1]) ? $request_params[1] : '19');
        if ($start_num > $end_num) {
            get_error('param_error');
        } elseif ($end_num - $start_num >= 50) {
            $end_num = $start_num + 49;
        }
        $limit_num = $end_num - $start_num + 1;
        break;
    case 'create_message':
        if ($params_num == 3 || $params_num == 5) {
            $_POST['username'] = implode(',', $request_params[0]);
            $_POST['message'] = $request_params[2];
            $_POST['pmsubmit'] = true;
            $_POST['pmsubmit_btn'] = true;
            $_GET['op'] = 'send';
            $_GET['ac'] = 'pm';
            $_GET['mod'] = 'spacecp';
            $mobi_mod = 'home';
        } else {
            get_error('param_error');
        }
        break;
    case 'create_topic':
        if ($params_num >= 4) {
            $_GET['mod'] = 'post';
            $_GET['action'] = 'newthread';
            $_GET['fid'] = intval($request_params[0]);
            $_GET['topicsubmit'] = 'yes';
            
            $_POST['posttime'] = time();
            $_POST['subject'] = $request_params[1];
            $_POST['message'] = $request_params[3];
            $_POST['usesig'] = 1;
            $_POST['allownoticeauthor'] = 1;
            if(isset($request_params[4])) $_POST['attachnew'] = array( $request_params[4] => array( 'description'=> ''));
            if(isset($request_params[5])) $_POST['typeid'] = $request_params[5];
        } else {
            get_error('param_error');
        }
        break;
    case 'new_topic':
        if ($params_num >= 3 && $params_num <= 6) {
            $_GET['mod'] = 'post';
            $_GET['action'] = 'newthread';
            $_GET['fid'] = intval($request_params[0]);
            $_GET['topicsubmit'] = 'yes';
            
            $_POST['posttime'] = time();
            $_POST['subject'] = $request_params[1];
            $_POST['message'] = $request_params[2];
            $_POST['usesig'] = 1;
            $_POST['allownoticeauthor'] = 1;
            if(isset($request_params[3])) $_POST['typeid'] = $request_params[3];
            if(isset($request_params[4]) && is_array($request_params[4])) {
                foreach($request_params[4] as $aid) {
                    $_POST['attachnew'][$aid] = array('description'=> '');
                }
            }
            if(isset($request_params[5]) && is_array($request_params[5])) {
                foreach($request_params[5] as $daid) {
                    $_POST['attachdel'][] = $daid;
                }
            }
        } else {
            get_error('param_error');
        }
        break;
    case 'reply_topic':
        if ($params_num >= 4) {
            $_POST['posttime'] = time();
            $_POST['message'] = $request_params[2];
            $_POST['subject'] = $request_params[3];
            $_POST['usesig'] = 1;
            if(isset($request_params[4])) $_POST['attachnew'] = array( $request_params[4] => array( 'description' => '','readperm' => '', 'price' => 0));
            $_GET['mod'] = 'post';
            $_GET['action'] = 'reply';
            $_GET['tid'] = intval($request_params[0]);
            $_GET['replysubmit'] = 'yes';
        } else {
            get_error('param_error');
        }
        break;
    case 'reply_post':
        if ($params_num >= 4) {
            $_POST['posttime'] = time();
            $_POST['subject'] = $request_params[2];
            $_POST['message'] = $request_params[3];
            $_POST['usesig'] = 1;
//            if(isset($request_params[4]) && is_array($request_params[4])) {
//                foreach($request_params[4] as $aid) {
//                    $_POST['attachnew'] = array($aid => array( 'description'=> ''));
//                }
//            }

            if(isset($request_params[4]) && is_array($request_params[4])) {
                foreach($request_params[4] as $aid) {
                    $_POST['attachnew'][$aid] = array('description'=> '');
                }
            }
            if(isset($request_params[5]) && is_array($request_params[5])) {
                foreach($request_params[5] as $daid) {
                    $_POST['attachdel'][] = $daid;
                }
            }
            $_GET['mod'] = 'post';
            $_GET['action'] = 'reply';
            $_GET['fid'] = intval($request_params[0]);
            $_GET['tid'] = intval($request_params[1]);
            $_GET['replysubmit'] = 'yes';
            $return_html = isset($request_params[6]) ? $request_params[6] : false;
        } else {
            get_error('param_error');
        }
        break;
    case 'get_quote_post':
        if ($params_num == 1) {
            $_GET['mod'] = 'post';
            $_GET['action'] = 'reply';
            $_GET['repquote'] = intval($request_params[0]);
        } else {
            get_error('param_error');
        }
        break;
    case 'delete_message':
        if ($params_num == 1) {
            $msg_id = intval($request_params[0]);
        } else {
            get_error('param_error');
        }
        break;
    case 'get_board_stat': break;
    case 'get_box':
        if ($params_num >= 1) {
            $_GET['mod'] = 'space';
            $_GET['do'] = 'pm';
            $_GET['boxid'] = $request_params[0];
            process_page($request_params[1], $request_params[2]);
            $mobi_mod = 'home';
        } else {
            get_error('param_error');
        }
        break;
    case 'get_box_info':
        $_GET['mod'] = 'space';
        $_GET['do'] = 'pm';
        $mobi_mod = 'home';
        break;
    case 'get_config': break;
    case 'get_forum': break;
    case 'get_inbox_stat': break;
    case 'get_home_data':
        if ($params_num == 1) {
            $home_data_id = intval($request_params[0]);
        }
        break;
    case 'get_home':
        if ($params_num == 1) {
            $home_data_id = intval($request_params[0]);
        }
        break;
    case 'get_message':
        if ($params_num >= 1 && $params_num <= 3) {
            $msg_id = intval($request_params[0]);
            $_GET['boxid'] = $request_params[1];
            $_GET['mod'] = 'space';
            $_GET['do'] = 'pm';
            $_GET['subop'] = 'view';
            $return_html = isset($request_params[2]) ? $request_params[2] : false;
            $mobi_mod = 'home';
        } else {
            get_error('param_error');
        }
        break;
    case 'get_new_topic':
        $_GET['mod'] = 'forum';
        $_POST['searchsubmit'] = 'yes';
        $_POST['srchfrom'] = 604800;
        $mobi_mod = 'search';
        process_page($request_params[0], $request_params[1]);
        break;
    case 'get_online_users': 
        $_GET['mod'] = 'space';
        $_GET['do'] = 'friend';
        $_GET['view'] = 'online';
        $_GET['type'] = 'member';
        $mobi_mod = 'home';
        break;
    case 'get_raw_post':
        if ($params_num == 1) {
            $_GET['pid'] = intval($request_params[0]);
            $_GET['action'] = 'edit';
            $_GET['mod'] = 'post';
        } else {
            get_error('param_error');
        }
        break;
    case 'get_subscribed_topic':
        $_GET['mod'] = 'space';
        $_GET['do'] = 'favorite';
        $_GET['view'] = 'me';
        $_GET['type'] = 'thread';
        process_page($request_params[0], $request_params[1]);
        $mobi_mod = 'home';
        break;
    case 'get_subscribed_forum':
        $_GET['mod'] = 'space';
        $_GET['do'] = 'favorite';
        $_GET['view'] = 'me';
        $_GET['type'] = 'forum';
        $mobi_mod = 'home';
        break;
    case 'get_thread':
        if ($params_num >= 1) {
            $_GET['tid'] = intval($request_params[0]);
            $_GET['mod'] = 'viewthread';
            process_page($request_params[1], $request_params[2]);
            $return_html = isset($request_params[3]) ? $request_params[3] : false;
        } else {
            get_error('param_error');
        }
        break;
    case 'get_topic':
        if ($params_num >= 1) {
            $_GET['fid'] = intval($request_params[0]);
            $_GET['mod'] = 'forumdisplay';
            $mode = isset($request_params[3]) ? $request_params[3] : '';
            process_page($request_params[1], $request_params[2]);
        } else {
            get_error('param_error');
        }
        break;
    case 'login_forum':
        if ($params_num == 2) {
            $_GET['fid'] = intval($request_params[0]);
            $_GET['action'] = 'pwverify';
            $_GET['mod'] = 'forumdisplay';
            $_POST['pw'] = $request_params[1];
            $_POST['loginsubmit'] = true;
        } else {
            get_error('param_error');
        }
        break;
    case 'get_user_info':
        if ($params_num == 0 || $params_num == 1) {
            if (isset($request_params[0]))
                $_GET['username'] = $request_params[0];
            $_GET['mod'] = 'space';
            $_GET['do'] = 'profile';
            $mobi_mod = 'home';
        } else {
            get_error('param_error');
        }
        break;
    case 'get_user_reply_post':
        if ($params_num == 1) {
            $username = $request_params[0];
        } else {
            get_error('param_error');
        }
        $_GET['mod'] = 'forum';
        $_POST['srchtxt'] = '';
        $_POST['searchsubmit'] = 'yes';
        $_POST['srchtype'] = 'fulltext';
        $_POST['srchuname'] = $username;
        $_POST['srchfilter'] = 'all';
        $_POST['srchfrom'] = 0;
        $_POST['orderby'] = 'lastpost';
        $_POST['ascdesc'] = 'desc';
        $_POST['srchfid'] = array('all');
        $_POST['before'] = '';
        $mobi_mod = 'search';
        $search_post = true;
        process_page(0, 49);
        break;
    case 'get_user_topic':
        if ($params_num == 1) {
            $username = $request_params[0];
        } else {
            get_error('param_error');
        }
        $_GET['mod'] = 'forum';
        $_POST['srchtxt'] = '';
        $_POST['searchsubmit'] = 'yes';
        $_POST['srchuname'] = $username;
        $_POST['srchfilter'] = 'all';
        $_POST['srchfrom'] = 0;
        $_POST['orderby'] = 'lastpost';
        $_POST['ascdesc'] = 'desc';
        $_POST['srchfid'] = array('all');
        $_POST['before'] = '';
        $mobi_mod = 'search';
        process_page(0, 49);
        break;
    case 'save_raw_post':
        if ($params_num == 3) {
            $_GET['editsubmit'] = 'yes';
            $_GET['action'] = 'edit';
            $_GET['mod'] = 'post';
            $_POST['pid'] = intval($request_params[0]);
            $_POST['subject'] = $request_params[1];
            $_POST['message'] = $request_params[2];
            $_POST['posttime'] = time();
            $_POST['editsubmit'] = 'true';
            $_POST['usesig'] = 1;
            $_POST['allownoticeauthor'] = 1;
            $return_html = isset($request_params[3]) ? $request_params[3] : false;
        } else {
            get_error('param_error');
        }
        break;
    case 'subscribe_topic':
        if ($params_num == 1) {
            $_GET['id'] = intval($request_params[0]);
            $_GET['ac'] = 'favorite';
            $_GET['type'] = 'thread';
            $_GET['mod'] = 'spacecp';
            $_GET['favoritesubmit'] = 'true';
            $mobi_mod = 'home';
        } else {
            get_error('param_error');
        }
        break;
    case 'unsubscribe_topic':
        if ($params_num == 1) {
            $_GET['id'] = intval($request_params[0]);
            $_GET['ac'] = 'favorite';
            $_GET['mod'] = 'spacecp';
            $_GET['op'] = 'delete';
            $_GET['deletesubmit'] = 'true';
            $mobi_mod = 'home';
        } else {
            get_error('param_error');
        }
        break;
    case 'subscribe_forum':
        if ($params_num == 1) {
            $_GET['id'] = intval($request_params[0]);
            $_GET['ac'] = 'favorite';
            $_GET['type'] = 'forum';
            $_GET['mod'] = 'spacecp';
            $_GET['favoritesubmit'] = true;
            $mobi_mod = 'home';
        } else {
            get_error('param_error');
        }
        break;
    case 'unsubscribe_forum':
        if ($params_num == 1) {
            $_GET['id'] = intval($request_params[0]);
            $_GET['ac'] = 'favorite';
            $_GET['type'] = 'forum';
            $_GET['mod'] = 'spacecp';
            $_GET['op'] = 'delete';
            $_GET['deletesubmit'] = 'true';
            $mobi_mod = 'home';
        } else {
            get_error('param_error');
        }
        break;
    case 'search_topic':
        if ($params_num == 1 || $params_num == 3 || $params_num == 4) {
            if (isset($request_params[3])) {
                $_GET['searchid'] = intval($request_params[3]);
            }
            $_GET['mod'] = 'forum';
            $_POST['srchtxt'] = $request_params[0];
            $_POST['searchsubmit'] = 'yes';
            $_POST['srchfilter'] = 'all';
            $_POST['srchfrom'] = 0;
            $_POST['orderby'] = 'lastpost';
            $_POST['ascdesc'] = 'desc';
            $_POST['srchfid'] = array('all');
            $_POST['before'] = '';
            $mobi_mod = 'search';
            process_page($request_params[1], $request_params[2]);
        } else {
            get_error('param_error');
        }
        break;
    case 'search_post':
        if ($params_num == 1 || $params_num == 3 || $params_num == 4) {
            if (isset($request_params[3])) {
                $_GET['searchid'] = intval($request_params[3]);
            }
            $_GET['mod'] = 'forum';
            $_POST['srchtxt'] = $request_params[0];
            $_POST['srchtype'] = 'fulltext';
            $_POST['searchsubmit'] = 'yes';
            $_POST['srchfilter'] = 'all';
            $_POST['srchfrom'] = 0;
            $_POST['orderby'] = 'lastpost';
            $_POST['ascdesc'] = 'desc';
            $_POST['srchfid'] = array('all');
            $_POST['before'] = '';
            $mobi_mod = 'search';
            $search_post = true;
            process_page($request_params[1], $request_params[2]);
        } else {
            get_error('param_error');
        }
        break;
    case 'search':
        if ($params_num == 1 || $params_num == 3) {
            $search_field = array(
                'srchtxt',      // search key words
                'srchtype',     // 'title' or 'fulltext'
                'srchuname',    // search author name
                'srchfilter',   // 'all', 'digest', or 'top'
                'special',      // special topic array of 1,2,3,4,5 (array)
                'srchfrom',     // search from time
                'before',       // before search from time or after
                'orderby',      // 'lastpost', 'dateline', 'replies', 'views'
                'ascdesc',      // 'asc' or 'desc'
                'srchfid',      // search forum id (array)
                'searchid',     // search id
            );
            
            $_POST['srchtype'] = 'title';
            $_POST['srchfilter'] = 'all';
            $_POST['orderby'] = 'lastpost';
            $_POST['srchfid'] = array('all');
            $_POST['st'] = 'on';
            $_POST['searchsubmit'] = true; 
            
            if (is_array($request_params[0]))
            {
                foreach($request_params[0] as $field)
                {
                    if (!in_array($field['name'], $search_field)) continue;
                    
                    $_POST[$field['name']] = $field['value'];
                }
            } else {
                $search_filters = explode(';', $request_params[0]);
                foreach($search_filters as $search_filter){
                    $search_key_value = preg_split('/=/', $search_filter, 2);
                    if (!in_array(trim($search_key_value[0]), $search_field)) continue;
                    
                    $_POST[trim($search_key_value[0])] = trim($search_key_value[1]);
                }
            }
            
            $start_num = intval(isset($request_params[1]) ? $request_params[1] : '0');
            $end_num = intval(isset($request_params[2]) ? $request_params[2] : '19');
            if ($start_num > $end_num) {
                get_error('param_error');
            } elseif ($end_num - $start_num >= 50) {
                $end_num = $start_num + 49;
            }
        } else {
            get_error('param_error');
        }
        break;
}



//$mobiquo_config['home_data'] = preg_grep("/^\d+$/", preg_split('/\s*,\s*/', $mobiquo_config['home_data']));
//$mobiquo_config['hide_forum_id'] = unserialize($mobiquo_config['hide_forum_id']);
if (!isset($mobiquo_config['hide_forum_id'][0])) {
    $mobiquo_config['hide_forum_id'] = array();
}
if ($mobi_mod == 'forum') {
    define('APPTYPEID', 2);
    define('CURSCRIPT', 'forum');
    //require DISCUZ_ROOT.'./source/class/class_core.php';
    require 'lib/class_core.php';
    require DISCUZ_ROOT.'./source/function/function_forum.php';
    $discuz = & discuz_core::instance();
    $modarray = array('ajax','announcement','attachment','forumdisplay',
    	'group','image','index','medal','misc','modcp','notice','post','redirect',
    	'relatekw','relatethread','rss','topicadmin','trade','viewthread','tag'
    );
    $modcachelist = array(
    	'index'		=> array('announcements', 'onlinelist', 'forumlinks',
    			'heats', 'historyposts', 'onlinerecord', 'userstats', 'diytemplatenameforum'),
    	'forumdisplay'	=> array('smilies', 'announcements_forum', 'globalstick', 'forums',
    			'onlinelist', 'forumstick', 'threadtable_info', 'threadtableids', 'stamps', 'diytemplatenameforum'),
    	'viewthread'	=> array('smilies', 'smileytypes', 'forums', 'usergroups',
    			'stamps', 'bbcodes', 'smilies',	'custominfo', 'groupicon', 'stamps',
    			'threadtableids', 'threadtable_info', 'posttable_info', 'diytemplatenameforum'),
    	'redirect'	=> array('threadtableids', 'threadtable_info', 'posttable_info'),
    	'post'		=> array('bbcodes_display', 'bbcodes', 'smileycodes', 'smilies', 'smileytypes',
    			'domainwhitelist'),
    	'space'		=> array('fields_required', 'fields_optional', 'custominfo'),
    	'group'		=> array('grouptype', 'diytemplatenamegroup'),
    );
    $mod = !in_array($discuz->var['mod'], $modarray) ? 'index' : $discuz->var['mod'];
    
    define('CURMODULE', $mod);
    $cachelist = array();
if(isset($modcachelist[CURMODULE])) {
	$cachelist = $modcachelist[CURMODULE];
}
if($discuz->var['mod'] == 'group') {
	$_G['basescript'] = 'group';
}

$discuz->cachelist = $cachelist;
$discuz->init();
} elseif ($mobi_mod == 'member') {
    define('APPTYPEID', 0);
    define('CURSCRIPT', 'member');
    
    require 'lib/class_core.php';
    
    $discuz = & discuz_core::instance();
    
    $modarray = array('activate', 'clearcookies', 'emailverify', 'getpasswd',
                        'groupexpiry', 'logging', 'lostpasswd',
                        'register', 'regverify', 'switchstatus');
    
    
    $mod = !in_array($discuz->var['mod'], $modarray) ? 'register' : $discuz->var['mod'];
    
    define('CURMODULE', $mod);
    
    $modcachelist = array('register' => array('modreasons', 'stamptypeid', 'fields_required', 'fields_optional', 'fields_register', 'ipctrl'));
    
    $cachelist = array();
    if(isset($modcachelist[CURMODULE])) {
        $cachelist = $modcachelist[CURMODULE];
    }
    
    $discuz->cachelist = $cachelist;
    $discuz->init();
    if($mod == 'register' && $discuz->var['mod'] != $_G['setting']['regname']) {
        get_error('undefined_action');
    }
    
    require libfile('function/member');
    //require DISCUZ_ROOT.'./source/module/member/member_'.$mod.'.php';
} elseif ($mobi_mod == 'home') {
    define('APPTYPEID', 1);
    define('CURSCRIPT', 'home');
    
    if(!empty($_GET['mod']) && ($_GET['mod'] == 'misc' || $_GET['mod'] == 'invite')) {
        define('ALLOWGUEST', 1);
    }
    
    require_once 'lib/class_core.php';
    require_once DISCUZ_ROOT.'./source/function/function_home.php';
    $discuz = & discuz_core::instance();
    $cachelist = array('magic','userapp','usergroups');
    $discuz->cachelist = $cachelist;
    $discuz->init();
    
    $space = array();
    
    $mod = getgpc('mod');
    if(!in_array($mod, array('space', 'spacecp', 'misc', 'magic', 'editor', 'userapp', 'invite', 'task', 'medal'))) {
        $mod = 'space';
        $_GET['do'] = 'home';
    }
    define('CURMODULE', $mod);
    if (file_exists('module/home_'.$mod.'.php'))
        require_once 'module/home_'.$mod.'.php';
    
} elseif ($mobi_mod == 'search') {
    define('APPTYPEID', 0);
    define('CURSCRIPT', 'search');
    
    require 'lib/class_core.php';
    
    $discuz = & discuz_core::instance();
    
    $modarray = array('my', 'user', 'curforum');
    
    $modcachelist = array('register' => array('modreasons', 'stamptypeid', 'fields_required', 'fields_optional'));
    
    $cachelist = $slist = array();
    if(isset($modcachelist[CURMODULE])) {
        $cachelist = $modcachelist[CURMODULE];
    }
    
    $discuz->cachelist = $cachelist;
    $discuz->init();
    
    if(in_array($discuz->var['mod'], $modarray) || !empty($_G['setting']['search'][$discuz->var['mod']]['status'])) {
        $mod = $discuz->var['mod'];
    } else {
        foreach($_G['setting']['search'] as $mod => $value) {
            if(!empty($value['status'])) {
                break;
            }
        }
    }
    
    define('CURMODULE', $mod);
    
    require_once libfile('function/discuzcode');
    
    if($mod == 'curforum') {
        $mod = 'forum';
        $_G['gp_srchfid'] = array($_G['gp_srhfid']);
        $_G['gp_srhfid'] = $_G['gp_srhfid'];
    } elseif($mod == 'forum') {
        $_G['gp_srchfid'] = array();
        $_G['gp_srhfid'] = '';
    }
} elseif ($mobi_mod == 'misc') {
    define('APPTYPEID', 100);
    define('CURSCRIPT', 'misc');
    
    require 'lib/class_core.php';
    
    $discuz = & discuz_core::instance();
    
    $modarray = array('seccode', 'secqaa', 'initsys', 'invite', 'faq', 'report', 'swfupload', 'manyou', 'stat', 'ranklist');
    
    $modcachelist = array(
        'ranklist' => array('forums'),
    );
    
    $mod = getgpc('mod');
    $mod = (empty($mod) || !in_array($mod, $modarray)) ? 'error' : $mod;
    
    $cachelist = array();
    if(isset($modcachelist[$mod])) {
    	$cachelist = $modcachelist[$mod];
    }
    
    $discuz->cachelist = $cachelist;
    
    switch ($mod) {
        case 'secqaa':
        case 'manyou':
        case 'seccode':
            $discuz->init_cron = false;
            $discuz->init_session = false;
            break;
        case 'updatecache':
            $discuz->init_cron = false;
            $discuz->init_session = false;
        case 'ranklist':
            define('CLOSEBANNED', 1);
        default:
            break;
    }
    
    $discuz->init();
    
    define('CURMODULE', $mod);
}
parameter_to_local($_G);
set_fid_tid();

error_reporting(MOBIQUO_DEBUG);
header('Mobiquo_is_login:'.($_G['uid'] ? 'true' : 'false'));