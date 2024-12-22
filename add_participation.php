<?php
include 'conn.php';
session_start();

$response = array();

if (isset($_POST['actvtId']) && isset($_POST['userId'])) {
    $actvtId = $_POST['actvtId'];
    $userId = $_POST['userId'];
    $currentDate = date("Y-m-d H:i:s");

    // 检查是否已经有申请记录
    $checkSql = "SELECT COUNT(*) FROM participation WHERE ActvtId = ? AND UserId = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("ii", $actvtId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_array();
    if ($row[0] > 0) {
        $response['status'] = 'error';
        $response['message'] = '您已经申请了这个活动。';
        echo json_encode($response);
        exit;
    }

    // 检查活动是否还有名额
    $activitiesSql = "SELECT PeopleNumIn, PeopleNumRqrd FROM activities WHERE ActvtId = ?";
    $activitiesStmt = $conn->prepare($activitiesSql);
    $activitiesStmt->bind_param("i", $actvtId);
    $activitiesStmt->execute();
    $activitiesResult = $activitiesStmt->get_result();
    $activitiesRow = $activitiesResult->fetch_assoc();

    if ($activitiesRow['PeopleNumIn'] >= $activitiesRow['PeopleNumRqrd']) {
        $response['status'] = 'error';
        $response['message'] = '活动名额已满。';
        echo json_encode($response);
        exit;
    }

    // 插入申请记录
    $insertSql = "INSERT INTO participation (ActvtId, UserId, Status) VALUES (?, ?, 0)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("ii", $actvtId, $userId);
    if ($insertStmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = '申请添加成功。';
    } else {
        $response['status'] = 'error';
        $response['message'] = '申请添加失败。';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = '无效的请求。';
}

echo json_encode($response);
?>