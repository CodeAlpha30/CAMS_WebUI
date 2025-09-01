<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['UserId'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['UserId'];
// $currentDate = date("Y-m-d H:i:s");
// echo "$currentDate";

// $sql = "SELECT a.ActvtId, a.ActvtTitle, d.DeptName, a.ActvtTime, p.PlaceName, a.PeopleNumRqrd, a.PeopleNumIn, a.Intro, a.OtherRqrments, a.Notes, a.AdminName
//         FROM activities a
//         JOIN departments d ON a.DeptId = d.DeptId
//         JOIN places p ON a.PlaceId = p.PlaceId
//         WHERE a.ActvtTime > Now() AND a.Status = 0";
$sql = "SELECT ActvtId, ActvtTitle, DeptName, ActvtTime, PlaceName, PeopleNumRqrd, PeopleNumIn, Intro, OtherRqrments, Notes, AdminName
        FROM view_activities_details
        WHERE ActvtTime > Now() 
        AND Status = 0
        AND PeopleNumRqrd > PeopleNumIn
        ";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    // 处理 prepare 失败的情况
    die('Prepare failed: ' . $conn->error);
}
// $stmt->bind_param("s", $currentDate);
// $stmt->execute();
if (!$stmt->execute()) {
    // 处理 execute 失败的情况
    die('Execute failed: ' . $stmt->error);
}
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>活动列表</title>
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
    </style>
</head>
<body>
    <h1>活动列表</h1>
    <div style="display: flex; flex-wrap: wrap;">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="card">
                <h2><?php echo htmlspecialchars($row['ActvtTitle']); ?></h2>
                <p><?php echo htmlspecialchars($row['DeptName']); ?></p>
                <p><?php echo htmlspecialchars($row['ActvtTime']); ?></p>
                <p><?php echo htmlspecialchars($row['PlaceName']); ?></p>
                <p><?php echo htmlspecialchars($row['PeopleNumIn']) . "/" . htmlspecialchars($row['PeopleNumRqrd']); ?></p>
                <p><?php echo htmlspecialchars($row['Intro']); ?></p>
                <p><?php echo htmlspecialchars($row['OtherRqrments']); ?></p>
                <p><?php echo htmlspecialchars($row['Notes']); ?></p>
                <p><?php echo htmlspecialchars($row['AdminName']); ?></p>
                <?php
                $participationStatus = "未申请";
                $participationSql = "SELECT Status FROM participation WHERE UserId = ? AND ActvtId = ?";
                $participationStmt = $conn->prepare($participationSql);
                $participationStmt->bind_param("ii", $userId, $row['ActvtId']);
                $participationStmt->execute();
                $participationResult = $participationStmt->get_result();
                if ($participationResult->num_rows > 0) {
                    $participationRow = $participationResult->fetch_assoc();
                    // echo "" . $participationRow['Status'];
                    $participationStatus = $participationRow['Status'] == 0 ? "待审核" : ($participationRow['Status'] == 1 ? "审核通过" : ($participationRow['Status'] == 5 ? "人数已满，审核未通过" : ($participationRow['Status'] == 11 ? "已撤回" : "审核未通过")));
                }
                ?>
                <?php if ($participationResult->num_rows == 0 && $row['PeopleNumIn'] < $row['PeopleNumRqrd']) { ?>
                    <button onclick="addParticipation(<?php echo $row['ActvtId']; ?>)">添加</button>
                <?php } else if ($row['PeopleNumIn'] >= $row['PeopleNumRqrd']) { ?>
                    <p>Status: 人数已满，<?php echo $participationStatus; ?></p>
                <?php } else { ?>
                    <button onclick="withdrawParticipation(<?php echo $row['ActvtId']; ?>)">撤回申请</button>
                    <p>Status: <?php echo $participationStatus; ?></p>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
    <script>
        function addParticipation(actvtId) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "add_participation.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    alert(this.responseText);
                }
            };
            xhr.send("actvtId=" + actvtId + "&userId=<?php echo $userId; ?>");
        }

        function withdrawParticipation(actvtId) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "withdraw_participation.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    alert(this.responseText);
                }
            };
            xhr.send("actvtId=" + actvtId + "&userId=<?php echo $userId; ?>");
        }
    </script>
</body>
</html>