<?php
include 'conn.php'; // 确保包含数据库连接
session_start();

if (isset($_GET['ActvtId']) && isset($_GET['UserId'])) {
    $actvtId = $_GET['ActvtId'];
    $userId = $_GET['UserId'];

    // 更新参与状态为拒绝
    $sql = "UPDATE participation SET Status = 2 WHERE ActvtId = ? AND UserId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $actvtId, $userId);
    if ($stmt->execute()) {
        echo "Application rejected successfully.";
    } else {
        echo "Error rejecting application: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Invalid request.";
}

header("Location: application_view.php?ActvtId=" . $actvtId); // 重定向到活动管理页面
exit;
?>