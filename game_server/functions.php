<?php
function log_append($string)
{
    $log_entry = date('d.m.Y_H:i:s', time()) . ' ' . $string . "\n";
    file_put_contents('log.txt', $log_entry, FILE_APPEND);
}
function respond($string)
{
    echo $string;
    log_append($string);
}
