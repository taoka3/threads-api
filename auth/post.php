<?php
require '../lib/threads.php';
if($argv[0]){
    (new threads)->getlongAccessTokenAndUserId()->post('これはAPI投稿テストです👍️')->publishPost();
}
