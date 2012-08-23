<?php

defined('IN_MOBIQUO') or exit;

function attach_image_func()
{
    global $upload;

    if ($upload->aid)
    {
        $xmlrpc_result = new xmlrpcval(array('attachment_id'  => new xmlrpcval($upload->aid)), 'struct');
        return new xmlrpcresp($xmlrpc_result);
    }
    else
    {
        get_error('Line: '.__LINE__);
    }
}

function authorize_user_func()
{
    global $_G;
    
    $response = new xmlrpcval(array(
        'authorize_result' => new xmlrpcval(true, 'boolean'),
        'user_id' => new xmlrpcval($_G['uid'], 'string'),
    ), 'struct');

    return new xmlrpcresp($response);
}

function login_func()
{
    global $_G;
    $groupidarray = array($_G['groupid']);
    
    foreach(explode("\t", $_G['member']['extgroupids']) as $extgroupid) {
        if($extgroupid = intval(trim($extgroupid))) {
            $groupidarray[] = $extgroupid;
        }
    }
    
    $usergroup_id = array();
    foreach ($groupidarray as $group_id)
        $usergroup_id[] = new xmlrpcval($group_id);
    
    $maxattachnum = $_G['group']['maxattachnum'] ? $_G['group']['maxattachnum'] : 99;
    $maxattachsize = $_G['group']['maxattachsize'] ? $_G['group']['maxattachsize'] : 2048000;
    
    $response = new xmlrpcval(array(
        'result'        => new xmlrpcval(true, 'boolean'),
        'result_text'   => new xmlrpcval('', 'base64'),
        'user_id'       => new xmlrpcval($_G['uid'], 'string'),
        'user_name'     => new xmlrpcval($_G['username'], 'string'),
        'can_pm'        => new xmlrpcval($_G['uid'], 'boolean'),
        'can_send_pm'   => new xmlrpcval($_G['group']['allowsendpm'], 'boolean'),
        //'can_moderate'  => new xmlrpcval($_G['adminid'], 'boolean'),
        'usergroup_id'  => new xmlrpcval($usergroup_id, 'array'),
        'max_attachment'=> new xmlrpcval($maxattachnum, 'int'),
        'max_png_size'  => new xmlrpcval($maxattachsize, 'int'),
        'max_jpg_size'  => new xmlrpcval($maxattachsize, 'int'),
    ), 'struct');

    return new xmlrpcresp($response);
}

function register_func()
{
    $response = new xmlrpcval(array(
        'result'        => new xmlrpcval(true, 'boolean'),
        'result_text'   => new xmlrpcval('', 'base64'),
    ), 'struct');

    return new xmlrpcresp($response);
}

function create_topic_func()
{
    return new_topic_func();
}

function new_topic_func()
{
    global $tid, $modnewthreads;

    $xmlrpc_create_topic = new xmlrpcval(array(
        'result'    => new xmlrpcval(true, 'boolean'),
        'topic_id'  => new xmlrpcval($tid),
        'state'     => new xmlrpcval($modnewthreads ? 1 : 0, 'int')
    ), 'struct');

    return new xmlrpcresp($xmlrpc_create_topic);
}

function get_board_stat_func()
{
    global $onlinenum, $guestnum, $totalmembers, $threads, $posts;

    $board_stat = array(
        'total_threads' => new xmlrpcval($threads, 'int'),
        'total_posts'   => new xmlrpcval($posts, 'int'),
        'total_members' => new xmlrpcval($totalmembers, 'int'),
        'guest_online'  => new xmlrpcval($guestnum, 'int'),
        'total_online'  => new xmlrpcval($onlinenum, 'int')
    );

    $response = new xmlrpcval($board_stat, 'struct');

    return new xmlrpcresp($response);
}

function get_box_func()
{
    global $pmlist, $pmnum, $unreadnum, $_G, $boxid;
    $pm_list = array();
    foreach ($pmlist as $pm)
    {
        $msg_to = array(new xmlrpcval(array('username' => new xmlrpcval(basic_clean($pm['msg_from']['username']), 'base64')), 'struct'));
        $pm_list[] = new xmlrpcval(array(
            'msg_id'        => new xmlrpcval($pm['pmid']),
            'msg_state'     => new xmlrpcval($pm['new'] ? 1 : 2, 'int'),
            'sent_date'     => new xmlrpcval(mobiquo_iso8601_encode($pm['dateline']),'dateTime.iso8601'),
            'msg_from'      => new xmlrpcval(basic_clean($pm['msg_from']['username']), 'base64'),
            'icon_url'      => new xmlrpcval(html_entity_decode($pm['icon_url'])),
            'msg_to'        => new xmlrpcval($msg_to, 'array'),
            'msg_subject'   => new xmlrpcval(basic_clean($pm['subject']), 'base64'),
            'short_content' => new xmlrpcval(get_short_message($pm['message']), 'base64'),
            'is_online'     => new xmlrpcval($_G['forum_onlineauthors'][$pm['displayid']], 'boolean'),
        ), 'struct');
    }

    $result = new xmlrpcval(array(
        'result'             => new xmlrpcval(true, 'boolean'),
        'total_message_count' => new xmlrpcval(count($pmlist), 'int'),
        'total_unread_count'  => new xmlrpcval(0, 'int'),
        'list'                => new xmlrpcval($pm_list, 'array')
    ), 'struct');

    return new xmlrpcresp($result);
}

function get_box_info_func()
{
    global $box_info;
    $box_list = array();
    foreach($box_info as $box)
    {
        $box_list[] = new xmlrpcval(array(
            'box_id'        => new xmlrpcval($box['box_id'], 'string'),
            'box_name'      => new xmlrpcval(basic_clean($box['box_name']), 'base64'),
            'msg_count'     => new xmlrpcval($box['msg_count'], 'int'),
            'unread_count'  => new xmlrpcval($box['unread_count'], 'int'),
            'box_type'      => new xmlrpcval($box['box_type'], 'string')
        ), 'struct');
    }

    $result = new xmlrpcval(array(
        'result'             => new xmlrpcval(true, 'boolean'),
        'message_room_count' => new xmlrpcval(100, 'int'),
        'list'               => new xmlrpcval($box_list, 'array')
    ), 'struct');

    return new xmlrpcresp($result);
}

