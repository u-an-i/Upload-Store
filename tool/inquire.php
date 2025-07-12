<?php
    $config_error = function($reason) {
        echo $reason;
    };
    
    $entry_denied = function($reason) {
        echo $reason;
    };

    require_once 'private/code/config.php';
    require_once 'private/code/borderpatrol.php';
    require_once 'private/code/utilities.php';
    
    $emailaddress = $_POST['e'];
    
    if(Type::EMAIL->underlies($emailaddress)) {
        file_put_contents($check_filename, $emailaddress);
        
        $whitelist = get_config('whitelist', Type::ARRAY);
        if(in_array($emailaddress, $whitelist)) {
            require_once 'sendlink.php';
        } else {
            if(get_config('enableManualApproval', Type::BOOL)) {
                $domain_path = normalise_domain_path(get_config('base', Type::TEXT));
                if(mail(
                    get_config('manualApprovallEmail', Type::EMAIL),
                    'link requested to let upload file(s) to you',
                    $emailaddress . ' requested a link.<br><br><a href="' .  'https://' . $domain_path . '/sendlink.php?c=' . $_POST['c'] . '">approve</a><br><br><a href="' .  'https://' . $domain_path . '/deny.php?c=' . $_POST['c'] . '">deny</a>',
                    implode("\r\n", ['MIME-Version: 1.0', 'Content-type: text/html; charset=iso-8859-1', 'From:' . get_config('serverFromEmail', Type::EMAIL)])
                )) {
                    echo 'request sent';
                } else {
                    echo 'sending request failed';
                }
            } else {
                require_once 'deny.php';
            }
        }
        exit;
    }
    
    echo 'failure';