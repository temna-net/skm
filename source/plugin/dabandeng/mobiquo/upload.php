<?php
/*======================================================================*\
|| #################################################################### ||
|| # Copyright &copy;2009 Quoord Systems Ltd. All Rights Reserved.    # ||
|| # This file may not be redistributed in whole or significant part. # ||
|| # This file is part of the Tapatalk package and should not be used # ||
|| # and distributed for any other purpose that is not approved by    # ||
|| # Quoord Systems Ltd.                                              # ||
|| # http://www.quoord.com | http://www.dabandeng.com                 # ||
|| #################################################################### ||
\*======================================================================*/

define('IN_MOBIQUO', true);
define('MOBIQUO_DEBUG', 0);
define('DISCUZ_ROOT', dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/');
error_reporting(MOBIQUO_DEBUG);
ob_start();

require('./lib/xmlrpc.inc');
require('./lib/xmlrpcs.inc');
require('./mobiquo_common.php');
require('./server_define.php');
require('./env_setting.php');
if ($_POST['method_name'] == 'upload_attach')
{
    $_G['gp_uid'] = $_G['uid'];
    $_G['gp_hash'] = md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid']);
    
    require_once 'module/dabandeng_upload.php';
    if(empty($_G['gp_simple'])) {
		$_FILES['Filedata']['name'] = addslashes(diconv(urldecode($_FILES['Filedata']['name']), 'UTF-8'));
		$_FILES['Filedata']['type'] = $_G['gp_filetype'];
	}
    $upload = new forum_upload();
} elseif ($_POST['method_name'] == 'upload_avatar')
{
    require($phpbb_root_path . 'common.' . $phpEx);
    require($phpbb_root_path . 'includes/functions_user.' . $phpEx);
    
    $user->session_begin();
    $auth->acl($user->data);
    $user->setup('ucp');
    $user->add_lang('posting');
    
    $status = true;
    $error = array();
    if (!$user->data['is_registered']) {
        $status = false;
        $error[] = $mobiquo_error_code[9];
    } else {
        include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
        
        if (!avatar_process_user($error))
        {
            $status = false;
            // Replace "error" strings with their real, localised form
            $error = preg_replace('#^([A-Z_]+)$#e', "(!empty(\$user->lang['\\1'])) ? \$user->lang['\\1'] : '\\1'", $error);
    
            if (!$config['allow_avatar'] && $user->data['user_avatar_type'])
            {
                $error[] = $user->lang['AVATAR_NOT_ALLOWED'];
            }
            else if ((($user->data['user_avatar_type'] == AVATAR_UPLOAD) && !$config['allow_avatar_upload']) ||
             (($user->data['user_avatar_type'] == AVATAR_REMOTE) && !$config['allow_avatar_remote']) ||
             (($user->data['user_avatar_type'] == AVATAR_GALLERY) && !$config['allow_avatar_local']))
            {
                $error[] = $user->lang['AVATAR_TYPE_NOT_ALLOWED'];
            }
        }
    }
    $warn_msg = strip_tags(join("\n", $error));
}

$rpcServer = new xmlrpc_server($server_param, false);
$rpcServer->setDebug(1);
$rpcServer->compress_response = 'true';
$rpcServer->response_charset_encoding = 'UTF-8';
$raw_data = '<?xml version="1.0"?><methodCall><methodName>' . trim($_POST['method_name']) . '</methodName><params></params></methodCall>';
$response = $rpcServer->service($raw_data);



function upload_attach_func()
{
    global $upload;
    
    $xmlrpc_result = new xmlrpcval(array(
        'attachment_id' => new xmlrpcval($upload->aid),
        'result'        => new xmlrpcval(true, 'boolean'),
    ), 'struct');
    
    return new xmlrpcresp($xmlrpc_result);
}

function upload_avatar_func() {
    global $status, $warn_msg;
    
    $xmlrpc_result = new xmlrpcval(array(
        'result'        => new xmlrpcval($status, 'boolean'),
        'result_text'   => new xmlrpcval($warn_msg, 'base64'),
    ), 'struct');
    
    return new xmlrpcresp($xmlrpc_result);
}