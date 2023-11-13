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

$sql = "CREATE TABLE IF NOT EXISTS friend_message"
    . " ("
    . "message_id INT AUTO_INCREMENT PRIMARY KEY,"
    . "user_id VARCHAR(255),"
    . "recipient_id VARCHAR(255),"
    . "message_text TEXT,"
    . "timestamp DATETIME,"
    . "is_read INT"
    . ");";
$stmt = $pdo->query($sql);
if (!$stmt) {
    die("データベースエラー: " . print_r($pdo->errorInfo(), true));
}

// レシピエントIDをURLパラメータから取得
$recipient_id = isset($_GET['recipient']) ? $_GET['recipient'] : '';

// メッセージ送信があった場合
if (isset($_POST['submit'])) {
    // フォームから受け取ったテキスト
    $message_text = $_POST['message_text'];

    // ログイン中のユーザー名
    $user_id = $_SESSION['user']['username'];

    // 現在の日時
    $timestamp = date('Y-m-d H:i:s');

    // メッセージをデータベースに挿入
    $sql = "INSERT INTO friend_message (user_id, recipient_id, message_text, timestamp, is_read) VALUES (:user_id, :recipient_id, :message_text, :timestamp, 0)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $stmt->bindParam(':recipient_id', $recipient_id, PDO::PARAM_STR);
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
    <title>メッセージ</title>
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
        <input type="hidden" name="recipient" value="<?php echo htmlspecialchars($recipient_id); ?>" readonly>
        <textarea name="message_text"></textarea>
        <p><input type="submit" name="submit"></p>
    </form><hr>
    <?php

    $user_id = $_SESSION['user']['username'];

    $sql = 'SELECT * FROM friend_message WHERE user_id = :user_id OR user_id = :recipient_id ORDER BY timestamp DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $stmt->bindParam(':recipient_id', $recipient_id, PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
        //$rowの中にはテーブルのカラム名が入る
        echo $row['user_id'] . '<br>';
        echo $row['message_text'] . '<br>';
        echo $row['timestamp'] . '<br>';

        echo "<hr>";
    }
    ?>
</body>

</html>
