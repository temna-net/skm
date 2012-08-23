<?php
if(!defined('IN_DISCUZ')){
	exit('Access Denied');
}

$TORRENTSDIR="torrent";//存储目录

require_once ("source/plugin/sikemi/BDecode.php");
require_once ("source/plugin/sikemi/BEncode.php");
function_exists("sha1") or die("<font color=\"red\">".$language["NOT_SHA"]."</font></body></html>");

if ($_FILES["torrent"]["error"] != 4){
	$fd = fopen($_FILES["torrent"]["tmp_name"], "rb") or showmessage('torrent error1');
	is_uploaded_file($_FILES["torrent"]["tmp_name"]) or showmessage('torrent error2');
	$length=filesize($_FILES["torrent"]["tmp_name"]);
	// if ($length){
		$alltorrent = fread($fd, 290000);
	// }
	// else {
		// showmessage('torrent error '.$length);
		// exit();
	// }
	$array = BDecode($alltorrent);
	if (!isset($array)){
		showmessage('torrent error4');
		exit();
	 }
	if (!$array){
		showmessage('torrent error5');
		exit();
	}
	$array["info"]["private"]=1;
	$hash=sha1(BEncode($array["info"]));
	fclose($fd);
}

$filename = mysql_real_escape_string(htmlspecialchars($_FILES["torrent"]["name"]));

if (isset($hash) && $hash){
	$url = $TORRENTSDIR . "/" . $hash . ".btf";
}
else{
	$url = 0;
}

if (isset($array["info"]) && $array["info"]){
	$upfile=$array["info"];
}else{
	$upfile = 0;
}
if (isset($upfile["length"])){
	$size = (float)($upfile["length"]);
}else if (isset($upfile["files"])){
	// multifiles torrent
	$size=0;
	foreach ($upfile["files"] as $file){
		$size+=(float)($file["length"]);
	}
}else{
	$size = "0";
}
if (!isset($array["announce"])){
	showmessage('torrent error6');
	exit();
}
$query = DB::query("SELECT * FROM ".DB::table('xbtit_files')." WHERE info_hash='$hash' limit 1");
$row = DB::fetch($query);
if(!empty($row)){
	showmessage('该资源已经被分享');
}
$status =DB::query("INSERT INTO  ".DB::table('xbtit_files')." (
`info_hash` ,
`tid` ,
`filename` ,
`url` ,
`data` ,
`size` ,
`anonymous` ,
`dlbytes` ,
`seeds` ,
`leechers` ,
`finished` ,
`lastactive`
)
VALUES ('$hash',$tid,'$filename','$url','0000-00-00 00:00:00',{$size},'false',  '0',  '0',  '0',  '0',UNIX_TIMESTAMP()
);");

if ($status){
	$mf=@move_uploaded_file($_FILES["torrent"]["tmp_name"] , $TORRENTSDIR . "/" . $hash . ".btf");
	if (!$mf){
		DB::query("DELETE FROM ".DB::table('xbtit_files')." WHERE info_hash=\"$hash\"");
	}
	@chmod($TORRENTSDIR . "/" . $hash . ".btf",0766);
}

else{
	showmessage('torrent error');
	unlink($_FILES["torrent"]["tmp_name"]);
	die();
}

?>