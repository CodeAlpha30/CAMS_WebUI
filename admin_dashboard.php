<?php
// admin_dashboard.php
include 'conn.php'; // 包含数据库连接文件
session_start();

// 检查用户是否登录并且是否为管理员
if (!isset($_SESSION['UserId']) || $_SESSION['isAdmin'] != 1) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['UserId'];
// $conn = new mysqli($servername, $username, $password, $dbname);
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

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
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($user['UserId']); ?></h1>
    <nav>
        <ul>
            <li><a href="activities_manage.php">活动列表</a></li>
            <li><a href="pending_approvals.php">待审批的申请</a></li>
            <li><a href="user_management.php">用户管理</a></li>
            <li><a href="my_admin_profile.php">本用户信息</a></li>
            <li><a href="logout.php">退出登录</a></li>
        </ul>
    </nav>
    <!-- 主内容区域 -->
    <main>
        <!-- 根据需要填充内容 -->
    </main>
</body>
</html>