function get_config_func()
{
    global $_G, $show_home_data, $home_data, $question_list, $forum_stat, $dabandeng_settings, $result;
    if(($_G['setting']['regstatus'] == 1 || $_G['setting']['regstatus'] == 3) && $result)
    {
        $register = 1;   
    }else
    {
        $register = 0;  
    }
    
    $config_list = array(
        'version'           => new xmlrpcval(DABANDENG_VERSION, 'string'),
        'is_open'           => new xmlrpcval(true, 'boolean'),
        'register'           => new xmlrpcval($register, 'boolean'),
        'guest_okay'        => new xmlrpcval($dabandeng_settings['guest_okay'], 'boolean'),
        'reg_url'           => new xmlrpcval("member.php?mod={$_G[setting][regname]}", 'string'),
        'api_level'         => new xmlrpcval('3', 'string'),
        
        'disable_search'    => new xmlrpcval($dabandeng_settings['disable_search'], 'string'),
        'disable_bbcode'    => new xmlrpcval('0', 'string'),
        
        'users'             => new xmlrpcval($forum_stat['members'], 'int'),
        'posts'             => new xmlrpcval($forum_stat['posts'], 'int'),
        'topics'            => new xmlrpcval($forum_stat['topics'], 'int'),
        'yesterday_posts'   => new xmlrpcval($forum_stat['yesterdayposts'], 'int'),
        
        'home_data'         => new xmlrpcval($home_data, 'base64'),
        'show_home_data'    => new xmlrpcval($show_home_data, 'boolean'),
        'question_list'     => new xmlrpcval($question_list, 'array'),
    );

    $response = new xmlrpcval($config_list, 'struct');

    return new xmlrpcresp($response);
}

function get_forum_func()
{
    global $forum_tree;

    $response = new xmlrpcval($forum_tree, 'array');

    return new xmlrpcresp($response);
}

function get_inbox_stat_func()
{
    global $unreadnum;
    
    $result = new xmlrpcval(array(
        'inbox_unread_count' => new xmlrpcval($unreadnum, 'int')
    ), 'struct');

    return new xmlrpcresp($result);
}

function get_message_func()
{
    global $pm;
    //error_log(print_r($pm, true), 3, 'my.log');
    $msg_to = array(new xmlrpcval(array('username' => new xmlrpcval(basic_clean($pm['msgto']), 'base64')), 'struct'));
    $result = new xmlrpcval(array(
        'result'        => new xmlrpcval(true, 'boolean'),
        'msg_from'      => new xmlrpcval(basic_clean($pm['msgfrom']), 'base64'),
        'msg_to'        => new xmlrpcval($msg_to, 'array'),
        'icon_url'      => new xmlrpcval(html_entity_decode($pm['icon_url'])),
        'sent_date'     => new xmlrpcval(mobiquo_iso8601_encode($pm['dateline']),'dateTime.iso8601'),
        'msg_subject'   => new xmlrpcval(basic_clean($pm['subject']), 'base64'),
        'text_body'     => new xmlrpcval(get_message($pm['message']), 'base64'),
        'is_online'     => new xmlrpcval($pm['is_online'], 'boolean'),
        'allow_smilies' => new xmlrpcval(true, 'boolean'),
    ), 'struct');

    return new xmlrpcresp($result);
}

function get_new_topic_func()
{
    global $threadlist, $_G;
    
    $topic_list = array();
    foreach ($threadlist as $thread)
    {
        $xmlrpc_topic = new xmlrpcval(array(
            'forum_id'          => new xmlrpcval($thread['fid']),
            'forum_name'        => new xmlrpcval(basic_clean($thread['forumname']), 'base64'),
            'topic_id'          => new xmlrpcval($thread['tid']),
            'topic_title'       => new xmlrpcval(basic_clean($thread['subject']), 'base64'),
            'post_author_name'  => new xmlrpcval(basic_clean($thread['author']), 'base64'),
            'can_subscribe'     => new xmlrpcval($_G['uid'] ? true : false, 'boolean'),
            'is_subscribed'     => new xmlrpcval(is_subscribed($thread['tid']), 'boolean'),
            'is_closed'         => new xmlrpcval($thread['folder'] == 'lock' ? true : false, 'boolean'),
            'icon_url'          => new xmlrpcval(get_user_avatar($thread['authorid'])),
            'post_time'         => new xmlrpcval(mobiquo_iso8601_encode($thread['dblastpost']), 'dateTime.iso8601'),
            'reply_number'      => new xmlrpcval(intval($thread['replies']), 'int'),
            'view_number'       => new xmlrpcval(intval($thread['views']), 'int'),
            'new_post'          => new xmlrpcval($thread['new'] ? true : false, 'boolean'),
            'short_content'     => new xmlrpcval(basic_clean($thread['message']), 'base64'),
        ), 'struct');

        $topic_list[] = $xmlrpc_topic;
    }

    return new xmlrpcresp(new xmlrpcval($topic_list, 'array'));
}

function get_online_users_func()
{
    global $list, $onlineinfo;

    $user_list = array();
    foreach($list as $user)
    {
        $user_list[] = new xmlrpcval(array(
            'user_name' => new xmlrpcval(basic_clean($user['username']), 'base64'),
            'icon_url'  => new xmlrpcval(get_user_avatar($user['uid'])),
            'display_text' => new xmlrpcval(basic_clean($user['recentnote']), 'base64'),
        ), 'struct');
    }

    $online_users = array(
        'member_count' => new xmlrpcval($onlineinfo['member_count'], 'int'),
        'guest_count'  => new xmlrpcval($onlineinfo['guest_count'], 'int'),
        'list'         => new xmlrpcval($user_list, 'array')
    );

    $response = new xmlrpcval($online_users, 'struct');

    return new xmlrpcresp($response);
}

