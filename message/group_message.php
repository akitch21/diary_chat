<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();
if (!isset($_SESSION['user'])) {
  // ログインしていない場合、ログインページにリダイレクト
  header("Location: ../user_info/login.php");
  exit;
}

require_once "../db-connect.php";

$sql = "CREATE TABLE IF NOT EXISTS group_message"
  . " ("
  . "id INT AUTO_INCREMENT PRIMARY KEY,"
  . "user_id VARCHAR(255),"
  . "recipient_group VARCHAR(225),"
  . "message_text TEXT,"
  . "timestamp DATETIME"
  . ");";
$stmt = $pdo->query($sql);
if (!$stmt) {
  die("データベースエラー: " . print_r($pdo->errorInfo(), true));
}

$recipient_group = isset($_GET['group_name']) ? $_GET['group_name'] : '';


if (isset($_POST['submit'])) {
  // フォームから受け取ったテキスト
  $message_text = $_POST['message_text'];

  // ログイン中のユーザー名
  $user_id = $_SESSION['user']['username'];

  // 現在の日時
  $timestamp = date('Y-m-d H:i:s');

  // メッセージをデータベースに挿入
  $sql = "INSERT INTO group_message (user_id, recipient_group, message_text, timestamp) VALUES (:user_id, :recipient_group, :message_text, :timestamp)";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
  $stmt->bindParam(':recipient_group', $recipient_group, PDO::PARAM_STR);
  $stmt->bindParam(':message_text', $message_text, PDO::PARAM_STR);
  $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR);

  if ($stmt->execute()) {
    echo "メッセージが送信されました。";
  } else {
    echo "メッセージの送信に失敗しました。";
  }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" type="text/css" href="message.css">
</head>

<body>
  <nav>
    <ul>
      <li><a href="../top-page.php">トップページ</a></li>
    </ul>
  </nav>
  <h1>メッセージ</h1>
    <form action="" method="POST">
        <input type="hidden" name="recipient" value="<?php echo htmlspecialchars($recipient_group); ?>" readonly>
        <textarea name="message_text"></textarea>
        <input type="submit" name="submit">
    </form>
    <?php

    $user_id = $_SESSION['user']['username'];

    $sql = 'SELECT * FROM group_message WHERE recipient_group = :recipient_group ORDER BY timestamp DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':recipient_group', $recipient_group, PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'] . ':';
        echo $row['user_id'] . ':';
        echo $row['message_text'] . ':';
        echo $row['timestamp'] . '<br>';

        echo "<hr>";
    }
    ?>

</body>

</html>