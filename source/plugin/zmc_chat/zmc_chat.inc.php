<?php

if(!defined('IN_DISCUZ')){ exit('Access Denied'); }
$zmcg01 = array();
$zmcg01 = $_G['cache']['plugin']['zmc_chat'];
if($zmcg01['chatswitch'] == '1'){
$zallowsay01 = 1;
$checkboss01 = 0;
$dontvisit01 = explode(',',$zmcg01['donview']);
foreach($dontvisit01 as $var01){
if($var01 == $_G['groupid']){
$zallowsay01 = 0;
showmessage('not_loggedin');
break;
}
}
$viewdontsay01 = explode(',',$zmcg01['donsay']);
foreach($viewdontsay01 as $var02){
if($var02 == $_G['groupid']){ $zallowsay01 = 0; break; }
}
$useadmin01 = explode(',',$zmcg01['manage']);
foreach($useadmin01 as $var03){
if($var03 == $_G['groupid']){ $checkboss01 = 1; break; }
}
$eac01 = array();
$intolang01 = DISCUZ_ROOT . './source/plugin/zmc_chat/' . $zmcg01['langpath'] . '.php';
require_once($intolang01);
$sea01 = array(); $rep01 = array(); $chatname01 = ''; $zguest01 = ''; $zchar01 = ''; $zthan01 = '';
if($zmcg01['langpath'] == 'langscgbk'){
$zchar01 = 'GBK';
}elseif($zmcg01['langpath'] == 'langtcbig5'){
$zchar01 = 'BIG5';
}else{
$zchar01 = 'UTF-8';
}
if(!empty($_G['username'])){
$chatname01 = $_G['username'];
if($zchar01 == 'BIG5'){
$zthan01 = ord(substr($chatname01,-1,1));
if($zthan01 == 92){ $chatname01 = $chatname01 . '&#12288;'; }
$sea01 = array('"','\'','\\\\','<','>');
$rep01 = array('','','\\','','');
}else{
$sea01 = array('"','\'','\\','<','>');
$rep01 = array('','','','','');
}
$chatname01 = str_replace($sea01,$rep01,$chatname01);
}else{
$chatname01 = $_G['group']['grouptitle']; $zguest01 = '7';
}
$bcolorbg01 = ''; $bcolorbg02 = ''; $bcolorbg03 = '';  $bpicbg01 = ''; $zchattitle05 = ''; $ftitle01 = '';
if(!empty($zmcg01['colorbg01'])){
$bcolorbg01 = ' background-color:' . $zmcg01['colorbg01'] . ';';
}
if(!empty($zmcg01['colorbg02'])){
$bcolorbg02 = ' background-color:' . $zmcg01['colorbg02'] . ';';
}
if(!empty($zmcg01['colorbg03'])){
$bcolorbg03 = ' background-color:' . $zmcg01['colorbg03'] . ';';
}
if(!empty($zmcg01['picbg01'])){
$bpicbg01 = ' background-image:url(./source/plugin/zmc_chat/' . $zmcg01['picbg01'] . '); background-repeat:no-repeat; background-attachment:scroll;';
}
$zchattitle05 = '<div style="height:20px;' . $bcolorbg02 . ' margin:5px 0 0 0; border:' . $zmcg01['color02'] . ' 1px solid; padding:3px 0 0 3px;">' . $zmcg01['ztitle'] . '</div>';
$ftitle01 = 'zchat';
include template('common/header');
include template('zmc_chat:zmcchat01');
include template('common/footer');
}

?>