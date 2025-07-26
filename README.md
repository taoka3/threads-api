# threads-api v1.0

このリポジトリは、Threads APIを使って投稿するための雛形です。

## 前提条件

- Threadsの設定が完了していること
- Threadsのアプリ設定が完了していること
- `threads`というテーブルを作成済みであること（詳細はコードを参照）

公式ドキュメント:  
https://developers.facebook.com/docs/threads/get-started

## サンプル画面

スクリーンショット例:
---
![image](https://github.com/taoka3/threads-api/assets/71567084/2004e9be-d466-4e00-b5a5-4a4fb4eca7c2)
![image](https://github.com/taoka3/threads-api/assets/71567084/9f2361a8-1e51-40bb-847b-c7e46a519059)
![image](https://github.com/taoka3/threads-api/assets/71567084/12304f3b-9895-4da1-95c1-353307521dc5)
![image](https://github.com/taoka3/threads-api/assets/71567084/9a218b1c-baa3-4448-8723-9150b9c30330)
---

## 利用手順

1. 認証リンクを取得する  
   `auth/authorize.php` を実行し、表示されたリンクをクリックします。

   ```php
   // auth/authorize.php
   <?php
   require '../lib/threads.php';
   $auth = (new threads)->authorize();
   if($argv[0]){
       var_dump(urldecode($auth));
   }
   ```

2. リダイレクト後の処理  
   認証後、リダイレクト先でアクセストークンを取得・保存します。

   ```php
   // auth/redirectCallback.php
   <?php
   require '../lib/threads.php';
   (new threads)->redirectCallback()->getAccessToken()->changeLongAccessToken()->save();
   ```

3. テスト投稿  
   アクセストークンとユーザーIDを取得し、投稿を行います。

   ```php
   // auth/post.php
   <?php
   require '../lib/threads.php';
   if($argv[0]){
       (new threads)->getlongAccessTokenAndUserId()->post('Threadsの作り方をQiitaに記載しています https://qiita.com/taoka-toshiaki/items/e606e2cfa31c6e2ed771')->publishPost();
   }
   ```

## 備考

- テーブル設計や詳細な処理はコードを参照してください。
- 不明点は公式ドキュメントやコードコメントを参考にしてください。
