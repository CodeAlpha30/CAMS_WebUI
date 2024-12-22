<?php
include 'conn.php';
session_start();

$response = array();

echo "".isset($_POST['ActvtId']);
echo "".isset($_POST['ActvtTitle']);
echo "".isset($_POST['ActvtTime']);
echo "".isset($_POST['PlaceId']);
echo "".isset($_POST['PeopleNumRqrd']);
echo "".isset($_POST['PeopleNumIn']);
// sleep(1);

// 检查是否设置了所有必要的字段
if (isset($_POST['ActvtTitle']) &&
    isset($_POST['ActvtTime']) &&
    isset($_POST['PlaceId']) &&
    isset($_POST['PeopleNumRqrd']) &&
    isset($_POST['PeopleNumIn'])) {

    // $actvtId = $_POST['ActvtId'];
    $actvtTitle = $_POST['ActvtTitle'];
    $deptId = $_POST['DeptId'] ?? null; // 非必填项，使用null合并运算符处理
    $actvtTime = $_POST['ActvtTime'];
    $placeId = $_POST['PlaceId'];
    $peopleNumRqrd = $_POST['PeopleNumRqrd'];
    $peopleNumIn = $_POST['PeopleNumIn'];
    $intro = $_POST['Intro'] ?? null; // 非必填项
    $otherRqrments = $_POST['OtherRqrments'] ?? null; // 非必填项
    $notes = $_POST['Notes'] ?? null; // 非必填项
    $administrationAuth = $_POST['AdministrationAuth'] ?? null; // 非必填项

    $userId = $_SESSION['UserId']; // 当前用户ID

    // 检查人数是否合理
    if ($peopleNumIn > $peopleNumRqrd) {
        $response['status'] = 'error';
        $response['message'] = '已报名人数不能超过所需人数。';
        echo json_encode($response);
        exit;
    }

    if (isset($_POST['ActvtId'])) {
        $newActvtId = $_POST['ActvtId'];

        // 更新activities表中的活动信息
        $sqlUpdateActivity = "UPDATE activities SET 
            ActvtId = ?, 
            ActvtTitle = ?, 
            DeptId = ?, 
            ActvtTime = ?, 
            PlaceId = ?, 
            PeopleNumRqrd = ?, 
            PeopleNumIn = ?, 
            Intro = ?, 
            OtherRqrments = ?, 
            Notes = ?, 
            Status = 0, 
            AdministrationAuth = ?, 
            PublisherId = ?
            WHERE ActvtId = ?";
        $stmtUpdateActivity = $conn->prepare($sqlUpdateActivity);
        $nulls = array_fill(0, 5, 'NULL'); // 用于非必填项的占位
        $deptId = $deptId ? $deptId : $nulls[0];
        $intro = $intro ? $intro : $nulls[1];
        $otherRqrments = $otherRqrments ? $otherRqrments : $nulls[2];
        $notes = $notes ? $notes : $nulls[3];
        $administrationAuth = $administrationAuth ? $administrationAuth : $nulls[4];
        $stmtUpdateActivity->bind_param("isisiiisssiii", 
                                        $newActvtId, 
                                        $actvtTitle, 
                                        // $deptId ? $deptId : $nulls[0], 
                                        $deptId, 
                                        $actvtTime, 
                                        $placeId, 
                                        $peopleNumRqrd, 
                                        $peopleNumIn, 
                                        // $intro ? $intro : $nulls[1], 
                                        // $otherRqrments ? $otherRqrments : $nulls[2], 
                                        // $notes ? $notes : $nulls[3], 
                                        $intro, 
                                        $otherRqrments, 
                                        $notes, 
                                        // $administrationAuth ? $administrationAuth : $nulls[0], 
                                        $administrationAuth, 
                                        $userId, 
                                        $actvtId);
        if ($stmtUpdateActivity->execute()) {
        $response['status'] = 'success';
        $response['message'] = '活动更新成功。';
        } else {
        $response['status'] = 'error';
        $response['message'] = '活动更新失败。';
        }
    }
    else 
    {
        // 获取activities表中最大的ActvtId
        $maxActvtIdSql = "SELECT MAX(ActvtId) as maxActvtId FROM activities";
        $maxActvtIdStmt = $conn->prepare($maxActvtIdSql);
        $maxActvtIdStmt->execute();
        $maxActvtIdResult = $maxActvtIdStmt->get_result();
        $maxActvtIdRow = $maxActvtIdResult->fetch_assoc();
        $newActvtId = $maxActvtIdRow['maxActvtId'] + 1;

        // 更新activities表中的活动信息
        $sqlUpdateActivity = "INSERT INTO activities VALUES ( 
            ?, 
            ?, 
            ?, 
            ?, 
            ?, 
            ?, 
            ?, 
            ?, 
            ?, 
            ?, 
            0, 
            NULL, 
            ?, 
            ?
            )";
        $stmtUpdateActivity = $conn->prepare($sqlUpdateActivity);
        $nulls = array_fill(0, 5, 'NULL'); // 用于非必填项的占位
        $deptId = $deptId ? $deptId : $nulls[0];
        $intro = $intro ? $intro : $nulls[1];
        $otherRqrments = $otherRqrments ? $otherRqrments : $nulls[2];
        $notes = $notes ? $notes : $nulls[3];
        $administrationAuth = $administrationAuth ? $administrationAuth : $nulls[4];
        $stmtUpdateActivity->bind_param("isisiiisssii", 
                    $newActvtId, 
                    $actvtTitle, 
                    // $deptId ? $deptId : $nulls[0], 
                    $deptId, 
                    $actvtTime, 
                    $placeId, 
                    $peopleNumRqrd, 
                    $peopleNumIn, 
                    // $intro ? $intro : $nulls[1], 
                    // $otherRqrments ? $otherRqrments : $nulls[2], 
                    // $notes ? $notes : $nulls[3], 
                    $intro, 
                    $otherRqrments, 
                    $notes, 
                    // $administrationAuth ? $administrationAuth : $nulls[0], 
                    $administrationAuth, 
                    $userId);
        if ($stmtUpdateActivity->execute()) {
        $response['status'] = 'success';
        $response['message'] = '活动更新成功。';
        } else {
        $response['status'] = 'error';
        $response['message'] = '活动更新失败。';
        }
    }

    
} else {
    $response['status'] = 'error';
    $response['message'] = '缺少必要的字段。';
}

echo json_encode($response);
header("Location: activities_manage.php"); // 重定向到活动管理页面
exit;
?>