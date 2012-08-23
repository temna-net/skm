<?php

defined('IN_MOBIQUO') or exit;
define('NOROBOT', TRUE);
define('CURSCRIPT', 'misc');

$_G['gp_uid'] = $_G['uid'];
$_G['gp_hash'] = md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid']);

require_once 'module/dabandeng_upload.php';

if(empty($_G['gp_simple'])) {
		$_FILES['Filedata']['name'] = addslashes(diconv(urldecode($_FILES['Filedata']['name']), 'UTF-8'));
		$_FILES['Filedata']['type'] = $_G['gp_filetype'];
	}
$upload = new forum_upload();
