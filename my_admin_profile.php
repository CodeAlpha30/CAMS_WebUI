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
$stmtUserinfo->bind_param("i", $userId);
$stmtUserinfo->execute();
$resultUserinfo = $stmtUserinfo->get_result();

$sqlAdminusers = "SELECT * FROM adminusers WHERE UserId = ?";
$stmtAdminusers = $conn->prepare($sqlAdminusers);
$stmtAdminusers->bind_param("i", $userId);
$stmtAdminusers->execute();
$resultAdminusers = $stmtAdminusers->get_result();

$userRow = $resultUserinfo->fetch_assoc();
$adminUserRow = $resultAdminusers->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>本用户信息</title>
    <style>
        /* Add CSS styles */
    </style>
</head>
<body>
    <h1>本用户信息</h1>
    <form action="profile_update_admin.php" method="post">
        <label for="AdminName">用户名:</label>
        <input type="text" id="AdminName" name="AdminName" value="<?php echo htmlspecialchars($adminUserRow['AdminName']); ?>" required><br>
        
        <label for="PhoneNo">电话号码:</label>
        <input type="text" id="PhoneNo" name="PhoneNo" value="<?php echo htmlspecialchars($userRow['PhoneNo']); ?>" required><br>
        
        <label for="Email">邮箱:</label>
        <input type="email" id="Email" name="Email" value="<?php echo htmlspecialchars($userRow['Email']); ?>" required><br>
        
        <label for="DeptId">部门ID:</label>
        <input type="text" id="DeptId" name="DeptId" value="<?php echo htmlspecialchars($adminUserRow['DeptId']); ?>" required><br>
        
        <label for="numActivityPublished">发布活动数:</label>
        <input type="text" id="numActivityPublished" name="numActivityPublished" value="<?php echo htmlspecialchars($adminUserRow['NumActivityPublished']); ?>" disabled><br>
        
        <label for="OldPassword">旧密码:</label>
        <input type="password" id="OldPassword" name="OldPassword"><br>
        
        <label for="NewPassword">新密码:</label>
        <input type="password" id="NewPassword" name="NewPassword"><br>
        
        <button type="submit">提交</button>
    </form>
</body>
</html>