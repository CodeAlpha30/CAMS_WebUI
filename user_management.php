<?php
include 'conn.php';
session_start();

$userId = $_SESSION['UserId'];

$sql = "SELECT u.*, a.AdminName as an, c.UserName as un
        FROM userinfo u
        LEFT JOIN adminusers a ON u.UserId = a.UserId
        LEFT JOIN commonusers c ON u.UserId = c.UserId";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>用户管理</title>
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
    </style>
</head>
<body>
    <h1>用户管理</h1>
    <table>
        <tr>
            <th>UserId</th>
            <th>AdminName</th>
            <th>UserName</th>
            <th>操作</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['UserId']); ?></td>
                <td><?php echo htmlspecialchars($row['an']); ?></td>
                <td><?php echo htmlspecialchars($row['un']); ?></td>
                <td>
                    <?php if ($row['UserId'] != $userId) { ?>
                        
                        <?php if ($row['isAdmin'] == 0) { ?>
                            <button onclick="confirm('确定要删除用户吗？') ? location.href='delete_user.php?UserId=<?php echo $row['UserId']; ?>' : ''">删除</button>
                            <button onclick="location.href='make_admin.php?UserId=<?php echo $row['UserId']; ?>'">设为管理员</button>
                        <?php } ?>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>