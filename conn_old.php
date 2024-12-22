<?php
// 通过信息链接数据库
$hostname = "localhost"; // 主机名,可以用IP代替
$database = "cams"; // 数据库名
$username ="root"; // 数据库用户名
$password = "MyRootPass"; // 数据库密码
$conn = mysqli_connect($hostname, $username, $password,$database) or trigger_error(mysqli_error() , E_USER_ERROR);

if($conn){
    echo "Successfully connected.\n";
}
else{
    echo "Connection failed.\n";
}

?>