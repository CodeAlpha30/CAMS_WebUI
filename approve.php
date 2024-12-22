<?php
include 'conn.php'; // 确保包含数据库连接

if (isset($_POST['ActvtId']) && isset($_POST['UserId'])) {
    $actvtId = $_POST['ActvtId'];
    $userId = $_POST['UserId'];

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
?>