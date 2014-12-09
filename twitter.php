<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>もっと読む</title>
    <script scr="http://code.jquery.com/jquery-1.10.2.min.js"></script>
</head>
<body>

<?php
// 参考URL
// http://apitip.com/twitter/38
// http://koukitips.net/twitter-api-1-1/
// http://www.tryphp.net/2012/01/05/phpapptwitter-public_timeline/
// https://dev.twitter.com/docs/api/1.1/get/search/tweets

// TwitterOAuthとはTwitterによる認証方式である。
// TwitterOAuthを使うとサイトのログインに使用できる
// ログインしたユーザーのTwitter情報を利用できる
require_once("twitteroauth/twitteroauth.php");

$consumerKey = "";
$consumerSecret = "";
$accessToken = "";
$accessTokenSecret = "";

// コンシューマキー、コンシューマシークレッ、アクセストークン、アクセスシークレットを使ってTwitterOAuthを生成する。
// TwitterOAuthクラスをnew演算子クラスでインスタンス化すして、変数$twObjに代入する
$twObj = new TwitterOAuth($consumerKey,$consumerSecret,$accessToken,$accessTokenSecret);

$keywords = "京阪電車";

$param = array(
    "q"=>$keywords,                  // keyword
    "lang"=>"ja",                   // language
    "count"=>100,                   // number of tweets
    "result_type"=>"recent",       // result type
    "include_entities"=>true       // entities
);

$json = $twObj->OAuthRequest(
    "https://api.twitter.com/1.1/search/tweets.json",
    "GET",
    $param);

$result = json_decode($json, true);

?>

<?php
// 参考URL
// http://www.ore-memo.com/1487.html 正規表現について
// http://mayer.jp.net/?p=185 Twitterの#ハッシュタグに自動リンク
// http://dotinstall.com/lessons/basic_regexp/5212 @スクリーンネームに自動リンク
// http://www.phppro.jp/qa/688 文中にURLがあれば、それをリンク化したい
// http://d.hatena.ne.jp/t_krm/20120124/1327367640 ツイートの#と@とURLをリンクに変換する
// http://www.luck2.co.jp/2506.html 【PHP】Twitterの日本語ハッシュに対応する正規表現

if($result['statuses']){
    foreach($result['statuses'] as $tweet){
?>
        <?php
            // 正規表現
            // ユーザ名への自動リンク←1.全角空白文字、半角空白文字、全角英数字を含めるようにする 2.ハッシュタグが含まれているので除く 3.℃（摂氏の度）,＠,々,小括弧を含めるようにする
            // $tweet['user']['name'] = preg_replace("/(w*[一-龠_ぁ-ん_ァ-ヴーａ-ｚＡ-Ｚa-zA-Z0-9]+|[a-zA-Z0-9_]+|[a-zA-Z0-9_]w*)/u", " <a href=\"https://twitter.com/\\1\" target=\"twitter\">\\1</a>", $tweet['user']['name']);

            // 名前へのリンク(0回以上すべての文字列)
            $tweet['user']['name'] = preg_replace("/(.*)/u", " <a href=\"https://twitter.com/\\1\" target=\"twitter\">\\1</a>", $tweet['user']['name']);
            // ユーザー名（スクリーンネーム）へのリンク
            $tweet['user']['screen_name'] = preg_replace("/([A-Za-z0-9_]{1,15})/", " <a href=\"https://twitter.com/\\1\" target=\"twitter\">@\\1</a>", $tweet['user']['screen_name']);

            // テキスト中の#（ハッシュタグ）へのリンク
            $tweet['text'] = preg_replace("/\s#(w*[一-龠_ぁ-ん_ァ-ヴーａ-ｚＡ-Ｚa-zA-Z0-9]+|[a-zA-Z0-9_]+|[a-zA-Z0-9_]w*)/u", " <a href=\"https://twitter.com/search/%23\\1\" target=\"twitter\">#\\1</a>", $tweet['text']);
            // テキスト中の@（スクリーンネーム）へのリンク
            $tweet['text'] = preg_replace("/(@[A-Za-z0-9_]{1,15})/", " <a href=\"https://twitter.com/\\1\" target=\"twitter\">\\1</a>", $tweet['text']);
            // テキスト中のURLへのリンク
            $tweet['text'] = preg_replace("/(http:\/\/t.co\/[a-zA-Z0-9]{10})/", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $tweet['text']);
          ?>

        <ul>
          <li><?php echo date('Y-m-d H:i:s', strtotime($tweet['created_at'])); ?></li>
          <li><?php echo $tweet['user']['name']; ?></li>
          <li><?php echo $tweet['user']['screen_name']; ?></li>
          <li><img src="<?php echo $tweet['user']['profile_image_url']; ?>" /></li>
          <li><?php echo $tweet['text']; ?></li>
          <li><?php echo $tweet['id']; ?></li>

          <script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
            <img src="https://si0.twimg.com/images/dev/cms/intents/icons/reply_hover.png">
            <p><a href="https://twitter.com/intent/tweet?in_reply_to= <?php echo $tweet['id']; ?>" target="_blank">Reply</a></p>
            <img src="https://si0.twimg.com/images/dev/cms/intents/icons/favorite_on.png">
            <p><a href="https://twitter.com/intent/retweet?tweet_id= <?php echo $tweet['id']; ?>" target="_blank">Retweet</a></p>
            <img src="https://si0.twimg.com/images/dev/cms/intents/icons/retweet_on.png">
            <p><a href="https://twitter.com/intent/favorite?tweet_id= <?php echo $tweet['id']; ?>" target="_blank">Favorite</a></p>
            <li><img src="<?php echo $tweet['entities']['media'][0]['media_url_https']; ?>"></li>
        </ul>

  <?php } ?>
    <?php }else{ ?>
    <div class="twi_box">
        <p class="twi_tweet">関連したつぶやきがありません。</p>
    </div>
<?php } ?>
</body>
</html>