<?php
session_start();

if (!isset($_SESSION['user'])) {
    // ログインしていない場合、リダイレクトまたはメインページに移動
    header("Location: login.php");
    exit;
}

if (isset($_POST['logout'])) {
    // セッションを破棄してログアウト
    session_destroy();

    // ログアウト後のリダイレクト先を設定
    header("Location: login.php"); // ログインページにリダイレクト
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="user_info.css">
  <title>ログアウト</title>
</head>
<body>
  <p>ログアウトしますか？</p>
  <form action="" method="POST">
    <input type="submit" name="logout" value="ログアウト">
  </form>
</body>
</html>
