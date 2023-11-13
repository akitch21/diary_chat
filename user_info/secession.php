<?php
session_start();

if (!isset($_SESSION['user'])) {
    // ログインしていない場合、ログインページにリダイレクト
    header("Location: login.php");
    exit;
}

require_once '../db-connect.php';

if (isset($_POST['secede'])) {
    $username = $_SESSION['user']['username'];

    // ユーザーをデータベースから削除
    $query = "DELETE FROM users WHERE username = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$username]);

    // セッションを破棄し、ログアウト
    session_destroy();

    // ログインページにリダイレクト
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>退会確認</title>
    <link rel="stylesheet" type="text/css" href="user_info.css">
</head>
<body>
    <h1>退会確認</h1>
    <p>本当に退会しますか？</p>
    <form action="" method="POST">
        <button type="submit" name="secede">はい、退会します</button>
    </form>
    <p><a href="my_account.php">戻る</a></p>
</body>
</html>