function get_raw_post_func()
{
    global $postinfo;
    
    $message = preg_replace('/^\[i=.*?\].*?\[\/i\]\s\s/si', '', $postinfo['message']);
    
    $response = new xmlrpcval(
        array(
            'post_id'       => new xmlrpcval($postinfo['pid']),
            'post_title'    => new xmlrpcval(basic_clean($postinfo['subject']), 'base64'),
            'post_content'  => new xmlrpcval(basic_clean($message), 'base64'),
        ),
        'struct'
    );
    
    return new xmlrpcresp($response);
}

function get_quote_post_func()
{
    global $_G, $thread, $quotemessage;
    
    $response = new xmlrpcval(
        array(
            'post_id'       => new xmlrpcval($_G['gp_repquote']),
            'post_title'    => new xmlrpcval(basic_clean('RE: '.$thread['subject']), 'base64'),
            'post_content'  => new xmlrpcval(basic_clean($quotemessage), 'base64'),
        ),
        'struct'
    );
    
    return new xmlrpcresp($response);
}

function get_subscribed_topic_func()
{
    global $f_list, $count;
    
    $topic_list = array();
    foreach ($f_list as $fav)
    {
        $posttableid = $fav['posttableid'];
        $posttable = $posttableid ? "forum_post_$posttableid" : 'forum_post';
        
        $short_content = get_short_content($fav['tid'], $posttable);
        
        $xmlrpc_topic = new xmlrpcval(array(
            'forum_id'          => new xmlrpcval($fav['fid'], 'string'),
            'forum_name'        => new xmlrpcval(basic_clean($fav['name']), 'base64'),
            'topic_id'          => new xmlrpcval($fav['tid'], 'string'),
            'topic_title'       => new xmlrpcval(basic_clean($fav['subject']), 'base64'),
            'post_author_name'  => new xmlrpcval(basic_clean($fav['author']), 'base64'),
            'is_closed'         => new xmlrpcval($fav['closed'], 'boolean'),
            'reply_number'      => new xmlrpcval($fav['replies'], 'int'),
            'view_number'       => new xmlrpcval($fav['views'], 'int'),
            'short_content'     => new xmlrpcval($short_content, 'base64'),
            'icon_url'          => new xmlrpcval(get_user_avatar($fav['authorid'])),
            'new_post'          => new xmlrpcval($fav['new'] ? true : false, 'boolean'),
            'post_time'         => new xmlrpcval(mobiquo_iso8601_encode($fav['dateline']), 'dateTime.iso8601')
        ), 'struct');

        $topic_list[] = $xmlrpc_topic;
    }

    $response = new xmlrpcval(
        array(
            'total_topic_num'   => new xmlrpcval($count, 'int'),
            'topics'            => new xmlrpcval($topic_list, 'array'),
        ),
        'struct'
    );

    return new xmlrpcresp($response);
}

function get_subscribed_forum_func()
{
    global $list, $count;
    
    $forum_list = array();
    foreach ($list as $fav)
    {
        $subforumonly = $fav['simple'] & 1;
        $xmlrpc_forum = new xmlrpcval(array(
            'forum_id'      => new xmlrpcval($fav['id'], 'string'),
            'forum_name'    => new xmlrpcval(basic_clean($fav['title']), 'base64'),
            'icon_url'      => new xmlrpcval(get_forum_icon_url($fav['id'], $fav['icon'])),
            'is_protected'  => new xmlrpcval($fav['password'] ? true : false, 'boolean'),
            'sub_only'      => new xmlrpcval(($fav['type'] == 'group' || $subforumonly) ? true : false, 'boolean'),
            'new_post'      => new xmlrpcval($fav['todayposts'] ? true : false, 'boolean'),
        ), 'struct');

        $forum_list[] = $xmlrpc_forum;
    }
    
    $response = new xmlrpcval(
        array(
            'total_forums_num'  => new xmlrpcval($count, 'int'),
            'forums'            => new xmlrpcval($forum_list, 'array'),
        ),
        'struct'
    );
    
    return new xmlrpcresp($response);
}

