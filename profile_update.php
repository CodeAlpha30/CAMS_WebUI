<?php
// include 'conn.php';
// session_start();

// $response = array();

// if (isset($_POST['UserName']) && isset($_POST['PhoneNo']) && 
// isset($_POST['Email']) && isset($_POST['ClassId']) && 
// isset($_POST['Gender_male']) 
// // && isset($_POST['OldPassword']) && isset($_POST['NewPassword'])
// ) {
//     $userName = $_POST['UserName'];
//     $phoneNo = $_POST['PhoneNo'];
//     $email = $_POST['Email'];
//     $classId = $_POST['ClassId'];
//     $genderMale = $_POST['Gender_male'];
//     $oldPassword = $_POST['OldPassword'];
//     $newPassword = isset($_POST['NewPassword']) ? $_POST['NewPassword'] : null;
//     $userId = $_SESSION['UserId'];

//     // 验证旧密码是否正确
//     $checkPasswordSql = "SELECT COUNT(*) FROM userinfo WHERE UserId = ? AND Password = ?";
//     $checkPasswordStmt = $conn->prepare($checkPasswordSql);
//     $checkPasswordStmt->bind_param("is", $userId, $oldPassword);
//     $checkPasswordStmt->execute();
//     $checkPasswordResult = $checkPasswordStmt->get_result();
//     $row = $checkPasswordResult->fetch_array();
//     if ($row[0] == 0) {
//         $response['status'] = 'error';
//         $response['message'] = '旧密码不正确。';
//         echo json_encode($response);
//         exit;
//     }

//     // 更新密码
//     $updatePasswordSql = "UPDATE userinfo SET Password = ? WHERE UserId = ?";
//     $updatePasswordStmt = $conn->prepare($updatePasswordSql);
//     $updatePasswordStmt->bind_param("ss", $newPassword, $userId);
//     if ($updatePasswordStmt->execute()) {
//         // 更新其他信息
//         $updateInfoSql = "UPDATE commonusers SET UserName = ?, ClassId = ?, Gender_male = ? WHERE UserId = ?";
//         $updateInfoStmt = $conn->prepare($updateInfoSql);
//         $updateInfoStmt->bind_param("ssis", $userName, $classId, $genderMale, $userId);
//         if ($updateInfoStmt->execute()) {
//             $response['status'] = 'success';
//             $response['message'] = '密码修改成功，请重新登录。';
//         } else {
//             $response['status'] = 'error';
//             $response['message'] = '个人信息更新失败。';
//         }
//     } else {
//         $response['status'] = 'error';
//         $response['message'] = '密码修改失败。';
//     }
// } else {
//     $response['status'] = 'error';
//     $response['message'] = '无效的请求。';
// }

// echo json_encode($response);
include 'conn.php';
session_start();

$response = array();

if (isset($_POST['UserName']) && isset($_POST['PhoneNo']) && isset($_POST['Email']) && isset($_POST['ClassId']) && isset($_POST['Gender_male'])) {
    $userName = $_POST['UserName'];
    $phoneNo = $_POST['PhoneNo'];
    $email = $_POST['Email'];
    $classId = $_POST['ClassId'];
    $genderMale = $_POST['Gender_male'];
    $oldPassword = $_POST['OldPassword'];
    $newPassword = isset($_POST['NewPassword']) ? $_POST['NewPassword'] : null;
    $userId = $_SESSION['UserId'];

    $flag = 0;

    // 更新其他信息
    $updateInfoSql = "UPDATE commonusers SET UserName = ?, ClassId = ?, Gender_male = ? WHERE UserId = ?";
    $updateInfoStmt = $conn->prepare($updateInfoSql);
    if ($updateInfoStmt === false) {
        // 处理 prepare 失败的情况
        die('Prepare failed: ' . $conn->error);
    }
    $updateInfoStmt->bind_param("siii", $userName, $classId, $genderMale, $userId);
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