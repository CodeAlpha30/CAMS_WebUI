<?php
// 定义一个函数，参数为表名，用来返回表的字段名
function get_field_name($table){
    include 'conn.php';
    $sql = "SHOW COLUMNS FROM $table";
    $result = mysqli_query($conn, $sql);
    $field_name = array();
    while ($row = mysqli_fetch_array($result)) {
        $field_name[] = $row['Field'];
    }
    return $field_name;
}
?>