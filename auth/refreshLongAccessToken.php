<?php
require '../lib/threads.php';
if($argv[0]){
    (new threads)->getlongAccessTokenAndUserId()->refreshLongAccessToken()->setUpdate();
}
