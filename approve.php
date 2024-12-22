<?php
include 'conn.php'; // 确保包含数据库连接
session_start();

// echo "".isset($_GET['ActvtId']);
// echo "".isset($_GET['UserId']);

if (isset($_GET['ActvtId']) && isset($_GET['UserId'])) {
    $actvtId = $_GET['ActvtId'];
    $userId = $_GET['UserId'];

    // 更新参与状态为批准
    $sql = "UPDATE participation SET Status = 1 WHERE ActvtId = ? AND UserId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $actvtId, $userId);
    if ($stmt->execute()) {
        echo "Application approved successfully.";
    } else {
        echo "Error approving application: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Invalid request.";
}

header("Location: application_view.php?ActvtId=" . $actvtId); // 重定向到活动管理页面
exit;
?>