<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['UserId'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['UserId'];
$currentDate = date("Y-m-d H:i:s");

// 获取当前管理员发布的活动信息
$sql = "SELECT a.ActvtId, a.ActvtTitle, a.DeptId, a.ActvtTime, a.PlaceId, a.PeopleNumRqrd, a.PeopleNumIn, a.Intro, a.OtherRqrments, a.Notes, a.Status, a.AdministrationAuth
        FROM activities a
        WHERE a.PublisherId = ? 
        -- AND a.ActvtTime > ? 
        AND a.Status <> 2";

$stmt = $conn->prepare($sql);
// $stmt->bind_param("is", $userId, $currentDate);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

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
    <title>我的活动管理页面</title>
    <style>
        .card {
            margin: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            margin: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>我的活动管理页面</h1>
    <a href="activities_add.php">添加活动</a>
    <div style="display: flex; flex-wrap: wrap;">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="card">
                <h2><?php echo htmlspecialchars($row['ActvtTitle']); ?></h2>
                <!-- Form for updating activity info -->
                <form method="post" action="update_activity.php">
                    <input type="hidden" name="ActvtId" value="<?php echo $row['ActvtId']; ?>">
                    <label for="ActvtTitle">活动标题:</label>
                    <input type="text" id="ActvtTitle" name="ActvtTitle" value="<?php echo htmlspecialchars($row['ActvtTitle']); ?>" required><br>
                    
                    <label for="DeptId">部门ID:</label>
                    <select name="DeptId" id="DeptId">
                        <option value="<?php echo $row['DeptId']; ?>"><?php echo htmlspecialchars($row['DeptId']); ?></option>
                        <!-- Populate departments here -->
                    </select><br>
                    
                    <label for="ActvtTime">活动时间:</label>
                    <input type="datetime-local" id="ActvtTime" name="ActvtTime" value="<?php echo htmlspecialchars($row['ActvtTime']); ?>" required><br>
                    
                    <label for="PlaceId">地点ID:</label>
                    <select name="PlaceId" id="PlaceId">
                        <option value="<?php echo $row['PlaceId']; ?>"><?php echo htmlspecialchars($row['PlaceId']); ?></option>
                        <!-- Populate places here -->
                    </select><br>
                    
                    <label for="PeopleNumRqrd">所需人数:</label>
                    <input type="number" id="PeopleNumRqrd" name="PeopleNumRqrd" value="<?php echo $row['PeopleNumRqrd']; ?>" required><br>
                    
                    <label for="PeopleNumIn">已报名人数:</label>
                    <input type="number" id="PeopleNumIn" name="PeopleNumIn" value="<?php echo $row['PeopleNumIn']; ?>" required><br>
                    
                    <label for="Intro">介绍:</label>
                    <textarea id="Intro" name="Intro"><?php echo htmlspecialchars($row['Intro']); ?></textarea><br>
                    
                    <label for="OtherRqrments">其他要求:</label>
                    <textarea id="OtherRqrments" name="OtherRqrments"><?php echo htmlspecialchars($row['OtherRqrments']); ?></textarea><br>
                    
                    <label for="Notes">备注:</label>
                    <textarea id="Notes" name="Notes"><?php echo htmlspecialchars($row['Notes']); ?></textarea><br>
                    
                    <button type="submit">提交修改</button>
                    <button onclick="confirm('确定要删除活动吗？') ? location.href='delete_activity.php?ActvtId=<?php echo $row['ActvtId']; ?>' : ''">删除活动</button>
                    <button><a href="application_view.php?ActvtId=<?php echo $row['ActvtId']; ?>">浏览申请</a></button>
                </form>
            </div>
        <?php } ?>
    </div>
</body>
</html>