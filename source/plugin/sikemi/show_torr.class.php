<?php

//全局嵌入点类（必须存在）
class plugin_sikemi{
}
//全局脚本嵌入点类
class plugin_sikemi_forum extends plugin_sikemi{
	function forumdisplay_thread_output() {
		global $_G;
		$tids="";
		$arr=array();
		$j=0;
		foreach($_G['forum_threadlist'] as $v){
			$arr[$j]['tid']=$v['tid'];
			$tids.=",".$v['tid'];
			$j++;
		}
		$tids=substr($tids,1);
		if($tids=="")$tids="0";
		$query=DB::query("select tid,seeds,leechers,finished from ".DB::table('xbtit_files')." where tid in ({$tids})");
		while($row=DB::fetch($query)){
			for($i=0;$i<count($arr);$i++){
				if($row['tid']==$arr[$i]['tid']){
					$arr[$i]['seeds']=$row['seeds'];
					$arr[$i]['leechers']=$row['leechers'];
					$arr[$i]['finished']=$row['finished'];
					break;
				}
			}
		}
		
		foreach($arr as $row){
			if(empty($row)){
				$return[]="";
			}else{
				$return[]="<span style='color:#F00; font-weight:bold;'>{$row['seeds']}</span>&nbsp;<span style='color:#00F;font-weight:bold;'>{$row['leechers']}</span>&nbsp;<span style='color:#0C3;font-weight:bold;'>{$row['finished']}</span>";
			}
		}
		return $return;
	}
}

?>