function get_thread_func()
{
    global $thread, $t_post, $postlist, $_G, $allowpostreply, $hiddenreplies, $threadsortshow, $page, $polloptions, $return_html;
    
    lang('forum/template');
    $lang = $_G['lang']['forum_template'];
    
    $needhiddenreply = ($hiddenreplies && $_G['uid'] != $t_post['authorid'] && $_G['uid'] != $_G['forum_thread']['authorid'] && !$t_post['first'] && !$_G['forum']['ismoderator']);
    $rpc_post_list = array();
    foreach($postlist as $key=>$post) {
        $post_content = '';
        
        if ($post['warned']) {
            $post_content .= basic_clean("[$lang[warn_get]!!!]\n\n");
        }
        
        if ($_G['adminid'] != 1 && $_G['setting']['bannedmessages'] & 1 && (($post['authorid'] && !$post['username']) || ($post['groupid'] == 4 || $post['groupid'] == 5) || $post['status'] == -1 || $post['memberstatus'])) {
            $post_content .= '[ '.basic_clean($lang['message_banned']).' ]';
        } elseif ($_G['adminid'] != 1 && $post['status'] & 1) {
            $post_content .= '[ '.basic_clean($lang['message_single_banned']).' ]';
        } elseif ($needhiddenreply) {
            $post_content .= '[ '.basic_clean($lang['message_ishidden_hiddenreplies']).' ]';
        } elseif ($post['first'] && $_G['forum_threadpay']) {
            if ($thread['freemessage']) {
                $post_content .= post_html_clean($thread['freemessage'])."\n";
            }
            if ($thread['payers']) {
                $post_content .= basic_clean($lang['have'] . $thread['payers'] . $lang['people_bug'])."\n";
            }
            
            if ($_G['forum_thread']['price'] > 0) {
                eval('$pay_comment = "'.$lang['pay_comment'].'";');
                $post_content .= '[ '.post_html_clean($pay_comment)." ]\n";
            }
            if ($thread['endtime']) {
                eval('pay_free_time = "'.$lang['pay_comment'].'";');
                $post_content .= '[ '.post_html_clean($pay_free_time)." ]\n";
            }
        } else {
            if ($_G['setting']['bannedmessages'] & 1 && (($post['authorid'] && !$post['username']) || ($post['groupid'] == 4 || $post['groupid'] == 5))) {
                $post_content .= '[ '.basic_clean($lang['admin_message_banned'])." ]\n";
            } elseif ($post['status'] & 1) {
                $post_content .= '[ '.basic_clean($lang['admin_message_single_banned'])." ]\n";
            }
            
            if ($post['first']) {
                if ($_G['forum_thread']['price'] > 0 && $_G['forum_thread']['special'] == 0) {
                    $p_title = $_G['setting']['extcredits'][$_G['setting']['creditstransextra'][1]]['title'];
                    $p_price = $_G['forum_thread']['price'];
                    $p_unit = $_G['setting']['extcredits'][$_G['setting']['creditstransextra'][1]]['unit'];
                    $post_content .= basic_clean("[ $lang[pay_threads]: $p_title $p_price $p_unit ]\n");
                }
            }
			$post['message'] = preg_replace('/\<img id="(.*?)" src="(.*?)"/', '<img', $post['message']);
			$post['message'] = str_replace("<img zoomfile=", "<img src=", $post['message']);
            $post_content .= post_html_clean($post['message']);
            $attachments = array();
            if ($post['attachment']) {
                $post_content .= "\n[".basic_clean($lang['attachment']).': ';
                if ($_G['uid']) {
                    $post_content .= basic_clean($lang['attach_nopermission']).']';
                } else {
                    $post_content .= substr(basic_clean($lang['attach_nopermission_login']), 0, 45).']';
                }
            } elseif ($post['imagelist'] || $post['attachlist']) {
                if ($post['imagelist']) {
                    foreach($post['attachments'] as $image) {
                        $xmlrpc_attachment = new xmlrpcval(array(
                            'filename'      => new xmlrpcval(basic_clean($image['filename']), 'base64'),
                            'filesize'      => new xmlrpcval($image['filesize'], 'int'),
                            'content_type'  => new xmlrpcval('image'),
                            'thumbnail_url' => new xmlrpcval(url_encode($image['thumbnail_url'])),
                            'url'           => new xmlrpcval(url_encode($image['url']).$image['attachment']),
                        ), 'struct');
                        $attachments[] = $xmlrpc_attachment;
                    }
                }
                if ($post['attachlist']) {
                    foreach($post['attachlist'] as $attach) {
                        $xmlrpc_attachment = new xmlrpcval(array(
                            'filename'      => new xmlrpcval(basic_clean($attach['filename']), 'base64'),
                            'filesize'      => new xmlrpcval($attach['filesize'], 'int'),
                            'content_type'  => new xmlrpcval('other'),
                            'thumbnail_url' => new xmlrpcval(''),
                            'url'           => new xmlrpcval(url_encode($attach['url']))
                        ), 'struct');
                        $attachments[] = $xmlrpc_attachment;
                    }
                }
            }
        }
        $post_content = preg_replace('/\[\/img\]\s+\[img\]/s', '[/img][img]', $post_content);
        if($key == 0 && $page == 1)
        {
           if(!empty($threadsortshow))
            {
                $arr = '';
        		foreach($threadsortshow['optionlist'] as $key=>$value)
        		{
        		    $arr .=   $value['title'].":".$value['value']."<br />";
        		}
                $types_title = array_values($_G['forum']['threadsorts']['types']); 
                if($return_html == '')
                {
                    $post_content =  post_html_clean($types_title[0])."\n\r\n\r".post_html_clean($arr)."\n\r".$post_content;
                }else
                {
                    $post_content = post_html_clean($types_title[0])."<br />".post_html_clean($arr)."<br /><br />".$post_content;   
                }
            }else if(!empty($polloptions))
            {
                $arr = '';
        		foreach($polloptions as $key=>$value)
        		{
        		    $arr .=   $value['polloption']."&nbsp;&nbsp;&nbsp;&nbsp;".$value['votes']."<br />";
        		}
                $types_title = array_values($_G['forum']['threadsorts']['types']); 
                
                if($return_html == '')
                {
                    $post_content =  post_html_clean($types_title[0])."\n\r\n\r".post_html_clean($arr)."\n\r".$post_content;
                }else
                {
                    $post_content = post_html_clean($types_title[0])."<br />".post_html_clean($arr)."<br /><br />".$post_content;   
                }
            }    
        }
        $xmlrpc_post = new xmlrpcval(array(
            'topic_id'          => new xmlrpcval($_G['forum_thread']['tid']),
            'post_id'           => new xmlrpcval($post['pid']),
            'post_title'        => new xmlrpcval(basic_clean($post['subject']), 'base64'),
            'post_content'      => new xmlrpcval($post_content, 'base64'),
            'post_author_id'    => new xmlrpcval($post['authorid']),
            'post_author_name'  => new xmlrpcval($post['anonymous'] ? '' : basic_clean($post['author']), 'base64'),
            'icon_url'          => new xmlrpcval($post['anonymous'] ? '' : get_user_avatar($post['authorid'])),
            'post_time'         => new xmlrpcval(mobiquo_iso8601_encode($post['dbdateline']), 'dateTime.iso8601'),
            'is_online'         => new xmlrpcval(($post['online'] || ($_G['setting']['vtonlinestatus'] == 2 && $_G['forum_onlineauthors'][$post['authorid']])), 'boolean'),
            'can_edit'          => new xmlrpcval($post['can_edit'], 'boolean'),
            'allow_smilies'     => new xmlrpcval($post['smileyoff'] != 1, 'boolean'),
            'attachments'       => new xmlrpcval($attachments, 'array')
        ), 'struct');

        $rpc_post_list[] = $xmlrpc_post;
    }
    
    $allowupload = !$_G['group']['maxattachnum'] || $_G['group']['maxattachnum'] && $_G['group']['maxattachnum'] > getuserprofile('todayattachs');
    
    return new xmlrpcresp(
        new xmlrpcval(array(
                'total_post_num' => new xmlrpcval(intval($_G['forum_thread']['replies'] + 1), 'int'),
                'forum_id'       => new xmlrpcval($_G['forum']['fid']),
                'forum_name'     => new xmlrpcval(basic_clean($_G['forum']['name']), 'base64'),
                'topic_id'       => new xmlrpcval($_G['forum_thread']['tid']),
                'topic_title'    => new xmlrpcval(basic_clean($_G['forum_thread']['subject']), 'base64'),
                'posts'          => new xmlrpcval($rpc_post_list, 'array'),
                
                'can_subscribe'  => new xmlrpcval($_G['uid'] && !$_G['forum_thread']['archiveid'], 'boolean'),
                'is_subscribed'  => new xmlrpcval(is_subscribed($_G['forum_thread']['tid']), 'boolean'),
                'can_reply'      => new xmlrpcval($allowpostreply, 'boolean'),
                'is_closed'      => new xmlrpcval($_G['forum_thread']['closed'], 'boolean'),
                'can_upload'     => new xmlrpcval($allowupload, 'boolean'),
            ),
            'struct'
        )
    );
}

