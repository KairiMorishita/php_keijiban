<?php
session_start();

$db['host'] = "localhost";  // DBサーバのURL
$db['user'] = "root";  // ユーザー名
$db['pass'] = "root";  // ユーザー名のパスワード
$db['dbname'] = "php_keijiban";  // データベース名

//ログイン状態チェック
if(!isset($_SESSION["NAME"])){
    header("Location: Logout.php");
    exit;
}

//エスケープする関数
function h($s){
return htmlspecialchars($s,ENT_QUOTES,'UTF-8');
}

$dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);

//投稿ボタンが押された場合
if(isset($_POST["message"])){
    if (!empty($_POST["message"])) {
    //送信されたname="message"とname="user_name"の値を取得する
    $message = trim($_POST['message']);
    $user = trim($_SESSION["NAME"]);

    // 投稿内容をデータベースに保存
    try {
        $pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
        $stmt = $pdo->prepare("INSERT INTO posts(text, user_id) VALUES (?, ?)");
        $stmt->execute(array($message, $_SESSION["ID"]));
    } catch(PDOException $e) {
        $errorMessage = 'データベースエラー';
        // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
        echo $e->getMessage();
    }
    }else{
        echo '投稿内容が未入力です。';
    }
}

// 投稿内容とユーザー名を取得
try {
    $pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
    $sql = "SELECT text,name,user_id,posts.id FROM posts INNER JOIN users ON posts.user_id = users.id";
    $post_list = $pdo->query($sql)->fetchALL();
} catch(PDOException $e) {
    $errorMessage = 'データベースエラー';
    // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
    echo $e->getMessage();
}

// 逆順に並べ替える
$post_list = array_reverse($post_list);

//　削除ボタンが押された時
if(isset($_POST["id"])){
    // 投稿内容を削除
    try {
        $pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
        $stmt = $pdo->prepare("DELETE FROM posts Where id = :id");
        $stmt->execute(array(':id' => $_POST["id"]));
        header("Location: index.php");
    } catch(PDOException $e) {
        $errorMessage = 'データベースエラー';
        // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
        echo $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html>
<head lang="ja">
<meta charset="utf-8">
<title>PHP掲示板</title>
</head>
<body>
<h1>PHP掲示板</h1>

<!-- ユーザー名をechoで表示 -->
<p>ようこそ<u><?php echo htmlspecialchars($_SESSION["NAME"], ENT_QUOTES); ?></u>さん</p>

<p><a href="Logout.php">ログアウト</a></p>

<!--ここで投稿内容を送信する-->
<form action="" method="post">
    投稿内容:<input type="text" name="message"><br>
            <input type="submit" name="send_message" value="投稿">
</form>

<h2>投稿一覧</h2>
<ul>
<!--post_listがある場合-->
<?php if (!empty($post_list)){ ?>
    <!--post_listの中身をひとつづつ取り出し表示する-->
    <?php foreach ($post_list as $post){ ?>
    <li>
        <?php echo $post["name"];?>
        <?php echo $post["text"];?>
    </li>
    <?php if ($_SESSION["ID"] == $post["user_id"]) :?>
        <form action="" method="post">
            <input type="hidden" name="id" value=<?php echo $post["id"] ?>>
            <input type="submit"  value="削除">
        </form>
    <?php endif; ?>
    <?php } ?>
<?php }else { ?>
    <li>まだ投稿はありません。</li>
<?php } ?>
</ul>
</body>
</html>
