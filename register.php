<?php
// register.php
include 'conn.php'; // 包含数据库连接文件

session_start(); // 开始session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 从表单获取用户信息
    $userId = $_POST['UserId'];
    // echo "$userId\n";
    $password = $_POST['Password'];
    // echo "$password\n";
    $phoneNo = $_POST['PhoneNo'];
    // echo "$phoneNo\n";
    $email = $_POST['Email'];
    // echo "$email\n";
    $userName = $_POST['UserName'];
    // echo "$userName\n";
    $classId = $_POST['ClassId'];
    // echo "$classId\n";
    $genderMale = $_POST['Gender_male'];
    // echo "$genderMale\n";
    // $isAdmin = isset($_POST['isAdmin']) ? 1 : 0; // 检查是否设置了isAdmin
    $isAdmin = 0;

    // 连接数据库
    // $conn = new mysqli($servername, $username, $password, $dbname);
    // if ($conn->connect_error) {
    //     die("Connection failed: " . $conn->connect_error);
    // }

    // 插入userinfo表
    $sqlUserInfo = "INSERT INTO userinfo (UserId, Password, PhoneNo, Email, isAdmin) VALUES (?, ?, ?, ?, ?)";
    // echo "$sqlUserInfo";
    $stmtUserInfo = $conn->prepare($sqlUserInfo);
    $stmtUserInfo->bind_param("isssi", $userId, $password, $phoneNo, $email, $isAdmin);
    // echo "$sqlUserInfo";
    $stmtUserInfo->execute();

    // 插入commonusers表
    $sqlCommonUsers = "INSERT INTO commonusers (UserId, UserName, ClassId, Gender_male, ActivityParticipation, Misbehavior) VALUES (?, ?, ?, ?, 0, 0)";
    $stmtCommonUsers = $conn->prepare($sqlCommonUsers);
    $stmtCommonUsers->bind_param("isii", $userId, $userName, $classId, $genderMale);
    // echo "$sqlCommonUsers";
    $stmtCommonUsers->execute();

    if ($stmtUserInfo->affected_rows > 0 && $stmtCommonUsers->affected_rows > 0) {
        echo "<script>alert('注册成功');window.location.href='login.php';</script>";
        // header("Location: login.php");
    } else {
        echo "<script>alert('注册失败');</script>";
    }

    $stmtUserInfo->close();
    $stmtCommonUsers->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <form action="" method="post">
        <!-- UserId: <input type="text" name="UserId" required><br> -->
        UserId: <input type="number" name="UserId" required><br>
        <!-- Password: <input type="password" name="Password" required><br> -->
        Password: <input type="password" name="Password" required><br>
        PhoneNo: <input type="text" name="PhoneNo" required><br>
        Email: <input type="email" name="Email" required><br>
        UserName: <input type="text" name="UserName" required><br>
        ClassId: <input type="number" name="ClassId" required><br>
        Gender_male: <input type="radio" name="Gender_male" value="1" required> Male
                 <input type="radio" name="Gender_male" value="0"> Female<br>
        <!-- isAdmin: <input type="checkbox" name="isAdmin" value="1"><br> -->
        <input type="submit" value="Register">
    </form>
</body>
</html>