<?php

return [
    'host'       => env('DING_TALK_HOST', 'https://oapi.dingtalk.com/'),
    'app_key'    => env('DING_TALK_APP_KEY'),
    'app_secret' => env('DING_TALK_APP_SECRET'),
    'agent_id'   => env('DING_TALK_APP_NOTIFICATION_AGENT_ID'),
    'uri_list'   => [
        'get_access_token' => 'gettoken',
        'notify'           => 'topapi/message/corpconversation/asyncsend_v2',
    ],
];