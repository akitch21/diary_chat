<?php
session_start();
if (!isset($_SESSION['user'])) {
    // ログインしていない場合、ログインページにリダイレクト
    header("Location: login.php");
    exit;
}

require_once '../db-connect.php';

// ユーザー情報をセッションから取得
$username = $_SESSION['user']['username'];

$query = "SELECT random_id FROM users WHERE username = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$username]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$randomID = $result['random_id'];


?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>アカウント情報</title>
  <link rel="stylesheet" type="text/css" href="user_info.css">
</head>
<body>
<nav>
    <ul>
      <li><a href="../top-page.php">トップページ</a></li>
    </ul>
  </nav>
  <h1>アカウント情報</h1>
  <p>ユーザー名: <?php echo $username; ?></p>
  <p>個別認識ID: <?php echo $randomID; ?></p>
  <p><a href = "../user_info/secession.php">退会しますか？</a></p>
</body>
</html>
