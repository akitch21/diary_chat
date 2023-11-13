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

$public = "all_public"; // デフォルトの公開状態
if (isset($_POST['change'])) {
  $public = $_POST['public']; // ユーザーが公開状態を変更した場合
}

// データベースから日記データとユーザー名を取得
$query = "SELECT diary.*, users.username AS author FROM diary JOIN users ON diary.username = users.username WHERE diary.public = :public";

// 非公開の場合、ユーザー名が一致するかどうかを確認
if ($public === 'not_public') {
  $query .= " AND diary.username = :current_user";
}

// 友達のみの場合、友達の日記のみを表示
if ($public === 'only_myfriends') {
  $query .= " AND diary.username IN (SELECT friend_name FROM friend_list WHERE username = :current_user)";
}

$stmt = $pdo->prepare($query);

if ($public === 'not_public' || $public === 'only_myfriends') {
  // ユーザー名をバインドして自身の非公開の日記または友達の日記のみを表示
  $stmt->bindParam(':current_user', $_SESSION['user']['username'], PDO::PARAM_STR);
}

$stmt->bindParam(':public', $public, PDO::PARAM_STR);
$stmt->execute();

$diaryData = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>日記閲覧</title>
  <link rel="stylesheet" type="text/css" href="diary.css">
</head>

<body>
  <nav>
    <ul>
      <li><a href="diary.php">日記投稿</a></li>
      <li><a href="../top-page.php">トップページ</a></li>
    </ul>
  </nav>

  <h1>日記閲覧</h1>

  <form action="" method="POST">
    <select name="public">
      <option value="all_public" <?php echo ($public == 'all_public') ? 'selected' : ''; ?>>公開</option>
      <option value="not_public" <?php echo ($public == 'not_public') ? 'selected' : ''; ?>>非公開</option>
      <option value="only_myfriends" <?php echo ($public == 'only_myfriends') ? 'selected' : ''; ?>>友達のみ</option>
    </select>
    <button type="submit" name="change">変更</button>
  </form>

  <h2>日記一覧</h2>

  <?php if (count($diaryData) > 0) { ?>
    <ul>
      <?php foreach ($diaryData as $diary) { ?>
        <li>
          <hr><h3><?php echo $diary['title']; ?></h3>
          <p>投稿者: <?php echo $diary['author']; ?></p>
          <p><?php echo $diary['contents']; ?></p>
          <p>投稿日: <?php echo $diary['posted_at']; ?></p>
          <!-- 画像を表示する場合 -->
          <?php if (!empty($diary['image_path'])) { ?>
            <img src="<?php echo $diary['image_path']; ?>" alt="日記画像" width="" height="300">
          <?php } ?>
        </li>
      <?php } ?>
    </ul>
  <?php } else { ?>
    <p>該当する日記はありません。</p>
  <?php } ?>
</body>

</html>
