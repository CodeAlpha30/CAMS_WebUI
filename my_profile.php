<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['UserId'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['UserId'];

$sqlUserinfo = "SELECT * FROM userinfo WHERE UserId = ?";
$stmtUserinfo = $conn->prepare($sqlUserinfo);
if ($stmtUserinfo === false) {
    // 处理 prepare 失败的情况
    die('Prepare failed: ' . $conn->error);
}
$stmtUserinfo->bind_param("i", $userId);
// echo "".$stmtUserinfo;
// $stmtUserinfo->execute();
if (!$stmtUserinfo->execute()) {
    // 处理 execute 失败的情况
    die('Execute failed: ' . $stmtUserinfo->error);
}
$resultUserinfo = $stmtUserinfo->get_result();
$userRow = $resultUserinfo->fetch_assoc();

$sqlCommonusers = "SELECT * FROM commonusers WHERE UserId = ?";
$stmtCommonusers = $conn->prepare($sqlCommonusers);
if ($stmtCommonusers === false) {
    // 处理 prepare 失败的情况
    die('Prepare failed: ' . $conn->error);
}
$stmtCommonusers->bind_param("i", $userId);
// echo "".$stmtCommonusers;
// $stmtCommonusers->execute();
if (!$stmtCommonusers->execute()) {
    // 处理 execute 失败的情况
    die('Execute failed: ' . $stmtCommonusers->error);
}
$resultCommonusers = $stmtCommonusers->get_result();
$commonUserRow = $resultCommonusers->fetch_assoc();

// 检查表单是否已提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userName = $_POST['UserName'];
    $phoneNo = $_POST['PhoneNo'];
    $email = $_POST['Email'];
    $classId = $_POST['ClassId'];
    $genderMale = $_POST['Gender_male'];
    $oldPassword = $_POST['OldPassword'];
    $newPassword = isset($_POST['NewPassword']) ? $_POST['NewPassword'] : null;

    // 邮箱格式验证
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("邮箱格式不正确。");
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>本用户信息</title>
        <style> form { 
            margin: 10px; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            } 
            label, input, button { 
                display: block; 
                margin: 5px 0; 
            } 
        </style>
    </head>
    <body>
        <h1>本用户信息</h1>
        <form action="profile_update.php" method="post">
            <label for="UserName">用户名:</label>
            <input type="text" id="UserName" name="UserName" value="<?php echo htmlspecialchars($commonUserRow['UserName']); ?>" required><br>
            <label for="PhoneNo">电话号码:</label>
            <input type="text" id="PhoneNo" name="PhoneNo" value="<?php echo htmlspecialchars($userRow['PhoneNo']); ?>" required><br>
            <label for="Email">邮箱:</label>
            <input type="email" id="Email" name="Email" value="<?php echo htmlspecialchars($userRow['Email']); ?>" required><br>
            <label for="ClassId">班级ID:</label>
            <input type="text" id="ClassId" name="ClassId" value="<?php echo htmlspecialchars($commonUserRow['ClassId']); ?>" required><br>
            <label for="Gender_male">性别:</label>
            <input type="radio" id="Gender_male_1" name="Gender_male" value="1" <?php echo $commonUserRow['Gender_male'] == 1 ? 'checked' : ''; ?> required> Male 
            <input type="radio" id="Gender_male_0" name="Gender_male" value="0" <?php echo $commonUserRow['Gender_male'] == 0 ? 'checked' : ''; ?>> Female<br>
            <label for="ActivityParticipation">活动参与次数:</label> 
            <input type="text" id="ActivityParticipation" name="ActivityParticipation" value="<?php echo htmlspecialchars($commonUserRow['ActivityParticipation']); ?>" disabled><br> 
            <label for="Misbehavior">不良行为记录:</label> 
            <input type="text" id="Misbehavior" name="Misbehavior" value="<?php echo htmlspecialchars($commonUserRow['Misbehavior']); ?>" disabled><br> 
            <label for="OldPassword">旧密码:</label> 
            <input type="password" id="OldPassword" name="OldPassword"><br> 
            <label for="NewPassword">新密码:</label> 
            <input type="password" id="NewPassword" name="NewPassword"><br> 
            <button type="submit">提交</button> 
        </form> 
    </body> 
</html>