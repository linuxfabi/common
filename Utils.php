<?php
class Utils {
    
    
    function getRequest($name, $default = '', $filter = true) {
        if (isset($_REQUEST[$name])) {
            if ($filter) {
                if (is_array($_REQUEST[$name])) {
                    array_map('htmlentities', $_REQUEST[$name]);
                    return $_REQUEST[$name];
                } else {
                    return htmlentities($_REQUEST[$name]);
                }
            }
            return $_REQUEST[$name];
        }
        return $default;
    }
}
?>