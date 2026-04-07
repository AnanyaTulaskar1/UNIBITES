<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$shops = [];
$sql = "SELECT id, name, email, status, created_at FROM users WHERE role = 'shop' ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $shops[] = $row;
    }
    mysqli_free_result($result);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shops - UniBites</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f5f5f5; padding: 18px; }
        .top { display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; }
        .top a { text-decoration: none; background: #111827; color: #fff; padding: 8px 12px; border-radius: 8px; display: inline-block; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        th, td { padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        th { background: #f3f4f6; }
        .empty { background: #fff; border-radius: 10px; padding: 14px; margin-top: 12px; }
    </style>
</head>
<body>
    <div class="top">
        <div>
            <a href="dashboard.php">Back</a>
        </div>
        <h2>Shop Accounts</h2>
    </div>

    <?php if (empty($shops)): ?>
        <div class="empty">No shops found.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Shop Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shops as $shop): ?>
                    <tr>
                        <td><?= (int) $shop['id'] ?></td>
                        <td><?= htmlspecialchars((string) $shop['name']) ?></td>
                        <td><?= htmlspecialchars((string) $shop['email']) ?></td>
                        <td><?= htmlspecialchars((string) $shop['status']) ?></td>
                        <td><?= htmlspecialchars((string) $shop['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>

