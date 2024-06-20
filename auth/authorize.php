<?php
require '../lib/threads.php';
$auth = (new threads)->authorize();
if($argv[0]){
    var_dump(urldecode($auth));
}
