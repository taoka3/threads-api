<?php
require '../lib/threads.php';
$auth = (new threads)->authorize();
var_dump($auth);
