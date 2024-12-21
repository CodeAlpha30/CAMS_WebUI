<?php

// 导入conn.php文件
include 'conn.php';
include 'fun.php';

// 接受从table.php传来的表名和id值，删除这一行数据
$table = $_GET['table'];
$id_col = get_field_name($table)[0];
$id = $_GET['id'];

// 设计sql语句，删除表$table中id值为$id的数据
$sql = "DELETE FROM $table WHERE $id_col = '$id'";
// 执行sql语句
$res = mysqli_query($conn, $sql);

// 使用js判断是否删除成功
if ($res) {
    echo "success";
    echo "<script>alert('删除成功');location.href='table.php?table=$table';</script>";
} else {
    echo "fail\n";
    die('MySQL query error: ' . mysqli_error($conn));
    echo "<script>alert('删除失败');location.href='table.php?table=$table';</script>";
}

?>