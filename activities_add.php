<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['UserId'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['UserId'];

// 获取departments表和places表的数据填充下拉列表
$sqlDept = "SELECT DeptId, DeptName FROM departments";
$stmtDept = $conn->prepare($sqlDept);
$stmtDept->execute();
$resultDept = $stmtDept->get_result();

$sqlPlace = "SELECT PlaceId, PlaceName FROM places";
$stmtPlace = $conn->prepare($sqlPlace);
$stmtPlace->execute();
$resultPlace = $stmtPlace->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>添加一个新活动</title>
    <style>
        form {
            margin: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        label, input, select, button {
            display: block;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>添加一个新活动</h1>
    <form action="update_activity.php" method="post">
        <label for="ActvtTitle">活动标题:</label>
        <input type="text" id="ActvtTitle" name="ActvtTitle" required><br>
        
        <label for="DeptId">部门ID:</label>
        <select name="DeptId" id="DeptId">
            <option value="">请选择部门</option>
            <?php while ($rowDept = $resultDept->fetch_assoc()) { ?>
                <option value="<?php echo $rowDept['DeptId']; ?>"><?php echo htmlspecialchars($rowDept['DeptName']); ?></option>
            <?php } ?>
        </select><br>
        
        <label for="ActvtTime">活动时间:</label>
        <input type="datetime-local" id="ActvtTime" name="ActvtTime" required><br>
        
        <label for="PlaceId">地点ID:</label>
        <select name="PlaceId" id="PlaceId">
            <option value="">请选择地点</option>
            <?php while ($rowPlace = $resultPlace->fetch_assoc()) { ?>
                <option value="<?php echo $rowPlace['PlaceId']; ?>"><?php echo htmlspecialchars($rowPlace['PlaceName']); ?></option>
            <?php } ?>
        </select><br>
        
        <label for="PeopleNumRqrd">所需人数:</label>
        <input type="number" id="PeopleNumRqrd" name="PeopleNumRqrd" required><br>
        
        <label for="PeopleNumIn">已报名人数:</label>
        <input type="number" id="PeopleNumIn" name="PeopleNumIn" required><br>
        
        <label for="Intro">介绍:</label>
        <textarea id="Intro" name="Intro"></textarea><br>
        
        <label for="OtherRqrments">其他要求:</label>
        <textarea id="OtherRqrments" name="OtherRqrments"></textarea><br>
        
        <label for="Notes">备注:</label>
        <textarea id="Notes" name="Notes"></textarea><br>
        
        <label for="AdministrationAuth">管理权限:</label>
        <textarea id="AdministrationAuth" name="AdministrationAuth"></textarea><br>
        
        <input type="hidden" name="Status" value="0">
        <input type="hidden" name="PublisherId" value="<?php echo $userId; ?>">
        
        <button type="submit">提交</button>
    </form>
</body>
</html>