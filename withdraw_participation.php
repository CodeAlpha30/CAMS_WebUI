<?php
include 'conn.php';
session_start();

$response = array();

if (isset($_POST['actvtId']) && isset($_POST['userId'])) {
    $actvtId = $_POST['actvtId'];
    $userId = $_POST['userId'];

    // 更新申请记录为撤回状态
    $updateSql = "UPDATE participation SET Status = 11 WHERE ActvtId = ? AND UserId = ? AND Status = 0";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ii", $actvtId, $userId);
    if ($updateStmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = '申请撤回成功。';
    } else {
        $response['status'] = 'error';
        $response['message'] = '申请撤回失败。';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = '无效的请求。';
}

echo json_encode($response);
?>