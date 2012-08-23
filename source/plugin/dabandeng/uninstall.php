<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$query = DB::query("SELECT bid FROM ".DB::table('common_block')." WHERE name like 'dabandeng_%'");
$bids = array();
while($result = DB::fetch($query)) {
    $bids[] = $result['bid'];
}

if (!empty($bids)) {
    $bid_str = implode(',', $bids);
    
    $sql = "
    DELETE FROM cdb_common_block_item WHERE bid IN ($bid_str);
    DELETE FROM cdb_common_block WHERE bid IN ($bid_str);
    DELETE FROM cdb_common_block_permission WHERE bid IN ($bid_str);
    DELETE FROM cdb_common_block_item_data WHERE bid IN ($bid_str);
    ";
    
    runquery($sql);
}

$finish = TRUE;