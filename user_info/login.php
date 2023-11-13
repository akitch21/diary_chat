<?php
session_start();

if (isset($_SESSION['user'])) {
    // 既にログインしている場合、リダイレクトまたはメインページに移動
    header("Location: ../top-page.php");
    exit;
}

require_once '../db-connect.php';

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // データベースからユーザー情報を取得
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // ユーザー名とパスワードが一致する場合、ログイン成功
        $_SESSION['user'] = $user;
        header("Location: ../top-page.php"); // ログイン後のページにリダイレクト
    } else {
        // ログイン失敗
        echo "ユーザー名またはパスワードが正しくありません。";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ログイン</title>
  <link rel="stylesheet" type="text/css" href="login.css">
  <link rel="stylesheet" type="text/css" href="user_info.css">
</head>
<body>
  <h1>ログイン</h1>
  <form action="" method="POST">
    <p>ユーザー名</p>
    <input type="text" name="username">
    <p>パスワード</p>
    <input type="password" name="password"> 
    <p><input type="submit" name="submit" value="ログイン"></p>
  </form>
  <p>アカウントをお持ちでない場合：<a href="../user_info/register.php">新規登録</a></p>
</body>
</html>
