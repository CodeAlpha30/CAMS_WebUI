<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['UserId'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['UserId'];
$currentDate = date("Y-m-d H:i:s");

$sql = "SELECT a.ActvtId, a.ActvtTitle, d.DeptName, a.ActvtTime, p.PlaceName, a.PeopleNumRqrd, a.PeopleNumIn, a.Intro, a.OtherRqrments, a.Notes, au.AdminName, pr.Status
        FROM participation pr
        JOIN activities a ON pr.ActvtId = a.ActvtId
        JOIN adminusers au ON a.PublisherId = au.UserId
        JOIN departments d ON a.DeptId = d.DeptId
        JOIN places p ON a.PlaceId = p.PlaceId
        WHERE pr.UserId = ? AND a.ActvtTime > ? AND a.Status = 0";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    // 处理 prepare 失败的情况
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("is", $userId, $currentDate);
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
    <title>已提交的申请</title>
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
    <h1>已提交的申请</h1>
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
                <p>Status: <?php echo $row['Status'] == 0 ? "待审核" : ($row['Status'] == 1 ? "审核通过" : ($row['Status'] == 5 ? "人数已满，审核未通过" : ($row['Status'] == 11 ? "已撤回" : "审核未通过"))); ?></p>
                <button onclick="withdrawParticipation(<?php echo $row['ActvtId']; ?>)">撤回申请</button>
            </div>
        <?php } ?>
    </div>
    <script>
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