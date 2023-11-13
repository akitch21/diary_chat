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

$sql = "CREATE TABLE IF NOT EXISTS group_list"
    . " ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "group_name VARCHAR(255),"
    . "group_member TEXT,"
    . "group_password TEXT,"
    . "created_at DATETIME"
    . ");";
$stmt = $pdo->query($sql);

if (isset($_POST['create'])) {
    $group_name = $_POST['group_name'];
    $group_password = $_POST['group_password'];
    $current_user = $_SESSION['user']['username'];

    if (!empty($group_name) && !empty($group_password)) {
        // 同じグループ名が既に存在するか確認
        $checkQuery = "SELECT * FROM group_list WHERE group_name = :group_name";
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->execute(array(':group_name' => $group_name));

        if ($checkStmt->fetch(PDO::FETCH_ASSOC)) {
            echo '同じグループ名が既に存在します。';
        } else {
            // データベースにデータを挿入
            $query = "INSERT INTO group_list (group_name, group_member, group_password, created_at) VALUES (:group_name, :group_member, :group_password, NOW())";
            $stmt = $pdo->prepare($query);
            $stmt->execute(array(':group_name' => $group_name, ':group_member' => $current_user, ':group_password' => $group_password));

            // データが挿入されたらメッセージを表示
            echo "グループが作成されました！";
        }
    } else {
        echo 'グループ名とパスワードを両方入力してください。';
    }
}

if (isset($_POST['group_add'])) {
    $group_add_name = $_POST['group_add_name'];
    $group_add_password = $_POST['group_add_password'];
    $current_user = $_SESSION['user']['username'];

    // 入力されたグループ名とパスワードを持つグループを検索
    $query = "SELECT * FROM group_list WHERE group_name = :group_name AND group_password = :group_password";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(':group_name' => $group_add_name, ':group_password' => $group_add_password));
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // グループが見つかった場合、group_member から既存のメンバーリストを取得
        $group_member = $result['group_member'];
        $member_list = explode(',', $group_member);

        // ユーザー名が既にメンバーリストに含まれているかチェック
        if (!in_array($current_user, $member_list)) {
            // ユーザー名が含まれていない場合、メンバーリストに追加
            $member_list[] = $current_user;

            // 更新されたメンバーリストをカンマで連結
            $updated_group_member = implode(',', $member_list);

            // グループメンバーを更新
            $updateQuery = "UPDATE group_list SET group_member = :group_member WHERE id = :group_id";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute(array(':group_member' => $updated_group_member, ':group_id' => $result['id']));

            echo "グループに追加されました！";
        } else {
            echo "既にメンバーとして登録済みです。";
        }
    } else {
        echo "グループが見つかりません。";
    }
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>グループ作成</title>
    <link rel="stylesheet" type="text/css" href="friend.css">
</head>

<body>
    <nav>
        <ul>
            <li><a href="../top-page.php">トップページ</a></li>
        </ul>
    </nav>
    <form action="" method="POST">
        <h1>グループ作成</h1>
        <p>グループ名</p>
        <input name="group_name" type="text">
        <p>グループパスワード</p>
        <input name="group_password" type="text">
        <p><button type="submit" name="create">作成</button></p>
    </form>

    <form action="" method="POST">
        <h1>グループ追加</h1>
        <p>グループ名</p>
        <input name="group_add_name" type="text">
        <p>グループパスワード</p>
        <input name="group_add_password" type="text">
        <p><button type="submit" name="group_add">グループ追加</button></p>
    </form>

    <h1>グループ一覧&チャット</h1>
    <h2><?php
    // 自分のユーザ名
    $current_user = $_SESSION['user']['username'];

    // データベースから自分のユーザ名が含まれるgroup_memberを取得
    $query = "SELECT DISTINCT group_name FROM group_list WHERE FIND_IN_SET(:current_user, group_member)";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(':current_user' => $current_user));
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($groups)) {
        echo '<ul>';
        foreach ($groups as $group) {
            echo '<li><a href="message\group_message.php?group_name=' . urlencode($group['group_name']) . '">' . htmlspecialchars($group['group_name']) . '</a></li>';
        }
        echo '</ul>';
    } else {
        echo '参加中のグループはありません。';
    }

    ?></h2>
</body>

</html>
