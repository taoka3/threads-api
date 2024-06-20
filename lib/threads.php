<?php
//threads apiに投稿するため雛形

require "../config/app.php";
class threads
{
    public $appId = APPID;
    public $apiSecret = APISECRET;
    public $redirectUri = REDIRECT_URI;
    public $userId = null;
    public $longAccessToken = null;
    public $code = null;
    public $endPointUri = 'https://graph.threads.net/';
    public $version = 'v1.0/';
    public $result = null;
    public $creation_id = null;
    public $dbh = null;

    public function __construct()
    {
        $dsn = 'mysql:dbname=' . DBNAME . ';host=' . HOST;
        try {
            $this->dbh = new PDO($dsn, DBUSER, DBPASSWORD);

            if ($this->dbh == null) {
                //print('接続に失敗しました');
            } else {
                //print('接続に成功しました');
            }
        } catch (PDOException $e) {
            print('Error:' . $e->getMessage());
            die();
        }

        return $this;
    }

    /**
     * authorize urlを叩いてアプリを認証
     */
    public function authorize($uri = 'authorize')
    {
        $url = 'https://threads.net/oauth/authorize';


        $ch = curl_init($url);

        $headers = [
            'Content-Type: application/x-www-form-urlencoded; charset=utf-8'
        ];
        $params = [
            'client_id' => $this->appId,
            'scope' => 'threads_basic',
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
        ];

        return $url . http_build_query($params);
    }

    /**
     * long_access_tokenとuser_idを読み込み
     */
    public function getlongAccessTokenAndUserId()
    {
        try {
            $sql = 'select user_id, long_access_token from threads where user_id = ?';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$this->userId]);

            while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->userId = (string)$result['user_id'];
                $this->longAccessToken = $result['long_access_token'];
            }
        } catch (PDOException $e) {
            print('Error:' . $e->getMessage());
            die();
        }

        return $this;
    }

    /**
     * 認証後．コールバックされるのでcodeを取得する
     */
    public function redirectCallback()
    {

        $json = file_get_contents('php://input');
        $code = str_replace('#_', '', $_GET['code']);
        $response = json_decode($json);
        $this->code = $code;

        return $this;
    }

    /**
     * ショートアクセストークンを取得する
     */
    public function getAccessToken($uri = 'oauth/access_token')
    {
        $url = $this->endPointUri . $uri;

        $ch = curl_init($url);

        $headers = [
            'Content-Type: application/x-www-form-urlencoded; charset=utf-8'
        ];
        $params = [
            'client_id' => $this->appId,
            'client_secret' => $this->apiSecret,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUri,
            'code' => $this->code
        ];
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        curl_close($ch);
        $this->result = json_decode($response);
        $this->userId = $this->result->user_id;

        return $this;
    }

    /**
     * ショートアクセストークンをロングアクセストークンへ変換する
     */
    public function changeLongAccessToken()
    {
        $url = "https://graph.threads.net/access_token?grant_type=th_exchange_token&client_secret={$this->apiSecret}&access_token={$this->result?->access_token}";
        $response = file_get_contents($url);
        $res = json_decode($response);
        $this->longAccessToken = $res->access_token;

        return $this;
    }

    /**
     * ロングアクセストークンをリフレッシュする 60日で切れるらしい．
     */
    public function refreshLongAccessToken()
    {
        $url = "https://graph.threads.net/refresh_access_token?grant_type=th_refresh_token&access_token={$this->longAccessToken}";
        $response = file_get_contents($url);
        $res = json_decode($response);
        $this->longAccessToken = $res->access_token;

        return $this;
    }

    /**
     * Threadsへ投稿するためのIDの切り出しを行う
     */
    public function post($text, $imgUrl = null, $media_type = 'TEXT', $uri = '/threads')
    {

        $url = $this->endPointUri . $this->version . $this->userId . $uri;

        $ch = curl_init($url);

        $headers = [
            'Content-Type: application/x-www-form-urlencoded; charset=utf-8'
        ];
        $params = [
            'text' => $text,
            'access_token' => $this->longAccessToken,
            'media_type' => $media_type,
        ];

        if ($media_type === 'IMAGE') {
            $params = $params + ['image_url' => $imgUrl];
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        curl_close($ch);
        $creation_id = json_decode($response);
        $this->creation_id = $creation_id?->id;

        return $this;
    }

    /**
     * Threadsへ投稿する
     */
    public function publishPost($uri = '/threads_publish')
    {
        if ($this->creation_id) {
            $url = $this->endPointUri . $this->version . $this->userId . $uri;

            $ch = curl_init($url);

            $headers = [
                'Content-Type: application/x-www-form-urlencoded; charset=utf-8'
            ];
            $params = [
                'creation_id' => $this->creation_id,
                'access_token' => $this->longAccessToken,
            ];

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response);

            var_dump($response);
        }
        return $this;
    }

    /**
     * データを初回DBに保存する
     */
    public function save()
    {
        try {
            $sql = 'insert into threads (user_id, long_access_token) values (?, ?)';
            $stmt = $this->dbh->prepare($sql);
            $flag = $stmt->execute([(int)$this->userId, $this->longAccessToken]);

            if ($flag) {
                //print('データの追加に成功しました');
            } else {
                //print('データの追加に失敗しました');
            }
        } catch (PDOException $e) {
            print('Error:' . $e->getMessage());
            die();
        }
    }

    /**
     * データを更新する
     */
    public function setUpdate()
    {
        try {
            $sql = 'UPDATE threads SET long_access_token = ? WHERE user_id = ?';
            $stmt = $this->dbh->prepare($sql);
            $flag = $stmt->execute([$this->longAccessToken, (int)$this->userId]);

            if ($flag) {
                //print('データの追加に成功しました');
            } else {
                //print('データの追加に失敗しました');
            }
        } catch (PDOException $e) {
            print('Error:' . $e->getMessage());
            die();
        }
    }
}
