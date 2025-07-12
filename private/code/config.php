<?php
    require_once 'private/code/utilities.php';
    
    $config = json_decode(file_get_contents('private/config.json'), true);
    
    if(!$config || !array_key_exists('serviceEnabled', $config) || !$config['serviceEnabled']) {
        try_run('config_error', 'service not enabled');
        exit;
    }
    
    enum Type {
        case EMAIL;
        case TEXT;
        case BOOL;
        case ARRAY;
        
        public function underlies($value, $optional = false) : bool {
            return match($this) {
                Type::EMAIL => is_string($value) && ($optional ? strlen($value) === 0 || filter_var($value, FILTER_VALIDATE_EMAIL) : (strlen($value) > 0 && filter_var($value, FILTER_VALIDATE_EMAIL))),
                Type::TEXT => is_string($value) && (strlen($value) > ($optional ?  -1 : 0)),
                Type::BOOL => is_bool($value),
                Type::ARRAY => is_array($value)
            };
        }
    }
    
    function get_config($property, Type $type, $optional = false) {
        global $config;
        if(array_key_exists($property, $config)) {
            $value = $config[$property];
            if($type->underlies($value, $optional)) {
                return $value;
            }
        }
        try_run('config_error', 'config broken');
        exit;
    }