<?php
    require_once 'private/code/config.php';
    
    function get_private_storagename($public_identifier) {
        return md5($public_identifier . get_config('salt', Type::TEXT));
    }
    
    function sanitise_filename($filename) {
        return preg_replace('/(^[\.]+)|[^A-Za-z0-9_\-,\.()]/', '_', $filename);
    }