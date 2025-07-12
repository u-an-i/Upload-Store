<?php
    require_once 'private/code/config.php';
    require_once 'private/code/borderpatrol.php';
    require_once 'private/code/storage.php';
    
    $emailaddress = file_get_contents($check_filename);
    unlink($check_filename);
    if($emailaddress) {
        $dirname = 'private/files/' . get_private_storagename($_REQUEST['c']);
        if(file_exists($dirname) && is_dir($dirname)) {
            $numfiles = count($_FILES);
            $index = -1;
            $filenames = [];
            while(++$index < $numfiles) {
                if($_FILES['f' . $index]['error'] === UPLOAD_ERR_OK) {
                    $filename = basename($_FILES['f' . $index]['name']);
                    if(move_uploaded_file($_FILES['f' . $index]['tmp_name'], $dirname . '/'. sanitise_filename($filename))) {
                        $filenames[] = $filename;
                    }
                }
            }
            $saved = count($filenames);
            $mail_head = ['From:' . get_config('serverFromEmail', Type::EMAIL)];
            $bcc = get_config('notifyEmail', Type::EMAIL, true);
            if(strlen($bcc) > 0) {
                $mail_head[] = 'BCC:' . $bcc;
            }
            mail(
                $emailaddress,
                $numfiles ? ('uploaded ' . ($_POST['a'] === ('' . $numfiles) ? ($numfiles > 1 ? 'all' : '') : (($numfiles > 1 ? 'some' : '1'))) . ' file' . ($numfiles > 1 ? 's' : '') . ' to ' . get_config('hostReceiverName', Type::TEXT) . ' successfully, saving ' . ($saved ? ($numfiles === $saved ? ($saved !== 1 ? 'all of them' : '') : ($saved !== 1 ? 'some of them' : 'one of them')) . ' succeeded' : 'failed')) : 'no files uploaded',
                'the following ' . $saved . ' file' . ($saved !== 1 ? 's were' : ' was') . ' saved:' . "\r\n\r\n" . implode("\r\n", $filenames) . "\r\n\r\n" . '---' . "\r\n\r\n\r\n" . 'reference: ' . $_POST['c'],
                implode("\r\n", $mail_head)
            );
            if($saved) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 SUCCESS');
                echo "success";
            } else {
                rmdir($dirname);
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 ERROR');
            }
            exit;
        }
    }
    
    header($_SERVER['SERVER_PROTOCOL'] . ' 403 FAILURE');

    function config_error($reason) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 ' . $reason);
    }
    
    function entry_denied($reason) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 ' . $reason);
    }