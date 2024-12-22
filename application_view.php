<?php
include 'conn.php';
session_start();

if (!isset($_GET['ActvtId'])) {
    header("Location: activities_manage.php");
    exit;
}

$actvtId = $_GET['ActvtId'];
$userId = $_SESSION['UserId'];

// 获取活动标题
$sqlActvtTitle = "SELECT * FROM activities WHERE ActvtId = ?";
$stmtActvtTitle = $conn->prepare($sqlActvtTitle);
$stmtActvtTitle->bind_param("i", $actvtId);
$stmtActvtTitle->execute();
$resultActvtTitle = $stmtActvtTitle->get_result();
$actvtTitleRow = $resultActvtTitle->fetch_assoc();
$actvtTitle = $actvtTitleRow['ActvtTitle'];
$pnr = $actvtTitleRow['PeopleNumRqrd'];
$pni = $actvtTitleRow['PeopleNumIn'];

// 获取参与人员信息
$sqlParticipation = "SELECT p.UserId, cu.UserName, p.Status, IFNULL(cu.Misbehavior, 0) AS Misbehavior, p.ActvtId
                     FROM participation p
                     JOIN commonusers cu ON p.UserId = cu.UserId
                     WHERE p.ActvtId = ? AND p.Status IN (0, 1, 5, 11, 2)";

$stmtParticipation = $conn->prepare($sqlParticipation);
$stmtParticipation->bind_param("i", $actvtId);
$stmtParticipation->execute();
$resultParticipation = $stmtParticipation->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($actvtTitle); ?> - 申请浏览</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .status-pending { color: black; }
        .status-approved { color: green; }
        .status-rejected { color: red; }
        .status-full { color: red; }
        .status-withdrawn { color: grey; }
        button {
            margin: 5px;
            cursor: pointer;
        }
        button:disabled {
            cursor: default;
        }
    </style>

    <script defer>
        function approveApplication(actvtId, userId) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "approve_reject.php?action=approve&ActvtId=" + actvtId + "&UserId=" + userId, true);
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    alert(this.responseText);
                    location.reload();
                }
            };
            xhr.send();
        }

        function rejectApplication(actvtId, userId) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "approve_reject.php?action=reject&ActvtId=" + actvtId + "&UserId=" + userId, true);
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    alert(this.responseText);
                    location.reload();
                }
            };
            xhr.send();
        }

        function statusClassName(status) {
            // Return the class name based on the status code
            switch (status) {
                case 0: return 'pending';
                case 1: return 'approved';
                case 2: return 'rejected';
                case 5: return 'full';
                case 11: return 'withdrawn';
                default: return 'pending';
            }
        }

        function statusText(status) {
            // Return the text based on the status code
            switch (status) {
                case 0: return '待审核';
                case 1: return '审核通过';
                case 2: return '审核未通过';
                case 5: return '人数已满，审核未通过';
                case 11: return '已撤回';
                default: return '未知状态';
            }
        }
    </script>
</head>
<body>
    <h1><?php echo htmlspecialchars($actvtTitle); ?> - 申请浏览</h1>
    <p>人数信息：<?php echo "已报名人数" . htmlspecialchars($pni) . "/所需人数" . htmlspecialchars($pnr); ?></p>
    
    <?php
        
        // 在 PHP 中定义 statusClassName 和 statusText 函数
        function statusClassName($status) {
            switch ($status) {
                case 0: return 'pending';
                case 1: return 'approved';
                case 2: return 'rejected';
                case 5: return 'full';
                case 11: return 'withdrawn';
                default: return 'pending';
            }
        }

        function statusText($status) {
            switch ($status) {
                case 0: return '待审核';
                case 1: return '审核通过';
                case 2: return '审核未通过';
                case 5: return '人数已满，审核未通过';
                case 11: return '已撤回';
                default: return '未知状态';
            }
        }
    ?>

    <table>
        <tr>
            <th>UserId</th>
            <th>UserName</th>
            <th>Status</th>
            <th>操作</th>
        </tr>
        <?php while ($row = $resultParticipation->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['UserId']); ?></td>
                <td><?php echo htmlspecialchars($row['UserName']); ?></td>
                <td class="status-<?php echo statusClassName($row['Status']); ?>">
                    <?php echo statusText($row['Status']); ?>
                </td>
                <td>
                    <?php if ($row['Status'] != 11) { ?>
                        <?php if ($row['Status'] != 1 && $row['Status'] != 5) { ?>
                            <button onclick="approveApplication(<?php echo $row['ActvtId']; ?>, <?php echo $row['UserId']; ?>)">审核通过</button>
                        <?php } ?>
                        <?php if ($row['Status'] != 1 && $row['Status'] != 2 && $row['Status'] != 5) { ?>
                            <button onclick="rejectApplication(<?php echo $row['ActvtId']; ?>, <?php echo $row['UserId']; ?>)">拒绝申请</button>
                        <?php } ?>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>