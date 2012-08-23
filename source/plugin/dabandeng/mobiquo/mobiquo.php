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
define('DABANDENG_VERSION', 'dzx2_1.3.0');
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
require('./xmlrpcresp.php');

if ($request_method && isset($server_param[$request_method]))
{
    loadcache('plugin');
    $dabandeng_settings = $_G['cache']['plugin']['dabandeng'];
    include('./include/'.$request_method.'.inc.php');
}

$rpcServer = new xmlrpc_server($server_param, false);
$rpcServer->setDebug(1);
$rpcServer->compress_response = true;
$rpcServer->response_charset_encoding = 'UTF-8';
$response = $rpcServer->service();

?>