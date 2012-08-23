<?php
if(!defined('IN_DISCUZ')){
	exit('Access Denied');
}
//$TORRENTSDIR="torrent";//存储目录 weibin change

$dir="torrent"; 
list($y, $m, $d) = explode('-', date('Y-m-d')); 
!is_dir("$dir/$y") && mkdir("$dir/$y", 0777);
!is_dir("$dir/$y/$m") && mkdir("$dir/$y/$m", 0777);

$TORRENTSDIR = $dir.'/'.$y.'/'.$m;// by month


require_once ("source/plugin/sikemi/BDecode.php");
require_once ("source/plugin/sikemi/BEncode.php");
function_exists("sha1") or die("<font color=\"red\">".$language["NOT_SHA"]."</font></body></html>");

if ($_FILES["torrent"]["error"] != 4){
	is_uploaded_file($_FILES["torrent"]["tmp_name"]) or showmessage('torrent error2');
	$length=filesize($_FILES["torrent"]["tmp_name"]);
	// if ($length){
	$alltorrent = file_get_contents($_FILES["torrent"]["tmp_name"]);
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

if(isset($_POST['douban'])){
    $douban_url=$_POST['douban'];
    $pattern = '/(\d{7,8})/';
    preg_match($pattern, $douban_url, $matches);
    if(empty($matches)) {
        $douban_id="";
    }else {
        $douban_id =$matches[0];
    }
}else {
    $douban_id ='';
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

$query = DB::query("SELECT tid FROM ".DB::table('xbtit_files')." WHERE tid <> '$tid' AND info_hash='$hash' limit 1");
$row = DB::fetch($query);
if(!empty($row)){
	$str = '该资源('.$row['tid'].')已经被分享,<a href="forum.php?mod=viewthread&tid='.$row['tid'].'" target="_blank">查看这个资源</a>';
	require_once DISCUZ_ROOT.'./source/function/function_delete.php';
	deletethread($tid);
	showmessage($str);
	//此处需要删除发布的帖子~
}

//信息添加到外网
if($douban_id!='') {
    $sina_url="http://sikemi.sinaapp.com/insert.php?infohash={$hash}&douban={$douban_id}&size={$size}";
    curlrequest($sina_url);
}
$status =DB::query("REPLACE INTO  ".DB::table('xbtit_files')." (
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
`lastactive`,
`douban`
)
VALUES ('$hash',$tid,'$filename','$url','0000-00-00 00:00:00',{$size},'false',  '0',  '0',  '0',  '0',UNIX_TIMESTAMP(),'$douban_id'
);");

if ($status){
	$mf=@move_uploaded_file($_FILES["torrent"]["tmp_name"] , $TORRENTSDIR . "/" . $hash . ".btf");
	if (!$mf){
		DB::query("DELETE FROM ".DB::table('xbtit_files')." WHERE info_hash=\"$hash\"");
	}
	@chmod($TORRENTSDIR . "/" . $hash . ".btf",0766);
}

else{
	unlink($_FILES["torrent"]["tmp_name"]);
	require_once DISCUZ_ROOT.'./source/function/function_delete.php';
	deletethread($tid);
	showmessage('torrent error');
	//此处也是需要删除发布成功的帖子~
}

function curlrequest($url){
	$ch = curl_init(); // 初始化CURL句柄
	curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);//启用时会将服务器服务器返回的“Location:”放在header中递归的返回给服务器
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);// 设为TRUE把curl_exec()结果转化为字串，而不是直接输出
	curl_setopt($ch, CURLOPT_TIMEOUT, 3); // 超时时间
	$document = curl_exec($ch); //执行预定义的CURL
	curl_close($ch);
	return true;
}
?>
