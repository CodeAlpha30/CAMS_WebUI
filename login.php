<?php
// login.php
include 'conn.php'; // 包含数据库连接文件

session_start(); // 开始session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 从表单获取用户名和密码
    $userId = $_POST['UserId'];
    $password = $_POST['Password'];

    // 连接数据库
    // $conn = new mysqli($servername, $username, $password, $dbname);
    // if ($conn->connect_error) {
    //     die("Connection failed: " . $conn->connect_error);
    // }

    // 验证用户名和密码
    $sql = "SELECT * FROM userinfo WHERE UserId = ? AND Password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $userId, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // 用户名和密码正确
        $row = $result->fetch_assoc();
        $_SESSION['UserId'] = $row['UserId']; // 存储用户ID到session
        $_SESSION['isAdmin'] = $row['isAdmin']; // 存储用户是否为管理员到session
        $_SESSION['UserName'] = $row['UserName']; // 存储用户名到session

        if ($row['isAdmin'] == 1) {
            // 跳转到管理员界面
            header("Location: admin_dashboard.php");
        } else {
            // 跳转到普通用户界面
            header("Location: user_dashboard.php");
        }
    } else {
        // 用户名不存在或密码错误
        echo "<script>alert('用户名不存在或密码不正确');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <form action="" method="post">
        UserId: <input type="text" name="UserId" required><br>
        Password: <input type="password" name="Password" required><br>
        <input type="submit" value="Login">
        <a href="register.php">Register</a>
    </form>
</body>
</html>