function get_topic_func()
{
    global $_G;
    $prefix_list = array();
    $prefixes = $_G['forum']['threadtypes'];
    
    if ($prefixes['types']) {
        foreach($prefixes['types'] as $prefix_id => $prefix_name) {
            $xmlrpc_prefix = new xmlrpcval(array(
                'prefix_id'           => new xmlrpcval($prefix_id, 'string'),
                'prefix_display_name' => new xmlrpcval(basic_clean($prefix_name), 'base64'),
            ), 'struct');
    
            $prefix_list[] = $xmlrpc_prefix;
        }
    }
    
    $topic_list = array();
    foreach($_G['forum_threadlist'] as $thread) {
        if($thread['readperm'] && $thread['readperm'] > $_G['group']['readaccess'] && !$_G['forum']['ismoderator'] && $thread['authorid'] != $_G['uid']) {
            $short_content = '';
        } else {
            $posttableid = $thread['posttableid'];
            $posttable = $posttableid ? "forum_post_$posttableid" : 'forum_post';
            $short_content = get_short_content($thread['tid'], $posttable);
            $anonymous = get_anonymous($thread['tid'], $posttable);
        }
        
        $xmlrpc_topic = new xmlrpcval(array(
            'forum_id'          => new xmlrpcval($_G['forum']['fid'], 'string'),
            'topic_id'          => new xmlrpcval($thread['tid'], 'string'),
            'topic_title'       => new xmlrpcval(basic_clean($thread['subject']), 'base64'),
            'topic_author_id'   => new xmlrpcval($thread['authorid'], 'string'),
            'topic_author_name' => new xmlrpcval($anonymous ? '' : basic_clean($thread['author']), 'base64'),
            'last_reply_time'   => new xmlrpcval(mobiquo_iso8601_encode($thread['lastpost']),'dateTime.iso8601'),
            'reply_number'      => new xmlrpcval($thread['replies'], 'int'),
            'new_post'          => new xmlrpcval($thread['new'] ? true : false, 'boolean'),
            'view_number'       => new xmlrpcval($thread['views'], 'int'),
            'short_content'     => new xmlrpcval($short_content, 'base64'),
            'icon_url'          => new xmlrpcval($anonymous ? '' : get_user_avatar($thread['authorid'])),
            'attachment'        => new xmlrpcval($thread['attachment'], 'string'),
            'is_closed'         => new xmlrpcval($thread['folder'] == 'lock' ? true : false, 'boolean'),
            'can_subscribe'     => new xmlrpcval($_G['uid'], 'boolean'),
            'is_subscribed'     => new xmlrpcval(is_subscribed($thread['tid']), 'boolean'),
        ), 'struct');

        $topic_list[] = $xmlrpc_topic;
    }
    
    $allowupload = !$_G['group']['maxattachnum'] || $_G['group']['maxattachnum'] && $_G['group']['maxattachnum'] > getuserprofile('todayattachs');
    
    $response = new xmlrpcval(
        array(
            'total_topic_num' => new xmlrpcval(intval($_G['forum_threadcount']), 'int'),
            'forum_id'        => new xmlrpcval($_G['forum']['fid'], 'string'),
            'forum_name'      => new xmlrpcval(basic_clean($_G['forum']['name']), 'base64'),
            'can_post'        => new xmlrpcval($_G['group']['allowpost'], 'boolean'),
            'require_prefix'  => new xmlrpcval($prefixes['required'], 'boolean'),
            'prefixes'        => new xmlrpcval($prefix_list, 'array'),
            'can_upload'      => new xmlrpcval($allowupload, 'boolean'),
            'topics'          => new xmlrpcval($topic_list, 'array'),
        ),
        'struct'
    );

    return new xmlrpcresp($response);
}

function get_user_info_func()
{
    global $space, $custom_fields;
    
    $custom_fields_list = array();
    foreach($custom_fields as $key => $value)
    {
        $custom_fields_list[] = new xmlrpcval(array(
            'name'  => new xmlrpcval(basic_clean($key), 'base64'),
            'value' => new xmlrpcval(basic_clean($value), 'base64'),
        ), 'struct');
    }

    $xmlrpc_user_info = new xmlrpcval(array(
        'post_count'            => new xmlrpcval($space['posts'], 'int'),
        'reg_time'              => new xmlrpcval(mobiquo_iso8601_encode($space['regdate']), 'dateTime.iso8601'),
        'last_activity_time'    => new xmlrpcval(mobiquo_iso8601_encode($space['lastactivity']), 'dateTime.iso8601'),
        'is_online'             => new xmlrpcval($space['is_online'], 'boolean'),
        'icon_url'              => new xmlrpcval(get_user_avatar($space['uid'])),
        'current_activity'      => new xmlrpcval($space['is_online'] ? basic_clean($space['recentnote']) : '', 'base64'),
        'custom_fields_list'    => new xmlrpcval($custom_fields_list, 'array'),
    ), 'struct');

    return new xmlrpcresp($xmlrpc_user_info);
}

