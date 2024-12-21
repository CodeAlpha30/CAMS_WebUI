<?php
// 用来接受showTable.php传来的表名，并查询这个表的所有内容
include 'conn.php';
include 'fun.php';

// 接受showTable.php传来的表名
$table = $_GET['table'];
 
// 获取表的字段名
$field_name = get_field_name($table);
$id_col = $field_name[0];

// 查询这个表的所有内容
$sql = "SELECT * FROM $table";
$result = mysqli_query($conn, $sql);

// 输出表的内容
// 每一行的末尾有一个删除按钮，点击这个按钮可以传递id值，调用'delete.php'文件删除这一行数据
echo "<table border='1'>";
echo "<tr>";
foreach ($field_name as $value) {
    echo "<td>$value</td>";
}
echo "<td>Action</td>";
echo "</tr>";

while ($row = mysqli_fetch_array($result)) {
    echo "<tr>";
    // 每一行是一个form表单，默认值是这一行的数据，点击提交按钮可以传递数据到'edit.php'文件，数据用data[]数组接受
    echo "<form action='edit.php' method='post'>";
    foreach ($field_name as $value) {
        echo "<td><input type='text' name='data[]' value='$row[$value]'></td>";
    }
    echo "<td><input type='hidden' name='table' value='" . htmlspecialchars($table) . "'><input type='hidden' name='id' value='$row[$id_col]'><input type='submit' value='Submit'><a href='delete.php?table=$table&id=$row[$id_col]'>Delete</a></td>";
    echo "</form>"; 
    echo "</tr>";
}

// 使用html form表单提交数据，将数据传递给'add.php'文件，用来添加数据
echo "<form action='add.php' method='post'>";
echo "<tr>";
foreach ($field_name as $value) {
    echo "<td><input type='text' name='$value'></td>";
}
echo "<td><input type='hidden' name='table' value='" . htmlspecialchars($table) . "'>";
echo "</tr>";
echo "<tr>";
echo "<td colspan='", count($field_name) + 1, "'><input type='submit' value='AddData'></td>";
echo "</tr>";
echo "</form>";

echo "</table>";
?>
