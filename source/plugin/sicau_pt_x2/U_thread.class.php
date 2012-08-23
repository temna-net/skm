<?php

/*
	[Discuz!] (C)2001-2009 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: homegrids.class.php 20541 2009-10-09 00:34:37Z monkey $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class threadplugin_sicau_pt_x2 {

	var $name = '发布资源';			//主题类型名称
	var $iconfile = 'source/plugin/sicau_pt_x2/images/sikemi.gif';		//images/icons/ 目录下新增的主题类型图片文件名
	var $buttontext = '发布资源';		//发帖时按钮文字

	function newthread($fid, $tid) {
		global $_G;
		$query=DB::query("select pid from ".DB::table('xbtit_users')." where uid={$_G['uid']} limit 1");
		$results=DB::fetch($query);
		if(empty($results)){
			$pid=md5(uniqid(rand(),true));	
			DB::query("insert into ".DB::table('xbtit_users')." (uid,pid) values ({$_G['uid']},'{$pid}')");
		}else{
			$pid=$results['pid'];
		}
		include('./xbt/config.inc.php');
		if ($fid==141 || $fid==6 || $fid==23 || $fid==117) {
            $html= <<<EOB
            <div style="border:dashed 4px #ccc;padding:10px 10px 10px 10px;margin-bottom:10px">
            <p>您的tracker地址为：<span style="color:#F00;">{$tracker_url}{$pid}</span>
            <span class="xw0 xs1 xg1">
            <a title="复制tracker地址" href="javascript:setCopy('{$tracker_url}{$pid}', '复制tracker地址成功');">[复制链接]</a></span>
            </p>
            种子文件：<input type='file' name='torrent' style="width:250px;"/>&nbsp;不会做种？<a href="{$help_url}" target="_blank" style="color:#00F;">这里有教程</a>&nbsp;<br/>
                豆瓣链接：<input type="text" id="douban_url" name="douban" style="width:250px;" /> <a href="http://movie.douban.com/" target="_blank" style="color:#00F;">去豆瓣搜索</a>
            </div>
EOB;
        }else {
            $html= <<<EOB
            <div style="border:dashed 4px #ccc;padding:10px 10px 10px 10px;margin-bottom:10px">
            <p>您的tracker地址为：<span style="color:#F00;">{$tracker_url}{$pid}</span>
            <span class="xw0 xs1 xg1">
            <a title="复制tracker地址" href="javascript:setCopy('{$tracker_url}{$pid}', '复制tracker地址成功');">[复制链接]</a></span>
            </p>
            <p style="margin-top:10px;">
            不会做种？<a href="{$help_url}" target="_blank" style="color:#00F;">这里有教程</a>&nbsp;种子文件:&nbsp;<input type='file' name='torrent' size='30' /></p>
            </div>
EOB;
        }
        return $html;
	}

	function newthread_submit($fid, $tid) {
		if($_FILES['torrent']['size'] == 0){
			showmessage('未选择种子文件');
		}elseif(substr($_FILES['torrent']['name'],-7)!='torrent'){
			showmessage('选择文件不合法');
		}
	}

	function newthread_submit_end($fid,$tid) {
		include('./source/plugin/sicau_pt_x2/upload.inc.php');
	}

	function editpost($fid, $tid) {
		global $_G;
		$query=DB::query("select pid from ".DB::table('xbtit_users')." where uid={$_G['uid']} limit 1");
		$results=DB::fetch($query);
		if(empty($results)){
			$pid=md5(uniqid(rand(),true));	
			DB::query("insert into ".DB::table('xbtit_users')." (uid,pid) values ({$_G['uid']},'{$pid}')");
		}else{
			$pid=$results['pid'];
		}
		include('./xbt/config.inc.php');
		return <<<EOB
		<div style="border:dashed 4px #ccc;padding:10px 10px 10px 10px;margin-bottom:10px">
		<p>您的tracker地址为：<span style="color:#F00;">{$tracker_url}{$pid}</span>
		<span class="xw0 xs1 xg1">
		<a title="复制tracker地址" href="javascript:setCopy('{$tracker_url}{$pid}', '复制tracker地址成功');">[复制链接]</a></span>
		</p><p style="margin-top:10px;">
		不会做种？<a href="{$help_url}" target="_blank" style="color:#00F;">这里有教程</a>&nbsp;种子文件:&nbsp;<input type='file' name='torrent' size='30' />(留空为不修改种子！)</p>
		<input type='hidden' name='edittorrent' value=1 />
		</div>
EOB;
	}

	function editpost_submit($fid, $tid) {

	}

	function editpost_submit_end($fid, $tid) {
		if($_FILES['torrent']['size'] != 0){
			include('./source/plugin/sicau_pt_x2/upload.inc.php');
		}
	}

	function newreply_submit_end($fid, $tid) {

	}
	
	function viewthread($tid) {
		global $_G;
		$query=DB::query("select * from ".DB::table('xbtit_files')." where tid={$tid} limit 1");
		$results=DB::fetch($query);
		$filename=cutstr($results['filename'],40,"...");
		$lastactive=dgmdate($results['lastactive'],"u");
		$size=sizecount($results['size']);
        $douban_id = $results['douban'];
        
        $same_torrent = '';
        
        if ($douban_id!="" && $douban_id!=0){
            function get_douban_info($douban_id){
                 $file_name='tmp/douban_files/'.$douban_id.'.tmp';
                 if (file_exists($file_name)) {
                     $response = file_get_contents($file_name);
                 }else {
                     $api_url='http://api.douban.com/movie/subject/'.$douban_id.'?alt=json';
                     $curlObj = curl_init();
                     curl_setopt($curlObj, CURLOPT_URL, $api_url);
                     // curl_setopt($curlObj, CURLOPT_CONNECTTIMEOUT, 9);//超时时间
                     curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
                     curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
                     curl_setopt($curlObj, CURLOPT_HEADER, 0);
                     curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
                     $response = curl_exec($curlObj);
                     curl_close($curlObj);
                     file_put_contents($file_name,$response);
                 }
                 $json = json_decode($response,1);
                 if($json!="wrong subject id" && is_array($json)) {
                    return $json;
                 }else {
                    return false;
                 }
            }
            $edit_douban_link = '';
            $same_torrent = "<a href='plugin.php?id=sicau_pt_x2:same_torrent&doubanid={$douban_id}&tid={$tid}' class='xw0 xs1 xg1' onclick=\"showWindow('torrentinfo', this.href);return false;\">[相似资源]</a>";
            $movie_info=get_douban_info($douban_id);
            if($movie_info!=false){
                
                $douban_url='http://movie.douban.com/subject/'.$douban_id;
                $douban_img_url=$movie_info['link'][2]['@href'];
                $douban_img_url=str_replace("spic","mpic",$douban_img_url);
                $pingfen=$movie_info['gd:rating']['@average'];
                $daoyan = $bianju = $zhuyan = $leixing = $guojia = $yuyan = $shangyingriqi = $youming = $imdb = "";
                foreach($movie_info['db:attribute'] as $v) {
                    if($v['@name']=='director') {
                        $daoyan.=$v['$t'];
                        continue;
                    }
                    if($v['@name']=='writer') {
                        $bianju.=$v['$t']." / ";
                        continue;
                    }
                    if($v['@name']=='cast') {
                        $zhuyan.=$v['$t']." / ";
                        continue;
                    }
                    if($v['@name']=='movie_type') {
                        $leixing.=$v['$t']." / ";
                        continue;
                    }
                    if($v['@name']=='country') {
                        $guojia.=$v['$t'];
                        continue;
                    }
                    if($v['@name']=='language') {
                        $yuyan.=$v['$t'];
                        continue;
                    }
                    if($v['@name']=='pubdate') {
                        $shangyingriqi.=$v['$t'];
                        continue;
                    }
                    if($v['@name']=='aka') {
                        $youming.=$v['$t']." / ";
                        continue;
                    }
                    if($v['@name']=='imdb') {
                        $imdb.=$v['$t'];
                    }
                }
                
                $douban_info_html= <<<EOB
                <div style="border:dashed 4px #ccc;padding-bottom:10px;margin-bottom:20px;height:auto">
                <table border="0">
                <tr>
                    <td width="520">
                    <div id="info" style="width:500px;height:auto;padding:10px 10px 0px 10px;">
                        <table border="0" cellpadding="5">
                             <tr >
                                <td width="80"><a href="{$douban_url}" style="color:#09C" target="_blank">电影信息</a></td>
                                <td></td>
                            </tr>
                        
                            <tr >
                                <td width="80">导演：</td>
                                <td>{$daoyan}</td>
                            </tr>
                            <tr>
                                <td>编剧：</td>
                                <td>{$bianju}</td>
                            </tr>
                            <tr>
                                <td valign="top" >主演：</td>
                                <td>{$zhuyan}</td>
                            </tr>
                            <tr>
                                <td>类型：</td>
                                <td>{$leixing}</td>
                            </tr>
                             <tr>
                                <td>制片国家：</td>
                                <td>{$guojia}</td>
                            </tr>
                             <tr>
                                <td>语言：</td>
                                <td>{$yuyan}</td>
                            </tr>
                            <tr>
                                <td valign="top" >又名：</td>
                                <td>{$youming}</td>
                            </tr>
                            <tr>
                                <td>IMDb链接:</td>
                                <td><a href="{$imdb}" target="_blank">{$imdb}</a></td>
                            </tr>
                            <tr>
                                <td>豆瓣评分:</td>
                                <td>{$pingfen}</td>
                            </tr>
                          
                        </table>
                    </div>
                    </td>
                    <td valign="top" align="center">
                        <div id="mainpic" style="width:180px;height:auto;padding:40px 10px 0px 20px;">
                           <a class="nbg" href="http://movie.douban.com/subject/{$douban_id}/photos?type=R" title="点击看更多海报" target="_blank">
                           <img src="{$douban_img_url}" title="点击看更多海报" />
                           </a>
                        </div>
                    </td>
                </tr>
                </table>
                </div>
EOB;
            }else {
                $douban_info_html="";
            }

        }else {
            $douban_info_html="";
            if($_G['forum']['fid']=='6' || $_G['forum']['fid']=='23' || $_G['forum']['fid']=='117') {
                $edit_douban_link = "<a href=\"plugin.php?id=sicau_pt_x2:edit_douban_url&tid={$tid}\" class='xw0 xs1 xg1' onclick=\"showWindow('doubanurl', this.href);return false;\" >[编辑豆瓣链接]</a>";
            }else {
                $edit_douban_link ="";
            }
            
        }
		include('xbt/config.inc.php');
		$query=DB::query("select displayorder,highlight from ".DB::table('forum_thread')." where tid={$tid} limit 1");
		$rs=DB::fetch($query);
		if($rs['displayorder']>0){
			$up_image="up".$upload_weight['top'].".gif";
			$down_image="down".$down_weight['top'].".gif";
		}elseif($rs['highlight']>0){
			$up_image="up".$upload_weight['highlight'].".gif";
			$down_image="down".$down_weight['highlight'].".gif";
		}else{
			$up_image="up".$upload_weight['normal'].".gif";
			$down_image="down".$down_weight['normal'].".gif";
		}
		if($_G['uid']>0){
            if($results['seeds']=='0') {
                $torrent_info_html = <<<EOB
                <div style="border:dashed 4px #ccc;padding-bottom:10px;margin-bottom:20px;">
                <span style="font-family: 微软雅黑;margin-top:10px;padding-left:10px;">
                    种子: <span style="color: red;">{$results['seeds']}</span>
                    下载中: <span style="color: red;">{$results['leechers']}</span>
                    完成: <span style="color: red;">{$results['finished']}</span>
                    大小: <span style="color: red;">{$size}</span>
                    最近活动时间: <span style="color: red;">{$lastactive}</span>
                    <img title="下载权值" src="source/plugin/sicau_pt_x2/images/{$down_image}" alt="normal" align="absmiddle"></span>
                    <img title="上传权值" src="source/plugin/sicau_pt_x2/images/{$up_image}" alt="normal" align="absmiddle">
                    
                </span><br/>
                <span style="padding-left:10px;">
                    <a href="http://www.utorrent.com/intl/zh_cn/" target="_blank"><img title="请使用utorrent打开种子文件" src="source/plugin/sicau_pt_x2/images/torrent.gif"align="absmiddle"></a>
                    <a onclick="if(!confirm('此资源种子数为0，下载可能没有速度，是否继续下载？（你也可以联系上传者和或已下载的觅友哦！）')){return false;}" style="font-weight: bold;color:#09C" title="{$results['filename']}" href="plugin.php?id=sicau_pt_x2:download&tid={$tid}">{$filename}</a>&nbsp;(<a href="plugin.php?id=sicau_pt_x2:torr_info&tid={$tid}" style="color:#09C" onclick="showWindow('torrentinfo', this.href);return false;">种子详情</a>) 
                    &nbsp;<a href="plugin.php?id=sicau_pt_x2:U_thread_history&tid={$tid}" class="xw0 xs1 xg1" onclick="showWindow('torrentinfo', this.href);return false;" title="如果没有种子，可以联系他们哦！">[哪些人下载过]</a>
                    {$edit_douban_link}&nbsp;{$same_torrent}
                </span>
                </div>
EOB;
            } else {
                $torrent_info_html =  <<<EOB
                    <div style="border:dashed 4px #ccc;padding-bottom:10px;margin-bottom:20px;">
                    <span style="font-family: 微软雅黑;margin-top:10px;padding-left:10px;">
                        种子: <span style="color: red;">{$results['seeds']}</span>
                        下载中: <span style="color: red;">{$results['leechers']}</span>
                        完成: <span style="color: red;">{$results['finished']}</span>
                        大小: <span style="color: red;">{$size}</span>
                        最近活动时间: <span style="color: red;">{$lastactive}</span>
                        <img title="下载权值" src="source/plugin/sicau_pt_x2/images/{$down_image}" alt="normal" align="absmiddle"></span>
                        <img title="上传权值" src="source/plugin/sicau_pt_x2/images/{$up_image}" alt="normal" align="absmiddle">
                        
                    </span><br/>
                    <span style="padding-left:10px;">
                        <a href="http://www.utorrent.com/intl/zh_cn/" target="_blank"><img title="请使用utorrent打开种子文件" src="source/plugin/sicau_pt_x2/images/torrent.gif"align="absmiddle"></a>
                        <a style="font-weight: bold;color:#09C" title="{$results['filename']}" href="plugin.php?id=sicau_pt_x2:download&tid={$tid}">{$filename}</a>&nbsp;(<a href="plugin.php?id=sicau_pt_x2:torr_info&tid={$tid}" style="color:#09C" onclick="showWindow('torrentinfo', this.href);return false;">种子详情</a>) 
                        &nbsp;<a href="plugin.php?id=sicau_pt_x2:U_thread_history&tid={$tid}" class="xw0 xs1 xg1" onclick="showWindow('torrentinfo', this.href);return false;" title="如果没有种子，可以联系他们哦！">[哪些人下载过]</a>
                        {$edit_douban_link}&nbsp;{$same_torrent}
                    </span>
                    </div>
EOB;
            }
		}else{
			$torrent_info_html =  <<<EOB
			<div style="border:dashed 4px #ccc;padding-bottom:10px;margin-bottom:20px;">
			<span style="font-family: 微软雅黑;margin-top:10px;padding-left:10px;">
				种子: <span style="color: red;">{$results['seeds']}</span>
				下载中: <span style="color: red;">{$results['leechers']}</span>
				完成: <span style="color: red;">{$results['finished']}</span>
				大小: <span style="color: red;">{$size}</span>
				最近活动时间: <span style="color: red;">{$lastactive}</span>
				<img title="下载权值" src="source/plugin/sicau_pt_x2/images/{$down_image}" alt="normal" align="absmiddle"></span>
				<img title="上传权值" src="source/plugin/sicau_pt_x2/images/{$up_image}" alt="normal" align="absmiddle">
				
			</span><br/>
			<span style="padding-left:10px;">
				您还没有登录，登陆后才可以下载哦！<a href="member.php?mod=logging&amp;action=login" onclick="showWindow('login', this.href)" class="xi2">登录</a>&nbsp;|
				<a href="member.php?mod=register" class="xi2">立即注册</a>
			</span>
			</div>
EOB;
		}
        return $torrent_info_html.$douban_info_html;
	}
}

?>