function get_user_reply_post_func()
{
    global $postlist;
    
    $post_list = array();
    foreach($postlist as $post)
    {
        $post_subject = $post['p_subject'] ? $post['p_subject'] : 'RE: '.$post['subject'];
        $xmlrpc_post = new xmlrpcval(array(
            'forum_id'          => new xmlrpcval($post['fid']),
            'forum_name'        => new xmlrpcval(basic_clean($post['forumname']), 'base64'),
            'topic_id'          => new xmlrpcval($post['tid']),
            'topic_title'       => new xmlrpcval(basic_clean($post_subject), 'base64'),
            'post_id'           => new xmlrpcval($post['pid']),
            'post_title'        => new xmlrpcval(basic_clean($post_subject), 'base64'),
            'post_time'         => new xmlrpcval(mobiquo_iso8601_encode($post['p_dateline']), 'dateTime.iso8601'),
            'icon_url'          => new xmlrpcval(get_user_avatar($post['authorid'])),
            'reply_number'      => new xmlrpcval(intval($post['replies']), 'int'),
            'view_number'       => new xmlrpcval(intval($post['views']), 'int'),
            'new_post'          => new xmlrpcval($post['new'] ? true : false, 'boolean'),
            'short_content'     => new xmlrpcval(basic_clean($post['message']), 'base64'),
        ), 'struct');

        $post_list[] = $xmlrpc_post;
    }

    return new xmlrpcresp(new xmlrpcval($post_list, 'array'));
}

function get_user_topic_func()
{
    global $threadlist, $_G;

    $topic_list = array();
    foreach($threadlist as $thread)
    {
        $xmlrpc_topic = new xmlrpcval(array(
            'forum_id'               => new xmlrpcval($thread['fid']),
            'forum_name'             => new xmlrpcval(basic_clean($thread['forumname']), 'base64'),
            'topic_id'               => new xmlrpcval($thread['tid']),
            'topic_title'            => new xmlrpcval(basic_clean($thread['subject']), 'base64'),
            'last_reply_author_name' => new xmlrpcval(basic_clean($thread['lastposter']), 'base64'),
            'icon_url'               => new xmlrpcval(get_user_avatar($thread['lastposterid'])),
            'last_reply_time'        => new xmlrpcval(mobiquo_iso8601_encode($thread['dblastpost']), 'dateTime.iso8601'),
            'reply_number'           => new xmlrpcval(intval($thread['replies']), 'int'),
            'view_number'            => new xmlrpcval(intval($thread['views']), 'int'),
            'new_post'               => new xmlrpcval($thread['new'] ? true : false, 'boolean'),
            'short_content'          => new xmlrpcval(basic_clean($thread['message']), 'base64'),
        ), 'struct');

        $topic_list[] = $xmlrpc_topic;
    }

    return new xmlrpcresp(new xmlrpcval($topic_list, 'array'));
}

function reply_topic_func()
{
    return reply_post_func();
}

function reply_post_func()
{
    global $pid, $modnewreplies, $tid, $_G;
    $xmlrpc_reply_topic = new xmlrpcval(array(
        'result'    => new xmlrpcval(true, 'boolean'),
        'post_id'   => new xmlrpcval($pid),
        'tid'       => new xmlrpcval($_G['gp_tid']),
        'state'     => new xmlrpcval($modnewreplies ? 1 : 0, 'int')
    ), 'struct');

    return new xmlrpcresp($xmlrpc_reply_topic);
}

function save_raw_post_func()
{
    global $modnewreplies;
    
    $result = new xmlrpcval(array(
        'result'    => new xmlrpcval(true, 'boolean'),
        'state'     => new xmlrpcval($modnewreplies ? 1 : 0, 'int')
    ), 'struct');

    return new xmlrpcresp($result);
}

