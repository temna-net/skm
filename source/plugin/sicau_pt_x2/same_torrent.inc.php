<?php
if(!defined('IN_DISCUZ')) {
        exit('Access Denied');
} 

if(isset($_GET['doubanid']) && isset($_GET['tid'])){
	$douban_id = intval($_GET['doubanid']);
	$tid = intval($_GET['tid']);
    
    $query=DB::query("select tid,size,filename,seeds from ".DB::table('xbtit_files')." where douban={$douban_id} and tid!={$tid} order by seeds desc limit 10");
    // $results=DB::fetch($query);
    $html = "";
    while ($row = DB::fetch($query)) {
        $size  = sizecount($row["size"]);
        if(mb_strlen($row['filename']) > 30) {
            $row['filename'] = mb_substr($row['filename'],0,30,'utf-8');
        }
        $html .= "<tr><td><a href=\"http://bt.sicau.org/thread-{$row['tid']}-1-1.html\" target='_blank'>{$row['filename']}</a></td><td>{$row['seeds']}</td><td>{$size}</td></tr>";
    }
    if($html=="") {
        $html = "没有找到相似资源,去<a href='search.php' target='_blank'>搜索</a>试试吧";
        $has_same = 0;
    }else {
        $has_same = 1;
    }
}else{
    showmessage("未指定豆瓣ID");
}
include template('same_torrent', "sicau_pt_x2","source/plugin/sicau_pt_x2/template");
?>