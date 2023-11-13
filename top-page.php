<?php
session_start();
if (!isset($_SESSION['user'])) {
  // ログインしていない場合、ログインページにリダイレクト
  header("Location: login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" type="text/css" href="howl-sns.css">
</head>

<body>
  <?php
  require_once('db-connect.php');

  ?>
  <h1>ようこそ</h1>
  <div>
    <p><a href="diary\diary.php">日記投稿</a></p>
    <p><a href="diary\diary_view.php">日記閲覧</a>
    <p><a href="friend\friend.php">友達一覧&チャット</a>
    <p><a href="friend\group_list.php">グループチャット</a></p>
    <p><a href="user_info\my_account.php">アカウント情報</a>
    <p><a href="user_info\logout.php">ログアウト</a></p>
  </div>

</body>

</html>