<?php

function zmcfwtxt($opfile01,$das01,$modx01 = 'wb'){
$fw01 = @fopen($opfile01,$modx01);
if($fw01 == false){ exit('1111111111~~1111111111~~1111111111|&#31995;&#32113;|red|unable to create file.@@'); }
@flock($fw01,LOCK_EX);
if(@fwrite($fw01,$das01) === false){ exit('1111111111~~1111111111~~1111111111|&#31995;&#32113;|red|Do not write this file.@@'); }
@flock($fw01,LOCK_UN);
@fclose($fw01);
}
function zmcfrtxt($opfile02,$modx02 = 'rb'){
$frtxt02 = '';
$fr02 = @fopen($opfile02,$modx02);
@flock($fr02,LOCK_SH);
$frtxt02 = @fread($fr02,filesize($opfile02));
@flock($fr02,LOCK_UN);
@fclose($fr02);
return $frtxt02;
}
function zhtmrep($zrep01){
$sea01 = array('&','"','\'','<','>','\\','|','~~','@@','&amp;#');
$rep01 = array('&amp;','&quot;','&#039;','&lt;','&gt;','&#92;&#32;','&#124;','&#126;&#126;','&#64;&#64;','&#');
$zrep02 = str_replace($sea01, $rep01, $zrep01);
return $zrep02;
}
function zutf8tohtm($zconv01){
if(function_exists('mb_convert_encoding')){
$zconv02 = mb_convert_encoding($zconv01,'HTML-ENTITIES','UTF-8');
return $zconv02;
}else{
require_once('./zutoh.php');
$zconv02 = zutftohtm($zconv01);
return $zconv02;
}
}
$nowtime01 = ''; $outstr01 = array(); $outstr02 = ''; $num02 = ''; $zdatas02 = ''; $zload01 = ''; $zload02 = '';
$crdata01 = ''; $crdata02 = ''; $clast01 = ''; $zchar03 = ''; $zchar04 = '';
if(!empty($_POST['zchar02'])){$zchar03 = $_POST['zchar02']; }else{ exit(); }
if(!empty($_POST['zdatas01'])){
$zdatas02 = intval($_POST['zdatas01']);
if($zdatas02 < 2){ exit(); }
if($zdatas02 > 120){ $zdatas02 = 120; }
}else{ exit(); }
if(!empty($_POST['ftitle02'])){
$zload01 = './' . $_POST['ftitle02'] . '.php';
if(strlen($zload01) > 20){ exit(); }
$zload02 = './' . $_POST['ftitle02'] . 't.php';
}else{ exit(); }
$nowtime01 = time();
if(!empty($_POST['zfirst01'])){
if((is_file($zload01)) && (is_file($zload02))){
include($zload01);
$num02 = count($outstr01);
$outstr02 = $nowtime01 . '~~' . $outstr01[0][0] . '~~';
for($n=0; $n<$num02; $n++){
$outstr02 .= $outstr01[$n][0] . '|' . $outstr01[$n][2] . '@@';
}
}else{
$crdata01 = '<?php $outstr01[0]=array("' . $nowtime01 . '","111","&#31995;&#32113;|black|New data is created."); ?>';
zmcfwtxt($zload01,$crdata01,'wb');
$crdata02 = '<?php $clast01="' . $nowtime01 . '" ?>';
zmcfwtxt($zload02,$crdata02,'wb');
$outstr02 = $nowtime01 . '~~' . $nowtime01 . '~~' . $nowtime01 . '|&#31995;&#32113;|red|New data is created.@@';
}
$zchar04 = 'Content-type: text/html; charset=' . $zchar03;
header($zchar04);
header('Pragma: no-cache');
echo $outstr02;
exit();
}
if((!empty($_POST['zoldt01'])) && (!empty($_POST['zlastupt01']))){
$oldtime01 = ''; $lastuptime02 = ''; $sion01 = ''; $sion02 = ''; $num01 = '';
$oldtime01 = $_POST['zoldt01'];
$lastuptime02 = $_POST['zlastupt01'];
if((!empty($_POST['zcmtxt01'])) && (!empty($_POST['txtolor01'])) && (!empty($_POST['zname03']))){
$zcmtxt02 = ''; $zcmcolor02 = ''; $zname05 = ''; $errout01 = ''; $instr01 = '';
session_start();
$sion01 = session_id();
$sion02 = substr($sion01,0,3);
if(empty($sion02)){ exit(); }
include($zload01);
$num01 = count($outstr01);
if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()){
$zcmtxt02 = zhtmrep(trim($_POST['zcmtxt01']));
$zcmcolor02  = zhtmrep(trim($_POST['txtolor01']));
$zname05  = zhtmrep(trim($_POST['zname03']));
$zcmtxt02 = stripslashes($zcmtxt02);
$zcmcolor02 = stripslashes($zcmcolor02);
$zname05 = stripslashes($zname05);
}else{
$zcmtxt02 = zhtmrep(trim($_POST['zcmtxt01']));
$zcmcolor02  = zhtmrep(trim($_POST['txtolor01']));
$zname05  = zhtmrep(trim($_POST['zname03']));
}
if((strlen($zcmtxt02) > 360) || (strlen($zcmcolor02) > 16) || (strlen($zname05) > 45)){
$errout01 = $oldtime01 . '~~' . $lastuptime02 . '~~' . $nowtime01 . '|&#31995;&#32113;|red|Warning: The input string exceeds the maximum number of characters@@';
exit($errout01);
}
if(($zchar03 == 'GBK') || ($zchar03 == 'BIG5')){
$zname05 = zutf8tohtm($zname05);
$zcmtxt02 = zutf8tohtm($zcmtxt02);
}
$instr01 = '<?php $outstr01[0]=array("' . $nowtime01 . '","' . $sion02 . '","' . $zname05 . '|' .$zcmcolor02 . '|' . $zcmtxt02 . '");';
$outstr02 = $nowtime01 . '~~' . $nowtime01 . '~~' . $nowtime01 . '|' . $zname05 . '|' . $zcmcolor02 . '|' . $zcmtxt02 . '@@';
$k = 0;
for($n=0; $n<$num01; $n++){
$k++;
if($k > $zdatas02){ break; }
if(($outstr01[$n][0] == $oldtime01) && ($sion02 != $outstr01[$n][1])){
$outstr02 .= $outstr01[$n][0] . '|' . $outstr01[$n][2] . '@@';
}
if($outstr01[$n][0] > $oldtime01){
$outstr02 .= $outstr01[$n][0] . '|' . $outstr01[$n][2] . '@@';
}
$instr01 .= "\r\n" . '$outstr01[' . $k . ']=array("' . $outstr01[$n][0] . '","' . $outstr01[$n][1] . '","' . $outstr01[$n][2] . '");';
}
$k = 0;
$instr01 .= ' ?>';
zmcfwtxt($zload01,$instr01,'wb');
$crdata02 = '<?php $clast01="' . $nowtime01 . '" ?>';
zmcfwtxt($zload02,$crdata02,'wb');
}else{
include($zload02);
if($lastuptime02 == $clast01){
$outstr02 = $nowtime01 . '~~' . $clast01 . '~~';
}else{
session_start();
$sion01 = session_id();
$sion02 = substr($sion01,0,3);
if(empty($sion02)){ exit(); }
include($zload01);
$num01 = count($outstr01);
$outstr02 = $nowtime01 . '~~' . $outstr01[0][0] . '~~';
for($n=0; $n<$num01; $n++){
if(($outstr01[$n][0] == $oldtime01) && ($sion02 != $outstr01[$n][1])){
$outstr02 .= $outstr01[$n][0] . '|' . $outstr01[$n][2] . '@@';
}
if($outstr01[$n][0] > $oldtime01){
$outstr02 .= $outstr01[$n][0] . '|' . $outstr01[$n][2] . '@@';
}
}
}
}
$zchar04 = 'Content-type: text/html; charset=' . $zchar03;
header($zchar04);
header('Pragma: no-cache');
echo $outstr02;
}
exit();

?>