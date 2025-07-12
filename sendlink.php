<?php
    $config_error = function($reason) {
        echo $reason;
    };
    
    $entry_denied = function($reason) {
        echo $reason;
    };
    
    require_once 'private/code/config.php';
    require_once 'private/code/borderpatrol.php';
    require_once 'private/code/storage.php';
    require_once 'private/code/utilities.php';
    
    $emailaddress = file_get_contents($check_filename);
    if($emailaddress) {
        $dirname = 'private/files/' . get_private_storagename($_REQUEST['c']);
        if(!file_exists($dirname)) {
            if(mkdir($dirname, 0770)) {
                if(mail(
                    $emailaddress,
                    'link to let upload file(s) to ' . get_config('hostReceiverName', Type::TEXT),
                    'https://' . normalise_domain_path(get_config('base', Type::TEXT)) . '/input.php?c=' . $_REQUEST['c'],
                    'From:' . get_config('serverFromEmail', Type::EMAIL)
                )) {
                    echo 'email sent';
                } else {
                    echo 'sending email failed';
                }
            } else {
                echo 'error on the server';
            }
            exit;
        }
    }

    echo 'failure';