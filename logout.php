<?php
// logout.php
session_start(); // 开始session

// 销毁所有session变量
$_SESSION = array();

// 如果使用基于cookies的session，则删除cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 销毁session
session_destroy();

// 重定向到登录页面
header("Location: login.php");
exit;
?>