<?php
include 'conn.php';
session_start();

$response = array();
$actvtId = $_POST['ActvtId'];
$userId = $_POST['UserId'];
$action = $_POST['action'];

// 检查是否为管理员
if ($_SESSION['isAdmin'] != 1) {
    $response['status'] = 'error';
    $response['message'] = '您没有权限执行此操作。';
    echo json_encode($response);
    exit;
}

// 检查活动是否已满
$sqlCheckPeopleNum = "SELECT PeopleNumIn, PeopleNumRqrd FROM activities WHERE ActvtId = ?";
$stmtCheckPeopleNum = $conn->prepare($sqlCheckPeopleNum);
$stmtCheckPeopleNum->bind_param("i", $actvtId);
$stmtCheckPeopleNum->execute();
$resultCheckPeopleNum = $stmtCheckPeopleNum->get_result();
$rowCheckPeopleNum = $resultCheckPeopleNum->fetch_assoc();

if ($rowCheckPeopleNum['PeopleNumIn'] >= $rowCheckPeopleNum['PeopleNumRqrd'] && $action == 'approve') {
    $response['status'] = 'error';
    $response['message'] = '活动人数已满，无法审核通过。';
    echo json_encode($response);
    exit;
}

// 更新participation表中的状态
if ($action == 'approve') {
    $newStatus = 1; // 审核通过
} elseif ($action == 'reject') {
    $newStatus = 2; // 审核未通过
} else {
    $response['status'] = 'error';
    $response['message'] = '无效的操作。';
    echo json_encode($response);
    exit;
}

$sqlUpdateStatus = "UPDATE participation SET Status = ? WHERE ActvtId = ? AND UserId = ?";
$stmtUpdateStatus = $conn->prepare($sqlUpdateStatus);
$stmtUpdateStatus->bind_param("iii", $newStatus, $actvtId, $userId);
if ($stmtUpdateStatus->execute()) {
    $response['status'] = 'success';
    $response['message'] = $action == 'approve' ? '审核通过成功。' : '拒绝申请成功。';
} else {
    $response['status'] = 'error';
    $response['message'] = '操作失败。';
}

echo json_encode($response);
?>