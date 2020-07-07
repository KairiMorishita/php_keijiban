<?php
?>
<!DOCTYPE html>
<html>
<head lang="ja">
<meta charset="utf-8">
<title>PHP掲示板</title>
<link rel="stylesheet" href="css/index.css">
</head>
<body>
<h1>PHP掲示板</h1>

<!--ここで投稿内容を送信する-->
<form action="" method="post">
    投稿内容:<input type="text" name="message">
    ユーザー名:<input type="text" name="user_name">
    <input type="submit" name="send_message" value="投稿">
</form>

<h2>投稿一覧</h2>
<ul>
    <li><!--ここに投稿が表示される--></li>
</ul>
</body>
</html>
