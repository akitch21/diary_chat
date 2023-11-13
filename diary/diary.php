<?php
session_start();
if (!isset($_SESSION['user'])) {
    // ログインしていない場合、ログインページにリダイレクト
    header("Location: ../user_info/login.php");
    exit;
}

require_once "../db-connect.php";

$sql = "CREATE TABLE IF NOT EXISTS diary"
    . " ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "username VARCHAR(255),"
    . "title TEXT,"
    . "contents TEXT,"
    . "image_path VARCHAR(255),"
    . "posted_at DATETIME,"
    . "reaction INT,"
    . "public TEXT"
    . ");";
$stmt = $pdo->query($sql);

if (!empty($_POST['title']) && !empty($_POST['contents']) && !empty($_POST['date'])) {
    $title = $_POST['title'];
    $contents = $_POST['contents'];
    $date = $_POST['date'];
    $public = $_POST['public'];

    if (!empty($_FILES['image']['name'])) {
        $targetDirectory = 'uploads/';
        $targetFile = $targetDirectory . basename($_FILES['image']['name']);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile;
        } else {
            echo '画像のアップロードに失敗しました。';
            $imagePath = null;
        }
    } else {
        $imagePath = null;
    }

    $username = $_SESSION['user']['username'];

    // データベースにデータを挿入
    $query = "INSERT INTO diary (username, title, contents, image_path, posted_at, public) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$username, $title, $contents, $imagePath, $date, $public]);

    // 日記が投稿されたらメッセージを表示
    echo "日記が投稿されました！";
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>日記投稿</title>
    <link rel="stylesheet" type="text/css" href="diary.css">
</head>

<body>
    <nav>
        <ul>
            <li><a href="../top-page.php">トップページ</a></li>
            <li><a href="diary_view.php">日記一覧</a></li>
        </ul>
    </nav>

    <h1>日記</h1>

    <form action="" method="POST" enctype="multipart/form-data">
        <?php
        $currentDate = date("Y-m-d");
        ?>
        <input type="date" name="date" value="<?php echo $currentDate; ?>"></input>
        <p>タイトル</p>
        <input name="title" type="text">
        <p>内容</p>
        <p><textarea name="contents"></textarea></p>
        <p><input type="file" name="image" accept="image/*"></p>
        <p><select name="public">
            <option value="all_public">公開</option>
            <option value="not_public">非公開</option>
            <option value="only_myfriends">友達のみ</option>
        </select></p>
        <p><button type="submit" name="upload">投稿</button></p>
    </form>
</body>

</html>