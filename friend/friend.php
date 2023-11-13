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

$sql = "CREATE TABLE IF NOT EXISTS friend_list"
    . " ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "username VARCHAR(255),"
    . "friend_name VARCHAR(255)"
    . ");";
$stmt = $pdo->query($sql);

if (isset($_POST['add'])) {
    $friend_name = $_POST['friend_name'];
    $friend_id = $_POST['friend_ID'];
    $current_user = $_SESSION['user']['username'];

    // データベースから友だちの情報を取得
    $query = "SELECT username, random_id FROM users WHERE username = :username AND random_id = :random_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(':username' => $friend_name, ':random_id' => $friend_id));
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // 友達が見つかった場合のみ処理を行う
    if ($result) {
        // データベースにデータが存在しない場合のみ挿入
        $checkQuery = "SELECT * FROM friend_list WHERE username = :current_user AND friend_name = :friend_name";
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->execute(array(':current_user' => $current_user, ':friend_name' => $friend_name));

        if ($checkStmt->rowCount() == 0) {
            // データベースにデータを挿入
            $sql = "INSERT INTO friend_list (username, friend_name) VALUES (:current_user, :friend_name)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(':current_user' => $current_user, ':friend_name' => $friend_name));

            $sql = "INSERT INTO friend_list (username, friend_name) VALUES (:friend_name, :current_user)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(':current_user' => $current_user, ':friend_name' => $friend_name));

            // データが挿入されたらメッセージを表示
            echo "友だちが追加されました！";
        } else {
            echo "すでに友だちに追加されています。";
        }
    } else {
        // ユーザーが見つからない場合のエラーメッセージ
        echo "友だちの情報が見つかりません。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>友だち追加</title>
    <link rel="stylesheet" type="text/css" href="friend.css">
</head>

<body>
    <nav>
        <ul>
            <li><a href="../top-page.php">トップページ</a></li>
        </ul>
    </nav>
    <form action="" method="POST">
        <h1>友だち追加</h1>
        <p>友だちのユーザー名</p>
        <input name="friend_name" type="text">
        <p>友だちの個別認識ID</p>
        <input name="friend_ID" type="text">
        <p><button type="submit" name="add">追加</button></p>
    </form><hr>

    <h1>友達一覧&チャット</h1>
    <h2><?php
    // 自分のユーザ名
    $current_user = $_SESSION['user']['username'];

    // データベースから友達一覧を取得
    $query = "SELECT friend_name FROM friend_list WHERE username = :current_user";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(':current_user' => $current_user));
    $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($friends) {
        echo '<ul>';
        foreach ($friends as $friend) {
            echo '<li><a href="message\friend_message.php?recipient=' . htmlspecialchars($friend['friend_name']) . '">' . htmlspecialchars($friend['friend_name']) . '</a></li>';
        }
        echo '</ul>';
    } else {
        echo '友達はまだいません。';
    }
    ?></h2>
</body>

</html>
