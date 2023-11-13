<?php
// セッションの開始
session_start();
if (isset($_SESSION['user']) != "") {
    // ログイン済みの場合はリダイレクト
    header("Location: ../top-page.php");
    exit;
}

// ファイルの読み込み
require_once "../db-connect.php";

// テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS users"
    . " ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "username VARCHAR(255) UNIQUE,"  // ユーザー名を一意に設定
    . "email VARCHAR(255) UNIQUE,"     // メールアドレスを一意に設定
    . "password TEXT,"
    . "random_id CHAR(10),"
    . "created_at DATETIME"
    . ");";
$stmt = $pdo->query($sql);

if (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_check = $_POST['password_check']; // 確認用パスワードを追加

    if ($password === $password_check) { // パスワードと確認用パスワードの一致をチェック
        // ユーザー名とメールアドレスの重複チェック
        $query = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$username, $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // ユーザー名またはメールアドレスが既に存在する場合のエラーメッセージ
            echo "ユーザー名またはメールアドレスが既に使用されています。";
        } else {
            // ユーザー名とメールアドレスが重複しない場合の挿入処理
            $password = password_hash($password, PASSWORD_BCRYPT);
            $randomID = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 10);

            $insertQuery = "INSERT INTO users(username, email, password, random_id, created_at) VALUES(?, ?, ?, ?, NOW())";
            $insertStmt = $pdo->prepare($insertQuery);
            if ($insertStmt->execute([$username, $email, $password, $randomID])) {
                // 登録成功のメッセージ
                echo "登録しました";
            } else {
                // データベース挿入エラーのメッセージ
                echo "データベースエラーが発生しました。";
            }
        }
    } else {
        // パスワードが一致しない場合のエラーメッセージ
        echo 'パスワードが一致しません。';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" type="text/css" href="user_info.css">
</head>

<body>
    <form method="post">
        <h1>会員登録フォーム</h1>
        <div class="form-group">
            <input type="text" class="form-control" name="username" placeholder="ユーザー名" required />
        </div>
        <div class="form-group">
            <input type="email" class "form-control" name="email" placeholder="メールアドレス" required />
        </div>
        <div class="form-group">
            <input type="password" class="form-control" name="password" placeholder="パスワード" required />
        </div>
        <div class="form-group">
            <input type="password" class="form-control" name="password_check" placeholder="もう一度同じパスワードを入力してください" required />
        </div>
        <p><button type="submit" class="btn btn-default" name="signup">会員登録する</button></p>
        <p><a href="login.php">ログインはこちら</a></p>
    </form>
</body>

</html>