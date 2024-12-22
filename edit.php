<?php

// 连接数据库
include 'conn_old.php';
include 'fun.php';

// 获取表名，id和数据，设计sql更新$table中id值为$id的数据
$table = $_POST['table'];
$id = $_POST['id'];
$id_col = get_field_name($table)[0];

// 获取数据
$data = $_POST['data'];

// 设计sql语句，更新表$table中id值为$id的数据
$sql = "UPDATE $table SET ";
foreach ($data as $key => $value) {
    if ($value == '') {
        continue;
    }
    else {
        $sql .= get_field_name($table)[$key] . " = '$value', ";
    }
}
$sql = substr($sql, 0, -2);
$sql .= " WHERE $id_col = '$id'";
// 执行sql语句
echo $sql;
$res = mysqli_query($conn, $sql);

// 使用js判断是否修改成功
if ($res) {
    echo "success";
    echo "<script>alert('修改成功');location.href='table.php?table=$table';</script>";
} else {
    $error_prompt = mysqli_error($conn);
    echo "fail\n";
    echo "<script>alert('修改失败');location.href='table.php?table=$table';</script>";
    die('MySQL query error: ' . $error_prompt);
}

?>