<?php
/*
 * PT用户排行榜，主要用于检查作弊
 */
if(!defined('IN_DISCUZ') || $_G['adminid']!=1) {
        exit('Access Denied');
} 

$order = in_array($_GET['order'], array('count', 'up', 'down', 'uid')) ? $_GET['order'] : 'count';
$sql = "SELECT uid,SUM(extcredits3) AS up,SUM(extcredits4) AS down,count(uid) AS count FROM ".DB::table(common_credit_log)." where dateline > UNIX_TIMESTAMP()-86400 group by uid order by $order DESC";


$query=DB::query($sql);
$total=DB::num_rows($query);
$ppp=25;//每页显示条数
$current_page=isset($_GET['page'])?$_GET['page']:1;
$start_limit = ($current_page - 1) * $ppp;

$query=DB::query($sql." limit $start_limit ,$ppp ");

$i=0;
while($history=DB::fetch($query)){
	if($i%2==1){
		$historys[$i]['color']="#F2F2F2";
	}else{
		$historys[$i]['color']="#FFF";
	}
	if($history['count'] > 480 || $history['up']+$history['down'] > 200*1024){
		$historys[$i]['color']="#FF6";
	}
	if($history['count'] > 1000 || $history['up']+$history['down'] > 500*1024){
		$historys[$i]['color']="#FF6600";
	}
	$historys[$i]['uid'] = $history['uid'];
	$historys[$i]['count'] = $history['count'];
	$historys[$i]['up'] = sizecount($history['up']*1048576 + 0);
	$historys[$i]['down'] = sizecount($history['down']*1048576 + 0);
	$i++;
}
$turnpage= multi($total, $ppp, $current_page, "home.php?mod=spacecp&ac=plugin&id=sicau_pt_x2:U_user_rank&order=$order");
template('sicau_pt_x2:U_user_rank');
?>