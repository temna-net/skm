<?php
if(!defined('IN_DISCUZ')) {
        exit('Access Denied');
} 
if(isset($_POST['submit']) && isset($_GET['tid'])) {
    $tid=$_GET['tid'];
    $douban_url=$_POST['douban'];
    $pattern = '/(\d{7,8})/';
    preg_match($pattern, $douban_url, $matches);
    if(empty($matches)) {
        $douban_id="";
    }else {
        $douban_id =$matches[0];
    }
    
    //更新到外网代码
    $query=DB::query("select info_hash,size from ".DB::table('xbtit_files')." where tid={$tid} limit 1");
    $results=DB::fetch($query);
    if($douban_id!='') {
        $sina_url="http://sikemi.sinaapp.com/insert.php?infohash={$results['info_hash']}&douban={$douban_id}&size={$results['size']}";
        
        $ch = curl_init(); // 初始化CURL句柄
        curl_setopt($ch, CURLOPT_URL, $sina_url); //设置请求的URL
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);//启用时会将服务器服务器返回的“Location:”放在header中递归的返回给服务器
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);// 设为TRUE把curl_exec()结果转化为字串，而不是直接输出
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); // 超时时间
        $document = curl_exec($ch); //执行预定义的CURL
        curl_close($ch);
    }
    //更新到外网代码--结束
    //添加到任务记录数据表
    DB::query("INSERT INTO cdb_xbtit_douban_task (`tid` ,`uid` ) VALUES ( {$tid} , {$_G['uid']});");
    
    DB::query("update ".DB::table('xbtit_files')." set douban={$douban_id} where tid={$tid}");
    Header("Location:forum.php?mod=viewthread&tid={$tid}");
}

if(!isset($_GET['tid'])){
	showmessage("未指定帖子");
}else{
	$tid=$_GET['tid'];
}
$tid=$_GET['tid'];
$query=DB::query("select douban from ".DB::table('xbtit_files')." where tid={$tid} limit 1");
// $results=DB::fetch($query);
$row=DB::fetch($query);
$douban_id=$row['douban'];
if($douban_id=="" || $douban_id=="0") {
    $douban_url="";
}else {
    $douban_url="http://movie.douban.com/subject/".$douban_id."/";
}
include template('edit_douban_url', "sicau_pt_x2","source/plugin/sicau_pt_x2/template");
?>