<?php
if(!defined('IN_DISCUZ')) {
        exit('Access Denied');
} 
$uid = 0;
$isAdmin = $_G['adminid']==1;

if($isAdmin){
	$uid = isset($_GET['uid']) && $_GET['uid']>0 ? $_GET['uid'] : $_G['uid'];
}else{
	$uid = $_G['uid'];
}

$query=DB::query("select count(*) as count from ".DB::table('xbtit_history')." where uid={$uid} ");
$totals=DB::fetch($query);
$total=$totals['count'];
$ppp=25;//每页显示条数
$current_page=isset($_GET['page'])?$_GET['page']:1;
$start_limit = ($current_page - 1) * $ppp;

$query=DB::query("select a.*,b.subject from ".DB::table('xbtit_history')." a left join ".DB::table('forum_thread')." b on a.tid=b.tid where a.uid={$uid} order by a.makedate desc limit $start_limit ,$ppp ");

$i=0;
while($history=DB::fetch($query)){
	if($i%2==1){
		$historys[$i]['color']="#F2F2F2";
	}else{
		$historys[$i]['color']="#FFF";
	}
	$historys[$i]['subject']=$history['subject']== "" ? "该资源已经被删除" : $history['subject'] ;
	$historys[$i]['tid']=$history['tid'];
	$historys[$i]['makedate']=dgmdate($history['makedate'],"u");
	$historys[$i]['date']=dgmdate($history['date'],"u");
	$historys[$i]['uploaded']=sizecount($history['uploaded']);
	$historys[$i]['realup']=sizecount($history['realup']);
	$historys[$i]['downloaded']=sizecount($history['downloaded']);
	$historys[$i]['realdown']=sizecount($history['realdown']);
	$i++;
}
// $no=$historys[0]['count'];
$turnpage= multi($total, $ppp, $current_page, "home.php?mod=spacecp&ac=plugin&id=sicau_pt_x2:U_user_history&uid=$uid");
template('sicau_pt_x2:U_user_history');
?>