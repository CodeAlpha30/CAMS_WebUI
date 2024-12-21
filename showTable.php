<?php
// 导入conn.php文件
include 'conn.php';
// 展示这个数据库中所有的表
$sql = "SHOW TABLES";
$result = mysqli_query($conn, $sql);
if ($result) {
    echo "Query done\n";
} else {
    echo "Query failed\n";
}
// 输出表名，同时点击表名可以调用table.php文件查看表的内容
echo "<table>";
while ($row = mysqli_fetch_array($result)) {
    echo "<tr><td><a href='table.php?table=$row[0]'><font color='black' underline='false'>$row[0]</a><br></td></tr>";
}
echo "</table>";
?>