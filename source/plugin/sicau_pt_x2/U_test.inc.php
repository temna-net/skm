<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

//test module

require(dirname(__FILE__).'/sicau_pt_x2.class.php');
$test = new Plugin_Sicau_Pt();
$test->test();
?>