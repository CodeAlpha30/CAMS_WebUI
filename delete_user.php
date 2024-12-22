<?php
include 'conn.php';
session_start();

$response = array();

if (isset($_GET['UserId'])) {
    $userId = $_GET['UserId'];

    // 检查是否是当前登录用户
    if ($userId == $_SESSION['UserId']) {
        $response['status'] = 'error';
        $response['message'] = '不能删除自己。';
        echo json_encode($response);
        exit;
    }

    // 删除participation表中所有UserId等于当前UserId的行
    $deleteParticipationSql = "DELETE FROM participation WHERE UserId = ?";
    $stmtDeleteParticipation = $conn->prepare($deleteParticipationSql);
    $stmtDeleteParticipation->bind_param("i", $userId);
    if (!$stmtDeleteParticipation->execute()) {
        $response['status'] = 'error';
        $response['message'] = '删除申请失败。';
        echo json_encode($response);
        exit;
    }

    // 删除adminusers表或commonusers表对应的行
    $deleteAdminSql = "DELETE FROM adminusers WHERE UserId = ?";
    $deleteCommonSql = "DELETE FROM commonusers WHERE UserId = ?";
    $deleteUserSql = "DELETE FROM userinfo WHERE UserId = ?";

    $stmtAdmin = $conn->prepare($deleteAdminSql);
    $stmtAdmin->bind_param("i", $userId);
    $stmtAdmin->execute();

    $stmtCommon = $conn->prepare($deleteCommonSql);
    $stmtCommon->bind_param("i", $userId);
    $stmtCommon->execute();

    $stmtUser = $conn->prepare($deleteUserSql);
    $stmtUser->bind_param("i", $userId);
    if ($stmtUser->execute()) {
        $response['status'] = 'success';
        $response['message'] = '用户删除成功。';
    } else {
        $response['status'] = 'error';
        $response['message'] = '用户删除失败。';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = '无效的请求。';
}

echo json_encode($response);
header("Location: user_management.php"); // 重定向到用户管理页面
exit;
?>