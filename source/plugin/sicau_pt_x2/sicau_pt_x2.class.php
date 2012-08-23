<?php

/**
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: sicau_pt_x2.class.php 1 2011-9-25 14:39:00Z shenmao1989 $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class Plugin_Sicau_Pt {
	function __construct() {
	}
	//test
	function test(){
		global $_G;
		echo $_G['cache']['plugin']['sicau_pt_x2']['test'];
		echo('test ok');
	}
	//判断用户是否存在
	//@param id
	//@return true or false
	function xbt_userexist($id){
		$id=intval($id);
		if(!intval($id))
			return false;
		$sql="SELECT id FROM ".DB::table('xbtit_users')." WHERE id='$id'";
		$query = DB::query($sql);
		if(count($query))
			return true;
		return false;
	}
	//获取用户信息
	//@param id
	//@return array() or false
	function xbt_userinfo($id,$param='*'){
		$id=intval($id);
		if(!intval($id))
			return false;
		$sql="SELECT $param FROM ".DB::table('xbtit_users')." WHERE id='$id'";
		$query = DB::query($sql);
		if(count($query))
			return $query[0];
		return false;
	}
	//添加用户
	//@param id,name,pass,type
	//@return true or false
	function xbt_useradd($id,$name,$pass='6501qq',$type=3){
		$id=intval($id);
		$type=intval($type);
		
		if(!intval($id) || !$name)
			return false;
		if(xbt_userexist($id))
			return false;
		
		$sql="INSERT INTO ".DB::table('xbtit_users')." (id,username, password, random, id_level, email, style, language, flag, joined, lastconnect, pid, time_offset) VALUES ($id,'$name', '" . md5($pass) . "', ".rand(10000, 60000).", $type, '$email', 1, 19, 0, NOW(), NOW(),'".md5(uniqid(rand(),true))."', '8')";
		if(DB::query($sql))
			return true;
		return false;
	}
	//删除用户
	//@param id
	//@return true or false
	function xbt_userdel($id){
		$id=intval($id);
		
		if(!intval($id))
			return false;
		if(!xbt_userexist($id))
			return false;
		$sql="DELETE FROM ".DB::table('xbtit_users')." WHERE id=$id";
		if(DB::query($sql))
			return true;
		return false;
	}
	//编辑用户
	//@param id,name,pass,type
	//@return true or false
	//暂不实现！
	function xbt_useredit($id,$name,$pass,$type){
		$id=intval($id);
		$type=intval($type);
		
		if(!userexist($id))
			return false;
			
		return true;
	}
	//添加种子（此处为files表增加字段aid，atype字段，用于对应附件aid以及对应帖子的四种类型）
	//@param 附件数组（见discuz上传文件函数）
	//@return 0,1,2,3
	function xbt_torradd($attach){
		global $_G;
		//fb("进入addTorrent()！",FirePHP::INFO);
		require ("BDecode.php");
		require ("BEncode.php");
		$torrentURL=$_G['attachdir'].'/'.$attach['attachment'];
		$torrentName=$attach['filename'];
		$torrentAid=$attach['aid'];
		//fb("读取torrent文件：".$torrentAid,FirePHP::INFO);
		//fb("读取torrent文件：".$torrentURL,FirePHP::INFO);
		clearstatcache();
		$fd = fopen($torrentURL, "rb");
		$length=filesize($torrentURL);
		//fb("读取torrent文件：".$length.'Bytes',FirePHP::INFO);
		//读取文件到$alltorrent
		if ($length)
			$alltorrent = fread($fd, $length);
		else {
			return -1;
			//fb("读取torrent文件错误！",FirePHP::ERROR);
		
		}
		//用BDecode解开torrent文件，获取文件信息到$array
		$array = BDecode($alltorrent);
		if (!isset($array))
		{
			return -2;
			//BD解包错误
		}
		
		//fb("torrent announce信息！".$array["announce"],FirePHP::INFO);
		
		//将种子设为私有
		$array["info"]["private"]=1;
		
		//将种子重新打包成torrent计算sha1值
		$hash=sha1(BEncode($array["info"]));
		
		//种子是否存在
		if(xbt_torrexist($hash,$type='hash')){
			//errorlog('XBT',"(xbt_torradd)种子已存在：hash:".$hash." ".$type, 0);
			return -3;
			//种子已存在
		}
		//fb("torrent announce信息！".$hash,FirePHP::INFO);
		fclose($fd);
		
		   $filename = $torrentName;
		//设置torrent文件存储位置
			$url = $torrentURL;
		//fb("torrent announce信息！".$url,FirePHP::INFO);
		
		// filename not writen by user, we get info directly from torrent.
		if (strlen($filename) == 0 && isset($array["info"]["name"]))
		   $filename = mysql_escape_string(htmlspecialchars($array["info"]["name"]));
		
		// description not writen by user, we get info directly from torrent.
		if (isset($array["comment"]))
		   $info = mysql_escape_string(htmlspecialchars($array["comment"]));
		else
			$info = "no info";
		
		//fb("torrent announce信息！".$filename,FirePHP::INFO);
		
		if (isset($array["info"]) && $array["info"]) $upfile=$array["info"];
			else $upfile = 0;
		
		//多文件种子与单文件种子处理获取文件大小
		if (isset($upfile["length"]))
		{
		  $size = (float)($upfile["length"]);
		}
		else if (isset($upfile["files"]))
		{
			// multifiles torrent
			$size=0;
			foreach ($upfile["files"] as $file)
			{
				$size+=(float)($file["length"]);
			}
		}
		else
			$size = "0";
		
		if (!isset($array["announce"]))
			{
			//err_msg($language["ERROR"], $language["EMPTY_ANNOUNCE"]);
			//stdfoot();
			//return 'EMPTY_ANNOUNCE';
		}
		
		$categoria = intval(8);//取消分类，统一设置为8
		$anonyme=false;
		//fb("$discuz_uid：".$discuz_uid,FirePHP::INFO);
		$curuid=intval($attach['uid']);
		
		$announce=str_replace(array("\r\n","\r","\n"),"",$array["announce"]);
		
		//      if ((strlen($hash) != 40) || !verifyHash($hash))
		//      {
		//         echo("<center><font color=\"red\">".$language["ERR_HASH"]."</font></center>");
		//         endOutput();
		//      }
		//      if ($announce!=$BASEURL."/announce.php" && $EXTERNAL_TORRENTS==false)
		//判断announce是否合法
		//if (!in_array($announce,$TRACKER_ANNOUNCEURLS) && $EXTERNAL_TORRENTS==false)
		//{
		//	//return "NOT_ALOWED_ANNOUNCE";
		//}
		//      if ($announce!=$BASEURL."/announce.php")
			
		// maybe we find our announce in announce list??
		 $internal=false;
		 if (isset($array["announce-list"]) && is_array($array["announce-list"]))
			{
			for ($i=0;$i<count($array["announce-list"]);$i++)
				{
				if (in_array($array["announce-list"][$i][0],$TRACKER_ANNOUNCEURLS))
				  {
				   $internal = true;
				   continue;
				  }
				}
			}
		  //不对announce进行判断
		  $internal = true;
		  //fb("torrent 开始插入！".$hash.",".$filename.",".$url.",".$info.",".$size.",".$comment.",".$announce.",".$curuid.",".$hash.",",FirePHP::INFO);
		  if ($internal)
			{
			// ok, we found our announce, so it's internal and we will set our announce as main
			   $array["announce"]=$TRACKER_ANNOUNCEURLS[0];
			   $query = "INSERT INTO ".DB::table('xbtit_files')." (info_hash, filename, url, info, category, data, size, comment, uploader, bin_hash,aid,atype,lastactive) VALUES (\"$hash\", \"$filename\", \"$url\", \"$info\",0 + $categoria,NOW(), \"$size\", \"$comment\",$curuid,0x$hash, $torrentAid,0,UNIX_TIMESTAMP())";
			}
		  else
			  $query = "INSERT INTO ".DB::table('xbtit_files')." (info_hash, filename, url, info, category, data, size, comment,external,announce_url, uploader,anonymous, bin_hash,aid,atype,lastactive) VALUES (\"$hash\", \"$filename\", \"$url\", \"$info\",0 + $categoria,NOW(), \"$size\", \"$comment\",\"yes\",\"$announce\",$curuid,$anonyme,0x$hash, $torrentAid,0,UNIX_TIMESTAMP())";
		  //echo $query;
		  
		//fb("torrent 开始插入！".$query,FirePHP::INFO);
		  $db->query($query); //makeTorrent($hash, true);
		return 0;
	}

	//删除种子,此处不删除文件，文件由discuz处理
	//@param $aid
	//@return true or false
	function xbt_torrdel($id){
		$id=intval($id);
		//errorlog('XBT',"(xbt_torrdel)删除种子：aid:".$id, 0);
		if(!intval($id))
			return false;
		if(!xbt_torrexist($id))
			return false;
		$sql="DELETE FROM ".DB::table('xbtit_files')." WHERE aid=$id";
		if($db->query($sql))
			return true;
		return false;
	}
	//种子是否存在
	//@param $id 
	//@param $type aid 还是 hash查询 默认aid
	//@return true or false
	function xbt_torrexist($id,$type=''){
		if($type=='hash')
			$sql="SELECT * FROM ".DB::table('xbtit_files')." WHERE info_hash='$id'";
		else
			$sql="SELECT * FROM ".DB::table('xbtit_files')." WHERE aid='$id'";
		//errorlog('XBT',"(xbt_torrexist)检测种子存在：hash:".$id." ".$type, 0);
		$query = DB::query($sql);
		if(count($query))
			return true;
		return false;
	}
	//编辑种子
	//@param 暂无
	//@return true or false
	//暂不实现
	function xbt_torredit(){
			
		return true;
	}
	//获取tracker
	//@param id
	//@return string or false
	function xbt_gettracker($id){
		$t=xbt_userinfo($id,'pid');
		return  $t['pid'];
	}
	//刷新tracker
	//@param id
	//@return string or false
	function xbt_torreflush($id){
		$id=intval($id);
		
		if(!$id)
			return false;
		$newt=md5(uniqid(rand(),true));	
		$sql="UPDATE ".DB::table('xbtit_users')." SET pid='$newt' WHERE id=$id";
		if($db->query($sql))
			return $newt;
		return false;
	}
	//torrent文件详情，读取文件分析
	//@param $aid
	//@return array() or false
	function xbt_torrfileinfo($id){
		$sql="SELECT url FROM ".DB::table('xbtit_files')." WHERE aid='$id'";
		$query=DB::query($sql);
		$torrenturl=$query[0]['url'];
		if(!$torrenturl)
			return false;
		clearstatcache();
		require ("BDecode.php");
		require ("BEncode.php");
		$fd = fopen($torrentURL, "rb");
		$length=filesize($torrentURL);
		//fb("读取torrent文件：".$length.'Bytes',FirePHP::INFO);
		//读取文件到$alltorrent
		if ($length)
			$alltorrent = fread($fd, $length);
		else {
			return false;
			//fb("读取torrent文件错误！",FirePHP::ERROR);
		}
		//用BDecode解开torrent文件，获取文件信息到$array
		return BDecode($alltorrent);
	}
	//torrent文件详情，数据库分析
	//@param $id
	//@return array() or false
	function xbt_torrinfo($id,$type='',$param='*'){
		if($type=='hash')
			$sql="SELECT $param FROM ".DB::table('xbtit_files')." WHERE info_hash='$id'";
		else{
			$id=intval($id);
			if(!intval($id))
				return false;
			$sql="SELECT $param FROM ".DB::table('xbtit_files')." WHERE aid='$id'";
		}
		$query = DB::query($sql);
		if(count($query))
			return $query[0];
		return false;
	}
	//某一种子的peers信息
	//@param $id
	//@return array() or false
	function xbt_peersinfo($id,$param='*'){
		$id=intval($id);
		if(!intval($id))
			return false;
		$sql="SELECT info_hash FROM ".DB::table('xbtit_files')." WHERE aid='$id'";
		$query=DB::query($sql);
		$infohash=$query[0]['info_hash'];
		//$infohash=$db->result_first($sql);

		$sql="SELECT $param FROM ".DB::table('xbtit_peers')." WHERE infohash='$infohash'";
		$query = DB::query($sql);
		if(count($query))
			return $query[0];
		return false;
	}
	//更新种子类型
	//@param $id
	//@return true or false
	function xbt_settorrtype($id,$type=0){
		$id=intval($id);
		$type=$type;
		$sql="UPDATE ".DB::table('xbtit_files')." SET atype=$type WHERE aid=$id";
		return DB::query($sql);
	}
	//获取种子类型
	//@param $id
	//@return true or false
	function xbt_gettorrtype($id,$type=''){
	$id=intval($id);
	if($type=='hash')
		$sql="SELECT atype FROM ".DB::table('xbtit_files')." WHERE info_hash='$id'";
	else
		$sql="SELECT atype FROM ".DB::table('xbtit_files')." WHERE aid='$id'";
	$query=DB::query($sql);
	return $query[0]['atype'];
	}
	//跳转到种子下载页面
	//@param 暂无
	//@return
	function xbt_gettorrdown(){
	}
	
}

/*class plugin_cloudstat_forum extends plugin_sicau_pt {


}*/

?>