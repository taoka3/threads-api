# threads-api v1.0
threads apiに投稿するため雛形<br>
前提条件：threadsの設定とthreadsのアプリ設定を終えていること．<br>
詳しくはこちら<br>
https://developers.facebook.com/docs/threads/get-started

#上記の設定を行った後、下記の通り手順を踏む．<br>

1. 出力されたリンクをクリック
```php:auth/authorize.php
<?php
require '../lib/threads.php';
$auth = (new threads)->authorize();
if($argv[0]){
    var_dump(urldecode($auth));
}

```
2. リダイレクトページの処理が走る
```php:auth/redirectCallback.php
<?php
require '../lib/threads.php';
(new threads)->redirectCallback()->getAccessToken()->changeLongAccessToken()->save();
```

3. テスト投稿する
```php:auth/post.php
<?php
require '../lib/threads.php';
if($argv[0]){
    var_dump($argv);
    (new threads)->getlongAccessTokenAndUserId()->post('Threadsの作り方をQiitaに記載しています https://qiita.com/taoka-toshiaki/items/e606e2cfa31c6e2ed771')->publishPost();
}
```
