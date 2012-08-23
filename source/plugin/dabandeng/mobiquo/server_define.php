<?php

defined('IN_MOBIQUO') or exit;

$server_param = array(

    'authorize_user' => array(
        'function'  => 'authorize_user_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcString)),
        'docstring' => 'authorize need two parameters,the first is user name(Base64), second is password.',
    ),
    
    'login' => array(
        'function'  => 'login_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64),
                             array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString, $xmlrpcBase64)),
        'docstring' => 'user name(Base64), password(Base64), question id(string), answer(base64)',
    ),

    'get_forum' => array(
        'function'  => 'get_forum_func',
        'signature' => array(array($xmlrpcArray)),
        'docstring' => 'no need parameters for get_forum.',
    ),

    'get_topic' => array(
        'function'  => 'get_topic_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcInt, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcInt),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt),
                             array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'parameter should be array(string,int,int,string)',
    ),

    'get_thread' => array(
        'function'  => 'get_thread_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcInt, $xmlrpcBoolean),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcInt),
                             array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'parameter should be array(string,int,int)',
    ),

    'get_raw_post' => array(
        'function'  => 'get_raw_post_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'parameter should be array(string)',
    ),
    
    'get_quote_post' => array(
        'function'  => 'get_quote_post_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'parameter should be array(string)',
    ),

    'save_raw_post' => array(
        'function'  => 'save_raw_post_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBoolean)),
        'docstring' => 'parameter should be array(string, base64, base64)',
    ),

    'get_user_topic' => array(
        'function'  => 'get_user_topic_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcBase64)),
        'docstring' => 'parameter should be array(string)',
    ),

    'get_user_reply_post' => array(
        'function'  => 'get_user_reply_post_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcBase64)),
        'docstring' => 'parameter should be array(int,int,int,string)',
    ),

    'get_new_topic' => array(
        'function'  => 'get_new_topic_func',
        'signature' => array(array($xmlrpcArray),
                             array($xmlrpcStruct, $xmlrpcInt, $xmlrpcInt)),
        'docstring' => 'no need parameters for get_forum',
    ),

    'get_subscribed_topic' => array(
        'function'  => 'get_subscribed_topic_func',
        'signature' => array(array($xmlrpcArray),
                             array($xmlrpcStruct, $xmlrpcInt, $xmlrpcInt)),
        'docstring' => 'no need parameters for get_subscribed_topic',
    ),
    
    'get_subscribed_forum' => array(
        'function'  => 'get_subscribed_forum_func',
        'signature' => array(array($xmlrpcArray)),
        'docstring' => 'no need parameters for get_subscribed_forum',
    ),
    
    'subscribe_forum' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'subscribe_topic need one parameters as forum id.',
    ),
    
    'unsubscribe_forum' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'unsubscribe_topic need one parameters as forum id.',
    ),

    'get_user_info' => array(
        'function'  => 'get_user_info_func',
        'signature' => array(array($xmlrpcStruct),
                             array($xmlrpcStruct, $xmlrpcBase64)),
        'docstring' => 'parameter should be array(sring)',
    ),
    
    'register' => array(
        'function'  => 'register_func',
        'signature' => array(array($xmlrpcStruct),
                             array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBase64)),
        'docstring' => 'parameter should be array(sring)',
    ),

    'get_config' => array(
        'function'  => 'get_config_func',
        'signature' => array(array($xmlrpcArray)),
        'docstring' => 'no need parameters for get_forum',
    ),

    'logout_user' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcArray)),
        'docstring' => 'no need parameters for logout',
    ),

    'create_topic' => array(
        'function'  => 'create_topic_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBase64)),
        'docstring' => 'parameter should be array(int,string,string)',
    ),
    
    'new_topic' => array(
        'function'  => 'new_topic_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString, $xmlrpcArray),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString, $xmlrpcArray, $xmlrpcArray)),
        'docstring' => 'parameter should be array(string,byte,byte,[string],[array],[string])',
    ),

    'reply_topic' => array(
        'function'  => 'reply_topic_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBase64)),
        'docstring' => 'parameter should be array(int,string,string)',
    ),
    
    'reply_post' => array(
        'function'  => 'reply_post_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcArray),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcArray, $xmlrpcArray),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcArray, $xmlrpcArray, $xmlrpcBoolean)),
        'docstring' => 'parameter should be array(int,string,string)',
    ),

    'subscribe_topic' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'subscribe_topic need one parameters as topic id.',
    ),

    'unsubscribe_topic' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'unsubscribe_topic need one parameters as topic id.',
    ),

    'get_inbox_stat' => array(
        'function'  => 'get_inbox_stat_func',
        'signature' => array(array($xmlrpcArray)),
        'docstring' => 'no parameter but need login first',
    ),

    'get_box_info' => array(
        'function'  => 'get_box_info_func',
        'signature' => array(array($xmlrpcArray)),
        'docstring' => 'no parameter but need login first',
    ),

    'get_box' => array(
        'function'  => 'get_box_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcInt, $xmlrpcDateTime),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcInt),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcInt),
                             array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'parameter should be array(string,int,int,date)',
    ),

    'get_message' => array(
        'function'  => 'get_message_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBoolean)),
        'docstring' => 'get_message need one parameter as message id'
    ),

    'delete_message' => array(
        'function'  => 'delete_message_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'get_message need one parameter as message id'
    ),

    'create_message' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcStruct, $xmlrpcArray, $xmlrpcBase64, $xmlrpcBase64),
                             array($xmlrpcStruct, $xmlrpcArray, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcInt, $xmlrpcString)),
        'docstring' => 'parameter should be array(array,string,string,[int, string])',
    ),

    'get_board_stat' => array(
        'function'  => 'get_board_stat_func',
        'signature' => array(array($xmlrpcStruct)),
        'docstring' => 'no parameter',
    ),

    'get_online_users' => array(
        'function'  => 'get_online_users_func',
        'signature' => array(array($xmlrpcStruct)),
        'docstring' => 'no parameter',
    ),

    'attach_image' => array(
        'function'  => 'attach_image_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString, $xmlrpcString)),
        'docstring' => 'parameter should be array()',
    ),

    'mark_all_as_read' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcStruct)),
        'docstring' => 'no parameter',
    ),
    
    'search_topic' => array(
        'function'  => 'search_topic_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcInt, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcInt),
                             array($xmlrpcStruct, $xmlrpcBase64)),
        'docstring' => 'parameter should be array()',
    ),
    
    'search_post' => array(
        'function'  => 'search_post_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcInt, $xmlrpcString),
                             array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcInt),
                             array($xmlrpcStruct, $xmlrpcBase64)),
        'docstring' => 'parameter should be array()',
    ),
    
    'get_search_option' => array(
        'function'  => 'get_search_option_func',
        'signature' => array(array($xmlrpcStruct)),
        'docstring' => 'parameter should be array()',
    ),

    'search' => array(
        'function'  => 'search_func',
        'signature' => array(array($xmlrpcStruct, $xmlrpcBase64),
                             array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcInt),
                             array($xmlrpcStruct, array($xmlrpcArray, array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64)), $xmlrpcInt, $xmlrpcInt),
                             array($xmlrpcStruct, array($xmlrpcArray, array($xmlrpcStruct, $xmlrpcBase64, array($xmlrpcArray, $xmlrpcBase64))), $xmlrpcInt, $xmlrpcInt),
                             array($xmlrpcStruct, array($xmlrpcArray, array($xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64))),
                             array($xmlrpcStruct, array($xmlrpcArray, array($xmlrpcStruct, $xmlrpcBase64, array($xmlrpcArray, $xmlrpcBase64))))),
        'docstring' => 'parameter should be array(base64,[int,int])',
    ),
    
    'get_home_data' => array(
        'function'  => 'get_home_data',
        'signature' => array(array($xmlrpcStruct),
                             array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'no parameter',
    ),
    
    'get_home' => array(
        'function'  => 'get_home',
        'signature' => array(array($xmlrpcStruct),
                             array($xmlrpcStruct, $xmlrpcString)),
        'docstring' => 'no parameter',
    ),

    'login_forum' => array(
        'function'  => 'xmlresptrue',
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64)),
        'docstring' => 'parameter should be)',
    ),
    
    'upload_attach' => array(
        'function'  => 'upload_attach_func',
        'signature' => array(array($xmlrpcStruct)),
        'docstring' => 'parameter should be',
    ),
);
