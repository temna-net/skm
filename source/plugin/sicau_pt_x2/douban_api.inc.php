<?php
if(!defined('IN_DISCUZ')) {
        exit('Access Denied');
} 

if(!isset($_GET['douban_id'])){
	die("error 1");
}else{
	$douban_id=$_GET['douban_id'];
}
$query=DB::query("select tid,filename,seeds,size from ".DB::table('xbtit_files')." where douban={$douban_id} order by seeds desc limit 8");
while($row=DB::fetch($query)){
	$temp=array('title'   => substr($row['filename'],0,-8),
				'filesize'=>$row['size'],
				'seeds'   =>$row['seeds'],
				'url'     =>"http://bt.sicau.me/thread-{$row['tid']}-1-1.html");
	$results[]=$temp;
}
// var_dump($results);

$sikemi=array();
$sikemi['m']=count($results);//结果数量
$sikemi['result'] = $results;
$sikemi2['sp']    = $sikemi;
// var_dump($sikemi2);
$json=json_encode($sikemi2);
echo "iaskSearchResult=".$json;

?>
