<?php

function utf8tounis($zc){
switch(strlen($zc)){
case 1:
return ord($zc);
case 2:
$n = (ord($zc[0]) & 0x3f) << 6;
$n += ord($zc[1]) & 0x3f;
return $n;
case 3:
$n = (ord($zc[0]) & 0x1f) << 12;
$n += (ord($zc[1]) & 0x3f) << 6;
$n += ord($zc[2]) & 0x3f;
return $n;
case 4:
$n = (ord($zc[0]) & 0x0f) << 18;
$n += (ord($zc[1]) & 0x3f) << 12;
$n += (ord($zc[2]) & 0x3f) << 6;
$n += ord($zc[3]) & 0x3f;
return $n;
}
}
function zutftohtm($t01){
$r01 = '';
while($t01 != '') {
$f01 = ord(substr($t01, 0, 1));
if($f01 < 128){
$r01 = $r01 . substr($t01, 0, 1);
$t01 = substr($t01, 1, strlen($t01));
}elseif(($f01 >= 192) && ($f01 < 224)){
$r01 = $r01 . "&#" . utf8tounis(substr($t01, 0, 2)) . ";";
$t01 = substr($t01, 2, strlen($t01));
}elseif(($f01 >= 224) && ($f01 < 240)){
$r01 = $r01 . "&#" . utf8tounis(substr($t01, 0, 3)) . ";";
$t01 = substr($t01, 3, strlen($t01));
}elseif(($f01 >= 240) && ($f01 < 248)){
$r01 = $r01 . "&#" . utf8tounis(substr($t01, 0, 4)) . ";";
$t01 = substr($t01, 4, strlen($t01));
}else{
$t01 = '';
}
}
return $r01;
}

?>