<?php

// 导入conn.php文件
include 'conn_old.php';
include 'fun.php';

// 接受table.php传来的表名和每一个字段的值，向数据库中的这个表添加一行数据
$table = $_POST['table'];
$field_name = get_field_name($table);
$data = array();
for ($i = 0; $i < count($field_name); $i++) {
    array_push($data, $_POST[$field_name[$i]]);
}

// 设计sql语句，向表$table中插入数据
$sql = "INSERT INTO $table VALUES (";
for ($i = 0; $i < count($data); $i++) {
    if ($data[$i] == '') {
        $sql .= "NULL,";
    }
    else {
        $sql .= "'$data[$i]',";
    }
}
$sql = substr($sql, 0, -1);
$sql .= ")";
// echo "$sql\n";
// 执行sql语句
$res = mysqli_query($conn, $sql);

// 使用js判断是否添加成功
if ($res) {
    echo "success";
    echo "<script>alert('添加成功');location.href='table.php?table=$table';</script>";
} else {
    $error_prompt = mysqli_error($conn);
    echo "fail\n";
    echo "<script>alert('添加失败');location.href='table.php?table=$table';</script>";
    die('MySQL query error: ' . $error_prompt);
}
?>