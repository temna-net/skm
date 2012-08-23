<?php

defined('IN_MOBIQUO') or exit;

$home_data = unserialize($dabandeng_settings['home_data']);
if (isset($home_data_id) && $home_data_id) $home_data = array($home_data_id);

$dabandeng_request = array(
    1  => 'dabandeng_latest_picture',
    2  => 'dabandeng_latest_topic',
    3  => 'dabandeng_latest_post',
    4  => 'dabandeng_digest_topic',
    5  => 'dabandeng_hot_topic',
    6  => 'dabandeng_stick_topic',
    7  => 'dabandeng_member_new',
    8  => 'dabandeng_member_post',
    9  => 'dabandeng_stat',
);

$bids = $name_bid = array();
$query = DB::query("SELECT bid, name FROM ".DB::table('common_block')." WHERE name like 'dabandeng_%'");
while($result = DB::fetch($query)) {
    $bids[] = $result['bid'];
    $name_bid[$result['name']] = $result['bid'];
}

$home_array = array();

if (!empty($bids) || !empty($home_data)) {
    block_get(implode(',', $bids));
    get_language();
    $board_url = $_G['setting']['discuzurl'].'/';
    
    foreach($home_data as $id)
    {
        $bid = $name_bid[$dabandeng_request[$id]];
        if (!$bid) continue;
        
        $block_data = empty($_G['block'][$bid])?array():$_G['block'][$bid];
        if(!$block_data) continue;
        
        if($block_data['cachetime'] > 0 && $_G['timestamp'] - $block_data['dateline'] > $block_data['cachetime'])
        {
            include_once libfile('function/block');
            block_updatecache($bid, true);
            $block_data = $_G['block'][$bid];
        }
        
        $xmlrpc_data = array();
        $data_title = $dabandeng_lang[$dabandeng_request[trim($id)]];
        
        switch ($id) {
            case 1:
                $data_type = 2;
                $attachurl = $_G['setting']['attachurl'];
                foreach($block_data['itemlist'] as $data)
                {
                    $url = $board_url.$attachurl.$data['pic'];
                    $url = preg_replace('/http:.*?http:/', 'http:', $url);
                    
                    $xmlrpc_record = new xmlrpcval(array(
                        'url'           => new xmlrpcval(html_entity_decode($url), 'string'),
                        'subject'       => new xmlrpcval(basic_clean($data['title']), 'base64'),
                        'topic_id'      => new xmlrpcval($data['id'], 'string'),
                    ), 'struct');
                    $xmlrpc_data[] = $xmlrpc_record;
                }
                break;
            case 7:
                $data_type = 3;
                foreach($block_data['itemlist'] as $data)
                {
                    $data['fields'] = unserialize($data['fields']);
                    
                    $xmlrpc_record = new xmlrpcval(array(
                        'name'  => new xmlrpcval(basic_clean($data['title']), 'base64'),
                        'value' => new xmlrpcval(basic_clean(dgmdate($data['fields']['regdate'], 'u')), 'base64'),
                    ), 'struct');
                    $xmlrpc_data[] = $xmlrpc_record;
                }
                break;
            case 8:
                $data_type = 3;
                foreach($block_data['itemlist'] as $data)
                {
                    $data['fields'] = unserialize($data['fields']);
                    
                    $xmlrpc_record = new xmlrpcval(array(
                        'name'  => new xmlrpcval(basic_clean($data['title']), 'base64'),
                        'value' => new xmlrpcval($data['fields']['posts'], 'base64'),
                    ), 'struct');
                    $xmlrpc_data[] = $xmlrpc_record;
                }
                break;
            case 9:
                $data_type = 3;
                preg_match_all('/<p>(\d+)<\/p>(.*?)<\/th>/', $block_data['summary'], $matches);
                
                foreach ($matches[1] as $key => $value)
                {
                    $xmlrpc_record = new xmlrpcval(array(
                        'name'  => new xmlrpcval(basic_clean($matches[2][$key]), 'base64'),
                        'value' => new xmlrpcval($value, 'base64'),
                    ), 'struct');
                    $xmlrpc_data[] = $xmlrpc_record;
                }
                break;
            default: 
                $data_type = 1;
                foreach($block_data['itemlist'] as $data)
                {
                    $data['fields'] = unserialize($data['fields']);
                    $time = ($id == 3 ? $data['fields']['lastpost'] : $data['fields']['dateline']);
                    $data['summary'] = preg_replace('/[\n\r\t]+/', ' ', $data['summary']);
                    
                    $xmlrpc_record =  new xmlrpcval(array(
                        'topic_id'          => new xmlrpcval($data['id'], 'string'),
                        'topic_title'       => new xmlrpcval(basic_clean($data['fields']['fulltitle']), 'base64'),
                        'authhor'           => new xmlrpcval(basic_clean($data['fields']['author']), 'base64'),
                        'topic_author_name' => new xmlrpcval(basic_clean($data['fields']['author']), 'base64'),
                        'time'              => new xmlrpcval(mobiquo_iso8601_encode($time), 'dateTime.iso8601'),
                        'short_content'     => new xmlrpcval(basic_clean($data['summary']), 'base64'),
                        'reply_number'      => new xmlrpcval($data['fields']['replies'], 'int'),
                        'view_number'       => new xmlrpcval($data['fields']['views'], 'int'),
                        'icon_url'          => new xmlrpcval($data['fields']['avatar'], 'string'),
                    ), 'struct');
                    
                    $xmlrpc_data[] = $xmlrpc_record;
                }
        }
        
        $home_array[] = new xmlrpcval(array(
            'type'  => new xmlrpcval($data_type, 'int'),     
            'title' => new xmlrpcval(basic_clean($data_title), 'base64'),
            'data'  => new xmlrpcval($xmlrpc_data, 'array'),
        ), 'struct');
    }
}
