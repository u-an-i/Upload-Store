<?php
    function normalise_domain_path($domain_path) {
        return preg_replace('/\/?$/', '', $domain_path);
    }
    
    function try_run($name_of_callable, $argument) {
        global $$name_of_callable;
        is_callable($name_of_callable) ? $name_of_callable($argument) : (is_callable($$name_of_callable) ? $$name_of_callable($argument) : 0);
    }