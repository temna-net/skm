<?php

function zmcfwtxt($opfile01,$das01,$modx01 = 'wb'){
$fw01 = @fopen($opfile01,$modx01);
if($fw01 == false){ exit('unable to create file'); }
@flock($fw01,LOCK_EX);
if(@fwrite($fw01,$das01) === false){ exit('Do not write this file'); }
@flock($fw01,LOCK_UN);
@fclose($fw01);
}
$dzcore02 = '';
$dzcore02 = substr(dirname(__FILE__),0,-15) . './class/class_core.php';
require_once($dzcore02);
$discuz = &discuz_core::instance();
$discuz -> init();
loadcache('plugin');
$zmcg02 = array();
$zmcg02 = $_G['cache']['plugin']['zmc_chat'];
$zallowboss = 0;
$useadmin02 = array();
$useadmin02 = explode(',',$zmcg02['manage']);
foreach($useadmin02 as $var04){
if($var04 == $_G['groupid']){ $zallowboss = 1; break; }
}
if($zallowboss != 1){ exit(); }
$gohome01 = substr($_G['siteurl'],0,-23);
$zload02 = ''; $zload03 = '';
if(!empty($_GET['ftitle02'])){
$zload02 = './' . $_GET['ftitle02'] . '.php';
$zload03 = $_GET['ftitle02'];
}
if(($zmcg02['langpath'] == 'langscgbk') || ($zmcg02['langpath'] == 'langscutf8')){
$trad[0] = '&#21024;&#38500;';
$trad[1] = '&#30830;&#23450;';
$trad[2] = '&#29992;&#36884;&#65306;' . $zload02 . ' &#20869;&#30340;&#35759;&#24687;&#35760;&#24405;,&#21333;&#31508;&#25110;&#22810;&#31508;&#21024;&#38500; (&#25171;&#21246;&#21518;,&#28857;&#21024;&#38500;&#25353;&#38062;)';
$trad[3] = '&#21024;&#38500; ' . $zload02 . ' &#20869;&#25152;&#26377;&#35759;&#24687;&#35760;&#24405; (&#25171;&#21246;&#21518;,&#28857;&#30830;&#23450;&#25191;&#34892;)';
$trad[4] = '&#37325;&#24314;&#34920;&#24773;&#31526;&#21495; (&#25171;&#21246;&#21518;,&#28857;&#30830;&#23450;&#25191;&#34892;)';
$trad[5] = '<a href="' . $gohome01 . '">&#25353;&#27492;&#36820;&#22238;&#39318;&#39029;</a>';
$trad[6] = '&#21024; &#38500; &#23436; &#25104; &#65281;';
$trad[7] = '&#25152; &#26377; &#35760; &#24405; &#30340; &#25968; &#25454; &#24050; &#34987; &#21024; &#38500; &#65281;';
$trad[8] = '&#37325; &#24314; &#34920; &#24773; &#31526; &#21495; &#23436; &#25104; &#65281;';
}else{
$trad[0] = '&#21024;&#38500;';
$trad[1] = '&#30906;&#23450;';
$trad[2] = '&#29992;&#36884;&#65306;' . $zload02 . ' &#20839;&#30340;&#35338;&#24687;&#35352;&#37636;,&#21934;&#31558;&#25110;&#22810;&#31558;&#21034;&#38500; (&#25171;&#21246;&#24460;,&#40670;&#21034;&#38500;&#25353;&#37397;)';
$trad[3] = '&#21024;&#38500; ' . $zload02 . ' &#20839;&#25152;&#26377;&#35338;&#24687;&#35352;&#37636; (&#25171;&#21246;&#24460;,&#40670;&#30906;&#23450;&#22519;&#34892;)';
$trad[4] = '&#37325;&#24314;&#34920;&#24773;&#31526;&#34399; (&#25171;&#21246;&#24460;,&#40670;&#30906;&#23450;&#22519;&#34892;)';
$trad[5] = '<a href="' . $gohome01 . '">&#25353;&#27492;&#36820;&#22238;&#39318;&#38913;</a>';
$trad[6] = '&#21034; &#38500; &#23436; &#25104; &#65281;';
$trad[7] = '&#25152; &#26377; &#35352; &#37636; &#30340; &#25976; &#25818; &#24050; &#34987; &#21034; &#38500; &#65281;';
$trad[8] = '&#37325; &#24314; &#34920; &#24773; &#31526; &#34399; &#23436; &#25104; &#65281;';
}
$zsmile04 = '';
if((!empty($_POST['ckbox01'])) && ($_GET['zck02'] == 'yesdoit')){
if($_POST['ckbox01'] == 'crsmile01'){
$zsmile01 = './zsmile/';
$zsmile02 = './source/plugin/zmc_chat/zsmile/';
$zsmile04 = 'var zsmile03 = "';
if(is_dir($zsmile01)){
if($zhandle01 = opendir($zsmile01)){
while(($zfile01 = readdir($zhandle01)) !== false){
if(($zfile01 != '.' ) && ($zfile01 != '..')){
$ckimg01 = substr($zfile01,-4,4);
if(($ckimg01 == '.gif') || ($ckimg01 == '.png') || ($ckimg01 == '.jpg')){
$zsmile04 .= '<a href=javascript:onclick=zintosmile(\'[' . $zfile01 . ']\');><img src=\'' . $zsmile02 . $zfile01 . '\' border=\'0\'></a> ';
}
}
}
closedir($zhandle01);
}
}
$zsmile04 .='";';
zmcfwtxt('./zmcsmile.js',$zsmile04,'wb');
echo '<p>&nbsp;</p><p style="color:#0000FF; font-size:24px;">' . $trad[8] . '</p>';
}
}
$nowtime05 = time();
$deldata05 = '';
if((!empty($_POST['ckbox02'])) && ($_GET['zck02'] == 'yesdoit')){
if($_POST['ckbox02'] == 'deldata02'){
$deldata05 = '<?php $outstr01[0]=array("' . $nowtime05 . '","111","&#31995;&#32113;|undefined|All data is deleted."); ?>';
zmcfwtxt($zload02,$deldata05,'wb');
echo '<p>&nbsp;</p><p style="color:#0000FF; font-size:24px;">' . $trad[7] . '</p>';
}
}
$ckdel02 = array();
$txt01 = '';
$txt02 = '';
if((!empty($_POST['ckdel'])) && ($_GET['zck02'] == 'yesdoit')){
$outstr01 = array();
include($zload02);
$ckdel02 = $_POST['ckdel'];
$arrnum01 = count($outstr01);
$f = 0;
for($i=0; $i<$arrnum01; $i++){
$s = 1;
foreach($ckdel02 as $var06){
if($i == $var06){ $s = 0; break; }
}
if($s == 1){
$txt02 .= '$outstr01[' . $f . ']=array("' . $outstr01[$i][0] . '","' . $outstr01[$i][1] . '","' . $outstr01[$i][2] . '");' . "\r\n";
$f++;
}
}
if($txt02 == ''){
$txt02 = '$outstr01[0]=array("' . $nowtime05 . '","111","&#31995;&#32113;|undefined|All data is deleted."); ';
}
$txt01 = '<?php ' . $txt02 . '?>';
zmcfwtxt($zload02,$txt01,'wb');
echo '<p>&nbsp;</p><p style="color:#0000FF; font-size:24px;">' . $trad[6] . '</p>';
}
$outstr01 = array();
include($zload02);
$showtxt01 = array();
$showtxt02 = '';
$k = 0;
foreach($outstr01 as $var05){
$showtxt01 = explode('|',$var05[2]);
$showtxt02 .= '<input type="checkbox" name="ckdel[]" value="' . $k . '"><font color="royalblue" size="1">' . $showtxt01[0] . '</font> <font color="#CC9900" size="1">&gt;&gt;</font> ' . $showtxt01[2] . '<br>';
$k++;
}
?>
<html>
<head>
<meta http-equiv="pragma" content="no-cache">
<style type="text/css">
<!--
* {
margin:0;
padding:0;
}
input {
margin:3px 3px 3px 3px;
vertical-align:-2px;
font-size: 16px;
}
-->
</style>
</head>
<body>
<div style="width:auto; margin:30px 10px 50px 10px; border:#999999 1px solid; padding:5px;">
<form name="forma1" method="post" action="./zadmin.php?ftitle02=<?php echo $zload03; ?>&zck02=yesdoit">
<div style="margin:5px; border:#999999 1px solid; padding:5px; font-size:12px;">
<?php echo $showtxt02; ?>
<br><input type="submit" name="Submit" value="<?php echo $trad[0]; ?>"><br><br>
<?php echo $trad[2]; ?>
</div>
<div style="margin:10px 5px 5px 5px; border:#999999 1px solid; padding:5px;">
<input type="checkbox" name="ckbox02" value="deldata02"><?php echo $trad[3]; ?> <input type="submit" name="Submit" value="<?php echo $trad[1]; ?>">
</div>
<div style="margin:10px 5px 5px 5px; border:#999999 1px solid; padding:5px;">
<input type="checkbox" name="ckbox01" value="crsmile01"><?php echo $trad[4]; ?> <input type="submit" name="Submit" value="<?php echo $trad[1]; ?>">
</div>
<div style="margin:10px 5px 5px 5px; border:#999999 1px solid; padding:5px;">
<br><?php echo $trad[5]; ?><br><br>
</div>
</form>
</div>
</body>
</html>
<?php exit(); ?>