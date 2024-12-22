<?php
include 'conn.php';
session_start();

$response = array();

if (isset($_GET['UserId'])) {
    $userId = $_GET['UserId'];

    // 检查是否是当前登录用户
    if ($userId == $_SESSION['UserId']) {
        $response['status'] = 'error';
        $response['message'] = '不能设置自己为管理员。';
        echo json_encode($response);
        exit;
    }

    // 设置为管理员
    $updateUserSql = "UPDATE userinfo SET isAdmin = 1 WHERE UserId = ?";
    $adminName = 'DefaultName'; // 默认管理员名称
    $defaultDeptId = 10; // 默认部门ID
    $insertAdminSql = "INSERT INTO adminusers (UserId, AdminName, DeptId) VALUES (?, ?, ?)";

    $stmtUser = $conn->prepare($updateUserSql);
    $stmtUser->bind_param("i", $userId);
    $stmtUser->execute();

    $stmtAdmin = $conn->prepare($insertAdminSql);
    $stmtAdmin->bind_param("isi", $userId, $adminName, $defaultDeptId);
    if ($stmtAdmin->execute()) {
        $response['status'] = 'success';
        $response['message'] = '用户设置为管理员成功。';
    } else {
        $response['status'] = 'error';
        $response['message'] = '用户设置为管理员失败。';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = '无效的请求。';
}

echo json_encode($response);
header("Location: user_management.php"); // 重定向到用户管理页面
exit;
?>