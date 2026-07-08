<?php
function clean($str) {
    $str = trim($str);
    if (function_exists('stripslashes')) {
        $str = stripslashes($str);
    }
    return addslashes(strip_tags($str));
}
