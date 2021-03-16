<?php

return [
    
    // used from mail
    // change here email from web.php authentication
    'senderEmail' => 'info@simplemessages',
    'senderName' => 'Info from Simple Messages',

    // this is used for console 
    // to receive status from kannel service

    'url_host' => 'http://95.77.99.27:8080/',

    'url_kannel' => 'http://localhost:13003/status.xml',
    'url_kannel_status' => 'http://localhost:13003/status',
    'kannel_admin_port' => 13003,
    'kannel_password' => 'simple_messages',
    

    'limit_service_sms_send_per_minute' => 100
];
