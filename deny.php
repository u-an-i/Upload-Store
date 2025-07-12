<?php
    $config_error = function($reason) {
        echo $reason;
    };
    
    $entry_denied = function($reason) {
        echo $reason;
    };
    
    require_once 'private/code/config.php';
    require_once 'private/code/borderpatrol.php';

    $emailaddress = file_get_contents($check_filename);
    unlink($check_filename);
    if($emailaddress) {
        $hostReceiverName = get_config('hostReceiverName', Type::TEXT);
        if(mail(
            $emailaddress,
            'denied upload file(s) to ' . $hostReceiverName,
            'denied upload file(s) to ' . $hostReceiverName,
            'From:' . get_config('serverFromEmail', Type::EMAIL)
        )) {
            echo 'denied upload - informed by email';
        } else {
            echo 'denied upload - failed to inform by email';
        }
        exit;
    }

    echo 'failure';