# threads-api v1.0
threads apiに投稿するため雛形<br>
前提条件：threadsの設定とthreadsのアプリ設定を終えていること．<br>
<b>※threadsというテーブルを作っています．コードを読んで推測ください😌．</b><br>

詳しくはこちら<br>
https://developers.facebook.com/docs/threads/get-started

スクリーンショット(ヒント)
---
![image](https://github.com/taoka3/threads-api/assets/71567084/2004e9be-d466-4e00-b5a5-4a4fb4eca7c2)
![image](https://github.com/taoka3/threads-api/assets/71567084/9f2361a8-1e51-40bb-847b-c7e46a519059)
![image](https://github.com/taoka3/threads-api/assets/71567084/12304f3b-9895-4da1-95c1-353307521dc5)
![image](https://github.com/taoka3/threads-api/assets/71567084/9a218b1c-baa3-4448-8723-9150b9c30330)
---

# 上記の設定を行った後、下記の通り手順を踏む．<br>

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
    (new threads)->getlongAccessTokenAndUserId()->post('Threadsの作り方をQiitaに記載しています https://qiita.com/taoka-toshiaki/items/e606e2cfa31c6e2ed771')->publishPost();
}
```
