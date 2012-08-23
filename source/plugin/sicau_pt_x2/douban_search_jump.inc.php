<?php
if(!defined('IN_DISCUZ')) {
        exit('Access Denied');
} 
//请求URL
$url="http://bt.sicau.me/search.php?mod=forum";
//POST提交数据，可用HTTPWATCH查看
if(isset($_GET['kw'])) {
	$kw=urldecode($_GET['kw']);
}else {
	Header("Location:search.php");
}
$postfield="srchtxt={$kw}";
//代理服务器
$proxy = '';
//请求
$str=curlrequest($url,$postfield,$proxy);
//输出结果
echo $str;


function curlrequest($url,$postfield,$proxy=""){
	$proxy=trim($proxy);
	$user_agent ="Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)";
	$ch = curl_init(); // 初始化CURL句柄
	if(!empty($proxy)){
		curl_setopt ($ch, CURLOPT_PROXY, $proxy);//设置代理服务器
	}
	curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL
	//curl_setopt($ch, CURLOPT_FAILONERROR, 1); // 启用时显示HTTP状态码，默认行为是忽略编号小于等于400的HTTP信息
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);//启用时会将服务器服务器返回的“Location:”放在header中递归的返回给服务器
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);// 设为TRUE把curl_exec()结果转化为字串，而不是直接输出
	curl_setopt($ch, CURLOPT_POST, 1);//启用POST提交
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfield); //设置POST提交的字符串
	//curl_setopt($ch, CURLOPT_PORT, 80); //设置端口
	curl_setopt($ch, CURLOPT_TIMEOUT, 25); // 超时时间
	curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);//HTTP请求User-Agent:头
	//curl_setopt($ch,CURLOPT_HEADER,1);//设为TRUE在输出中包含头信息
	//$fp = fopen("example_homepage.txt", "w");//输出文件
	//curl_setopt($ch, CURLOPT_FILE, $fp);//设置输出文件的位置，值是一个资源类型，默认为STDOUT (浏览器)。
	curl_setopt($ch,CURLOPT_HTTPHEADER,array(
	'Accept-Language: zh-cn',
	'Connection: Keep-Alive',
	'Cache-Control: no-cache'
	));//设置HTTP头信息
	$document = curl_exec($ch); //执行预定义的CURL
	$info=curl_getinfo($ch); //得到返回信息的特性
	//print_r($info);
	if($info['http_code']=="405"){
		echo "bad proxy {$proxy}\n"; //代理出错
		exit;
	}
	//curl_close($ch);
	return $document;
}

?>