function get_search_option_func()
{
    global $disabled, $language, $forums;

    $field_list = array();
    
    // add option for search key field
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval('', 'base64'),
        'value'     => new xmlrpcval('', 'base64'),
    ), 'struct');
    
    $field = new xmlrpcval(array(
        'type'      => new xmlrpcval('text', 'base64'),
        'field'     => new xmlrpcval('srchtxt', 'base64'),
        'title'     => new xmlrpcval('', 'base64'),
        'option'    => new xmlrpcval(array($option), 'array'),
    ), 'struct');
    
    $field_list[] = $field;
    
    // add option for search type: title or fulltext
    $options = array();
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['title'], 'base64'),
        'value'     => new xmlrpcval('title', 'base64'),
        'default'   => new xmlrpcval(true, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    if (!$disabled['fulltext']) {
        $option = new xmlrpcval(array(
            'title'     => new xmlrpcval($language['fulltext'], 'base64'),
            'value'     => new xmlrpcval('fulltext', 'base64'),
            'default'   => new xmlrpcval(false, 'boolean'),
        ), 'struct');
        $options[] = $option;
    }
    
    $field = new xmlrpcval(array(
        'type'      => new xmlrpcval('radio', 'base64'),
        'field'     => new xmlrpcval('srchtype', 'base64'),
        'title'     => new xmlrpcval('', 'base64'),
        'option'    => new xmlrpcval($options, 'array'),
    ), 'struct');
    
    $field_list[] = $field;
    
    // add option for search by user name
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval('', 'base64'),
        'value'     => new xmlrpcval('', 'base64'),
    ), 'struct');
    
    $field = new xmlrpcval(array(
        'type'      => new xmlrpcval('text', 'base64'),
        'field'     => new xmlrpcval('srchuname', 'base64'),
        'title'     => new xmlrpcval($language['author'], 'base64'),
        'option'    => new xmlrpcval(array($option), 'array'),
    ), 'struct');
    
    $field_list[] = $field;
    
    // add option for search range: all, digest, or top      
    $options = array();
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['search_thread_range_all'], 'base64'),
        'value'     => new xmlrpcval('all', 'base64'),
        'default'   => new xmlrpcval(true, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['search_thread_range_digest'], 'base64'),
        'value'     => new xmlrpcval('digest', 'base64'),
        'default'   => new xmlrpcval(false, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['search_thread_range_top'], 'base64'),
        'value'     => new xmlrpcval('top', 'base64'),
        'default'   => new xmlrpcval(false, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $field = new xmlrpcval(array(
        'type'      => new xmlrpcval('radio', 'base64'),
        'field'     => new xmlrpcval('srchfilter', 'base64'),
        'title'     => new xmlrpcval($language['search_thread_range'], 'base64'),
        'option'    => new xmlrpcval($options, 'array'),
    ), 'struct');
    
    $field_list[] = $field;
    
    // add option for special thread search
    $options = array();
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['special_poll'], 'base64'),
        'value'     => new xmlrpcval('1', 'base64'),
        'default'   => new xmlrpcval(false, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['special_trade'], 'base64'),
        'value'     => new xmlrpcval('2', 'base64'),
        'default'   => new xmlrpcval(false, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['special_reward'], 'base64'),
        'value'     => new xmlrpcval('3', 'base64'),
        'default'   => new xmlrpcval(false, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['special_activity'], 'base64'),
        'value'     => new xmlrpcval('4', 'base64'),
        'default'   => new xmlrpcval(false, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['special_debate'], 'base64'),
        'value'     => new xmlrpcval('5', 'base64'),
        'default'   => new xmlrpcval(false, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $field = new xmlrpcval(array(
        'type'      => new xmlrpcval('checkbox', 'base64'),
        'field'     => new xmlrpcval('special', 'base64'),
        'title'     => new xmlrpcval($language['special_thread'], 'base64'),
        'option'    => new xmlrpcval($options, 'array'),
    ), 'struct');
    
    $field_list[] = $field;
    
    // add option for search time control
    $options = array();
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['search_any_date'], 'base64'),
        'value'     => new xmlrpcval('0', 'base64'),
        'default'   => new xmlrpcval(true, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['1_days_ago'], 'base64'),
        'value'     => new xmlrpcval('86400', 'base64'),
        'default'   => new xmlrpcval(false, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['2_days_ago'], 'base64'),
        'value'     => new xmlrpcval('172800', 'base64'),
        'default'   => new xmlrpcval(false, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['7_days_ago'], 'base64'),
        'value'     => new xmlrpcval('604800', 'base64'),
        'default'   => new xmlrpcval(false, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['30_days_ago'], 'base64'),
        'value'     => new xmlrpcval('2592000', 'base64'),
        'default'   => new xmlrpcval(false, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['90_days_ago'], 'base64'),
        'value'     => new xmlrpcval('7776000', 'base64'),
        'default'   => new xmlrpcval(false, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['180_days_ago'], 'base64'),
        'value'     => new xmlrpcval('15552000', 'base64'),
        'default'   => new xmlrpcval(false, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['365_days_ago'], 'base64'),
        'value'     => new xmlrpcval('31536000', 'base64'),
        'default'   => new xmlrpcval(false, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $field = new xmlrpcval(array(
        'type'      => new xmlrpcval('radio', 'base64'),
        'field'     => new xmlrpcval('srchfrom', 'base64'),
        'title'     => new xmlrpcval($language['search_time'], 'base64'),
        'option'    => new xmlrpcval($options, 'array'),
    ), 'struct');
    
    $field_list[] = $field;
    
    // add option for search time order
    $options = array();
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['search_newer'], 'base64'),
        'value'     => new xmlrpcval('', 'base64'),
        'default'   => new xmlrpcval(true, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    if (!$disabled['fulltext']) {
        $option = new xmlrpcval(array(
            'title'     => new xmlrpcval($language['search_older'], 'base64'),
            'value'     => new xmlrpcval('1', 'base64'),
            'default'   => new xmlrpcval(false, 'boolean'),
        ), 'struct');
        $options[] = $option;
    }
    
    $field = new xmlrpcval(array(
        'type'      => new xmlrpcval('radio', 'base64'),
        'field'     => new xmlrpcval('before', 'base64'),
        'title'     => new xmlrpcval('', 'base64'),
        'option'    => new xmlrpcval($options, 'array'),
    ), 'struct');
    
    $field_list[] = $field;
    
    // add option for search order control
    $options = array();
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['order_lastpost'], 'base64'),
        'value'     => new xmlrpcval('lastpost', 'base64'),
        'default'   => new xmlrpcval(true, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['order_starttime'], 'base64'),
        'value'     => new xmlrpcval('dateline', 'base64'),
        'default'   => new xmlrpcval(false, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['order_replies'], 'base64'),
        'value'     => new xmlrpcval('replies', 'base64'),
        'default'   => new xmlrpcval(false, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['order_views'], 'base64'),
        'value'     => new xmlrpcval('views', 'base64'),
        'default'   => new xmlrpcval(false, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    $field = new xmlrpcval(array(
        'type'      => new xmlrpcval('radio', 'base64'),
        'field'     => new xmlrpcval('orderby', 'base64'),
        'title'     => new xmlrpcval($language['search_orderby'], 'base64'),
        'option'    => new xmlrpcval($options, 'array'),
    ), 'struct');
    
    $field_list[] = $field;
    
    // add option for search time order: desc or asc
    $options = array();
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['order_asc'], 'base64'),
        'value'     => new xmlrpcval('asc', 'base64'),
        'default'   => new xmlrpcval(false, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    if (!$disabled['fulltext']) {
        $option = new xmlrpcval(array(
            'title'     => new xmlrpcval($language['order_desc'], 'base64'),
            'value'     => new xmlrpcval('desc', 'base64'),
            'default'   => new xmlrpcval(true, 'boolean'),
        ), 'struct');
        $options[] = $option;
    }
    
    $field = new xmlrpcval(array(
        'type'      => new xmlrpcval('radio', 'base64'),
        'field'     => new xmlrpcval('ascdesc', 'base64'),
        'title'     => new xmlrpcval('', 'base64'),
        'option'    => new xmlrpcval($options, 'array'),
    ), 'struct');
    
    $field_list[] = $field;
    
    // add option for search range
    $options = array();
    
    $option = new xmlrpcval(array(
        'title'     => new xmlrpcval($language['search_allforum'], 'base64'),
        'value'     => new xmlrpcval('all', 'base64'),
        'default'   => new xmlrpcval(true, 'boolean'),
    ), 'struct');
    
    $options[] = $option;
    
    
    foreach($forums as $forum) 
    {
        $option = new xmlrpcval(array(
            'title'     => new xmlrpcval($forum[2], 'base64'),
            'value'     => new xmlrpcval($forum[1], 'base64'),
            'default'   => new xmlrpcval(false, 'boolean'),
        ), 'struct');
        
        $options[] = $option;
    }
    
    $field = new xmlrpcval(array(
        'type'      => new xmlrpcval('checkbox', 'base64'),
        'field'     => new xmlrpcval('srchfid', 'base64'),
        'title'     => new xmlrpcval($language['search_range'], 'base64'),
        'option'    => new xmlrpcval($options, 'array'),
    ), 'struct');
    
    $field_list[] = $field;
    
    return new xmlrpcresp(new xmlrpcval($field_list, 'array'));
}

