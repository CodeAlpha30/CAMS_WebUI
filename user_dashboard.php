<?php
// user_dashboard.php
include 'conn.php'; // 包含数据库连接文件
session_start();

// 检查用户是否登录
if (!isset($_SESSION['UserId'])) {
    header("Location: login.php");
    exit;
}

// 获取用户信息
$userId = $_SESSION['UserId'];
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM userinfo WHERE UserId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo $user['UserName']; ?></h1>
    <nav>
        <ul>
            <li><a href="activities.php">活动列表</a></li>
            <li><a href="my_applications.php">已提交的申请</a></li>
            <li><a href="my_profile.php">本用户信息</a></li>
        </ul>
    </nav>
    <!-- 主内容区域 -->
    <main>
        <!-- 根据需要填充内容 -->
    </main>
</body>
</html>