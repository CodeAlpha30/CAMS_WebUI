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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-image: url('LoginPageBackg0.jpg'); /* 设置背景图片 */
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }

        .login-card {
            width: 300px;
            padding: 20px;
            background-color: white;
            opacity: 0.9; /* 设置透明度为50% */
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .login-card input[type="text"],
        .login-card input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .login-card input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
        }

        .login-card button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
        }

        .login-card input[type="submit"]:hover,
        .login-card button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function goToLink() {
            window.location.href = 'register.php';
        }
    </script>
</head>
<body>
    <div class="login-card">
        <h2>CAMS Login</h2>
        <form action="" method="post">
            <input type="text" placeholder="UserId" name="UserId" required>
            <input type="password" placeholder="Password" name="Password" required>
            <input type="submit" value="Login">
            <button onclick="goToLink()">Register</button>
        </form>
    </div>
</body>
</html>
