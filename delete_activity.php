<?php
include 'conn.php';
session_start();

$response = array();

if (isset($_GET['ActvtId'])) {
    $actvtId = $_GET['ActvtId'];
    $userId = $_SESSION['UserId'];

    // 删除participation表中所有ActvtId等于当前ActvtId的行
    $deleteParticipationSql = "DELETE FROM participation WHERE ActvtId = ?";
    $stmt = $conn->prepare($deleteParticipationSql);
    $stmt->bind_param("i", $actvtId);
    if ($stmt->execute()) {
        // 删除activities表中当前ActvtId的行
        $deleteActivitySql = "DELETE FROM activities WHERE ActvtId = ?";
        $stmt->close();
        $stmt = $conn->prepare($deleteActivitySql);
        $stmt->bind_param("i", $actvtId);
        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = '活动删除成功。';
        } else {
            $response['status'] = 'error';
            $response['message'] = '活动删除失败。';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = '删除申请失败。';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = '无效的请求。';
}

echo json_encode($response);
header("Location: activities_manage.php"); // 重定向到活动管理页面
exit;
?>