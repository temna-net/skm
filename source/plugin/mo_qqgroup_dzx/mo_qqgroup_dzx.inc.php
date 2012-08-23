<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$mo_cfg=$_G['cache']['plugin']['mo_qqgroup_dzx'];
$ggroup=(array)unserialize($mo_cfg[ggroup]);
if(in_array($_G[groupid],$ggroup)) showmessage(lang('plugin/mo_qqgroup_dzx','mogroup_block'));

$moroot="source/plugin/mo_qqgroup_dzx";
$mo_notice=$mo_cfg['notice'];
$qqgroup = explode("\r\n", $mo_cfg['groupc']);
if($qqgroup[0]==0){
	$qqgroup='';
}else{
	foreach($qqgroup as $key=>$value){
		$value=explode(':', $value);
		$value[2]=intval($value[2])==0?1:intval($value[2]);
		$qqgroup[$key]=$value;
	}
	unset($value);
}
include_once template('mo_qqgroup_dzx:mo_qqgroup_dzx');
?>