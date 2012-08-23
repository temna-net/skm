<?php

if(!defined('IN_DISCUZ')){ exit('Access Denied'); }
class plugin_zmc_chat {
public $zfirst01 = false;
public $zfsit01 = 0;
public $zfidsit01 = 0;
public $zfid01 = 0;
public $outrun01 = '';
function ztalk01(){
$this->zfirst01 = true;
global $_G;
$zmcg01 = array();
$zmcg01 = $_G['cache']['plugin']['zmc_chat'];
if($zmcg01['chatswitch'] == '1'){
$runnext01 = 0;
if(empty($_G['fid'])){
if(($zmcg01['fsit01'] > 0) && ($zmcg01['fsit01'] < 4)){
$this->zfsit01 = $zmcg01['fsit01'];
$runnext01 = 1;
}
}else{
if(($zmcg01['ssit01'] > 0) && ($zmcg01['ssit01'] < 4)){
$this->zfidsit01 = $zmcg01['ssit01'];
$showfid01 = explode(',', $zmcg01['sfid01']);
foreach($showfid01 as $var04){
if($var04 == $_G['fid']){
$this->zfid01 = $_G['fid'];
$runnext01 = 1;
break;
}
}
}
}
}
if($runnext01 == 1){
$zallowview01 = 1;
$zallowsay01 = 1;
$checkboss01 = 0;
$dontvisit01 = explode(',',$zmcg01['donview']);
foreach($dontvisit01 as $var01){
if($var01 == $_G['groupid']){ $zallowview01 = 0; break; }
}
if($zallowview01 == 1){
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
$bcolorbg01 = ''; $bcolorbg02 = ''; $bcolorbg03 = '';  $bpicbg01 = ''; $show2h01 = ''; $show2w01 = ''; $ftitle01 = '';
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
if(empty($_G['fid'])){
$show2h01 = $zmcg01['fh01'];
$show2w01 = $zmcg01['fw01'];
$ftitle01 = 'zforum';
}else{
$show2h01 = $zmcg01['sh01'];
$show2w01 = $zmcg01['sw01'];
$ftitle01 = 'fid' . $_G['fid'];
}
include template('zmc_chat:zmcchat02');
$this->outrun01 = $run01;
}
}
}
}
class plugin_zmc_chat_forum extends plugin_zmc_chat {
function index_top(){
if($this->zfirst01 != true){ $this->ztalk01(); }
if($this->zfsit01 == 1){ return $this->outrun01; }
}
function index_middle(){
if($this->zfsit01 == 2){ return $this->outrun01; }
}
function index_bottom(){
if($this->zfsit01 == 3){ return $this->outrun01; }
}
function forumdisplay_top(){
if($this->zfirst01 != true){ $this->ztalk01(); }
if($this->zfid01 != 0){
if($this->zfidsit01 == 1){ return $this->outrun01; }
}
}
function forumdisplay_middle(){
if($this->zfid01 != 0){
if($this->zfidsit01 == 2){ return $this->outrun01; }
}
}
function forumdisplay_bottom(){
if($this->zfid01 != 0){
if($this->zfidsit01 == 3){ return $this->outrun01; }
}
}
}

?>