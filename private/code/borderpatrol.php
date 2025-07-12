<?php
    require_once 'private/code/storage.php';
    require_once 'private/code/utilities.php';

    $check_filename = 'private/checks/' . get_private_storagename($_REQUEST['c']);
    
    if(!file_exists($check_filename)) {
        try_run('entry_denied', 'authentification missing');
        exit;
    }