function search_topic_func()
{
    global $threadlist, $_G, $searchid, $result_num;
    
    $topic_list = array();
    foreach($threadlist as $thread)
    {
        $xmlrpc_topic = new xmlrpcval(array(
            'forum_id'          => new xmlrpcval($thread['fid']),
            'forum_name'        => new xmlrpcval(basic_clean($thread['forumname']), 'base64'),
            'topic_id'          => new xmlrpcval($thread['tid']),
            'topic_title'       => new xmlrpcval(basic_clean($thread['subject']), 'base64'),
            'post_author_name'  => new xmlrpcval(basic_clean($thread['author']), 'base64'),
            'can_subscribe'     => new xmlrpcval($_G['uid'] ? true : false, 'boolean'),
            'is_subscribed'     => new xmlrpcval(is_subscribed($thread['tid']), 'boolean'),
            'is_closed'         => new xmlrpcval($thread['folder'] == 'lock' ? true : false, 'boolean'),
            'icon_url'          => new xmlrpcval(get_user_avatar($thread['authorid'])),
            'post_time'         => new xmlrpcval(mobiquo_iso8601_encode($thread['dblastpost']), 'dateTime.iso8601'),
            'reply_number'      => new xmlrpcval(intval($thread['replies']), 'int'),
            'view_number'       => new xmlrpcval(intval($thread['views']), 'int'),
            'new_post'          => new xmlrpcval($thread['new'] ? true : false, 'boolean'),
            'short_content'     => new xmlrpcval(basic_clean($thread['message']), 'base64'),
        ), 'struct');

        $topic_list[] = $xmlrpc_topic;
    }

    $response = new xmlrpcval(
        array(
            'total_topic_num' => new xmlrpcval($result_num, 'int'),
            'searchid'        => new xmlrpcval($searchid, 'string'),
            'topics'          => new xmlrpcval($topic_list, 'array')
        ),
        'struct'
    );

    return new xmlrpcresp($response);
}

function search_post_func()
{
    global $postlist, $searchid, $result_num;
    
    $post_list = array();
    foreach($postlist as $post)
    {
        $post_subject = $post['p_subject'] ? $post['p_subject'] : 'RE: '.$post['subject'];
        $xmlrpc_post = new xmlrpcval(array(
            'forum_id'          => new xmlrpcval($post['fid']),
            'forum_name'        => new xmlrpcval(basic_clean($post['forumname']), 'base64'),
            'topic_id'          => new xmlrpcval($post['tid']),
            'topic_title'       => new xmlrpcval(basic_clean($post['subject']), 'base64'),
            'post_id'           => new xmlrpcval($post['pid']),
            'post_title'        => new xmlrpcval(basic_clean($post_subject), 'base64'),
            'post_author_name'  => new xmlrpcval(basic_clean($post['p_author']), 'base64'),
            'can_subscribe'     => new xmlrpcval($_G['uid'] ? true : false, 'boolean'),
            'is_subscribed'     => new xmlrpcval(is_subscribed($post['tid']), 'boolean'),
            'is_closed'         => new xmlrpcval($post['folder'] == 'lock' ? true : false, 'boolean'),
            'post_time'         => new xmlrpcval(mobiquo_iso8601_encode($post['p_dateline']), 'dateTime.iso8601'),
            'icon_url'          => new xmlrpcval(get_user_avatar($post['p_authorid'])),
            'reply_number'      => new xmlrpcval(intval($post['replies']), 'int'),
            'view_number'       => new xmlrpcval(intval($post['views']), 'int'),
            'new_post'          => new xmlrpcval($post['new'] ? true : false, 'boolean'),
            'short_content'     => new xmlrpcval(basic_clean($post['message']), 'base64'),
        ), 'struct');

        $post_list[] = $xmlrpc_post;
    }

    $response = new xmlrpcval(
        array(
            'total_post_num' => new xmlrpcval($result_num, 'int'),
            'searchid'       => new xmlrpcval($searchid, 'string'),
            'posts'          => new xmlrpcval($post_list, 'array')
        ),
        'struct'
    );

    return new xmlrpcresp($response);
}

function xmlresptrue()
{
    return new xmlrpcresp(new xmlrpcval(
        array(
            'result' => new xmlrpcval(true, 'boolean'),
            'result_text' => new xmlrpcval('', 'base64'),
        ), 'struct')
    );
}

function get_home_data()
{
    global $home_array;
    
    return new xmlrpcresp(new xmlrpcval($home_array, 'array'));
}

function get_home()
{
    global $home_array;
    
    return new xmlrpcresp(new xmlrpcval($home_array, 'array'));
}
 