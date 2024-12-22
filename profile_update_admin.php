<?php
include 'conn.php';
session_start();

$response = array();

$flag = 0;

// echo "".isset($_POST['AdminName']);
// echo "".isset($_POST['PhoneNo']);
// echo "".isset($_POST['Email']);
// echo "".isset($_POST['DeptId']);
// echo "".isset($_POST['numActivityPublished']);

if (isset($_POST['AdminName']) && isset($_POST['PhoneNo']) && 
isset($_POST['Email']) && isset($_POST['DeptId'])) {
    $userName = $_POST['AdminName'];
    $phoneNo = $_POST['PhoneNo'];
    $email = $_POST['Email'];
    $deptId = $_POST['DeptId'];
    // $numActivityPublished = $_POST['numActivityPublished'];
    $oldPassword = $_POST['OldPassword'];
    $newPassword = isset($_POST['NewPassword']) ? $_POST['NewPassword'] : null;
    $userId = $_SESSION['UserId'];

    // 更新其他信息
    $updateInfoSql = "UPDATE adminusers SET AdminName = ?, DeptId = ? WHERE UserId = ?";
    $updateInfoStmt = $conn->prepare($updateInfoSql);
    if ($updateInfoStmt === false) {
        // 处理 prepare 失败的情况
        die('Prepare failed: ' . $conn->error);
    }
    $updateInfoStmt->bind_param("sii", $userName, $deptId, $userId);
    // $exc1 = $updateInfoStmt->execute();
    if (!($exc1 = $updateInfoStmt->execute())) {
        // 处理 execute 失败的情况
        die('Execute failed: ' . $updateInfoStmt->error);
    }
    $updateuInfoSql = "UPDATE userinfo SET PhoneNo = ?, Email = ? WHERE UserId = ?";
    $updateuInfoStmt = $conn->prepare($updateuInfoSql);
    if ($updateuInfoStmt === false) {
        // 处理 prepare 失败的情况
        die('Prepare failed: ' . $conn->error);
    }
    $updateuInfoStmt->bind_param("ssi", $phoneNo, $email, $userId);
    // $exc2 = $updateuInfoStmt->execute();
    if (!($exc2 = $updateuInfoStmt->execute())) {
        // 处理 execute 失败的情况
        die('Execute failed: ' . $updateuInfoStmt->error);
    }
    
    if ($exc1 && $exc2) {
        if ($newPassword) {
            // 验证旧密码是否正确
            $checkPasswordSql = "SELECT COUNT(*) FROM userinfo WHERE UserId = ? AND Password = ?";
            $checkPasswordStmt = $conn->prepare($checkPasswordSql);
            $checkPasswordStmt->bind_param("is", $userId, $oldPassword);
            $checkPasswordStmt->execute();
            $checkPasswordResult = $checkPasswordStmt->get_result();
            $row = $checkPasswordResult->fetch_array();
            if ($row[0] == 0) {
                $response['status'] = 'error';
                $response['message'] = '旧密码不正确。';
                echo json_encode($response);
                exit;
            }

            // 更新密码
            $updatePasswordSql = "UPDATE userinfo SET Password = ? WHERE UserId = ?";
            $updatePasswordStmt = $conn->prepare($updatePasswordSql);
            $updatePasswordStmt->bind_param("si", $newPassword, $userId);
            if ($updatePasswordStmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = '密码修改成功，请重新登录。';
                // 销毁session，登出用户
                $_SESSION = array();
                if (ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000,
                            $params["path"], $params["domain"],
                            $params["secure"], $params["httponly"]
                    );
                }
                session_destroy();
                $flag = 1;
            } else {
                $response['status'] = 'error';
                $response['message'] = '密码修改失败。';
            }
        } else {
            $response['status'] = 'success';
            $response['message'] = '个人信息更新成功。';
            echo json_encode($response);
            // echo "<script>alert('个人信息更新成功');location.href='admin_dashboard.php?table=$table';</script>";
            // echo "<script>alert('个人信息更新成功');</script>";
            // sleep(1);
            header("Location: admin_dashboard.php");
            exit;
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = '个人信息更新失败。';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = '无效的请求。';
}

echo json_encode($response);
if (1 == $flag) {
    header("Location: login.php"); // 重定向到登录页面
    exit;
}
?>