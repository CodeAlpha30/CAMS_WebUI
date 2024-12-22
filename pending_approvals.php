<?php
include 'conn.php';
session_start();

$userId = $_SESSION['UserId'];

$sql = "SELECT a.ActvtId, a.ActvtTitle
        FROM activities a
        JOIN participation p ON a.ActvtId = p.ActvtId
        WHERE a.PublisherId = ? AND a.PeopleNumIn < a.PeopleNumRqrd AND p.Status = 0
        GROUP BY a.ActvtId";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>待审批的申请</title>
</head>
<body>
    <h1>待审批的申请</h1>
    <ul>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <li><a href="application_view.php?ActvtId=<?php echo $row['ActvtId']; ?>"><?php echo htmlspecialchars($row['ActvtTitle']); ?></a></li>
        <?php } ?>
    </ul>
</body>
</html>