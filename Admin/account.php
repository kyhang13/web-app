<?php
session_start();
require '../includes/db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$editId = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Delete user if requested
if (isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM accounts WHERE id = ?");
    $stmt->execute([$deleteId]);
    header("Location: account.php");
    exit;
}

// Update user
if (isset($_POST['edit_id'])) {
    $stmt = $pdo->prepare("UPDATE accounts SET fullname = ?, email = ?, status = ? WHERE id = ?");
    $stmt->execute([$_POST['fullname'], $_POST['email'], $_POST['status'], $_POST['edit_id']]);
    header("Location: account.php");
    exit;
}

// Fetch accounts with pagination
try {
    if ($search !== '') {
        $stmt = $pdo->prepare("SELECT * FROM accounts WHERE fullname LIKE ? OR email LIKE ? LIMIT $limit OFFSET $offset");
        $stmt->execute(["%$search%", "%$search%"]);

        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM accounts WHERE fullname LIKE ? OR email LIKE ?");
        $countStmt->execute(["%$search%", "%$search%"]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM accounts LIMIT $limit OFFSET $offset");
        $stmt->execute();

        $countStmt = $pdo->query("SELECT COUNT(*) FROM accounts");
    }

    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $totalAccounts = $countStmt->fetchColumn();
    $totalPages = ceil($totalAccounts / $limit);
} catch (PDOException $e) {
    die("Error fetching accounts: " . $e->getMessage());
}

// Get user for editing
$editUser = null;
if ($editId) {
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
    $stmt->execute([$editId]);
    $editUser = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Accounts</title>
    <link rel="stylesheet" href="styles/style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/ice.png">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.body.classList.add(`${savedTheme}-mode`);
        });
    </script>
</head>


<body>
<div class="dashboard">
    <div class="sidebar">
        <div class="heads">A C C O U N T S <img src="../images/wp.png" alt="logo"></div>
        <div class="sidebar2">
            <ul class="menu">
                <li><a href="index.php"><img src="../images/dashboard.png" alt="dashboard"><span style="padding-left: 10px;">Dashboard</span></a></li>
                <li><a href="products.php"><img src="../images/product.png" alt="products"><span style="padding-left: 10px;">Products</span></a></li>
                <li><a href="booking_table.php"><img src="../images/book.jpg" alt="bookings"><span style="padding-left: 10px;">Bookings</span></a></li>
                <li><a href="reports.php"><img src="../images/report.png" alt="reports"><span style="padding-left: 10px;">Reports</span></a></li>
                <li><a href="account.php"  class="active"><img src="../images/account.png" alt="accounts"><span style="padding-left: 10px;">Accounts</span></a></li>
                <li><a href="settings.php"><img src="../images/settings.png" alt="settings"><span style="padding-left: 10px;">Settings</span></a></li>
                
                <li>
                    <a href="#" onclick="confirmLogout()"><img src="../images/logout.png"> <span>Logout</span></a>
                    <script>
                        function confirmLogout() {
                            if (confirm("Are you sure you want to log out?")) {
                                window.location.href = "../home.php";
                            }
                        }
                    </script>
                </li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div class="search">
                <input type="text" placeholder="Search...">
            </div>
            <div class="user-profile">
                <span>MCG Creamline</span>
                <img src="../images/ice.png" alt="User">
            </div>
        </div>

            <div class="content"style="overflow-y: auto;">
                <h2 class="text-center mb-4">User Accounts</h2>

                <form method="GET" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="<?= htmlspecialchars($search) ?>" id="searchInput">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>

                <script>
                    const searchInput = document.getElementById('searchInput');
                    searchInput.addEventListener('input', function () {
                        if (searchInput.value === '') {
                            this.form.submit();
                        }
                    });
                </script>

                <?php if ($editUser): ?>
                    <form method="POST" class="mb-4">
                        <h4>Edit User</h4>
                        <input type="hidden" name="edit_id" value="<?= $editUser['id'] ?>">
                        <div class="mb-2">
                            <input type="text" name="fullname" class="form-control" placeholder="Full Name" value="<?= htmlspecialchars($editUser['fullname']) ?>" required>
                        </div>
                        <div class="mb-2">
                            <input type="email" name="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($editUser['email']) ?>" required>
                        </div>
                        <div class="mb-2">
                            <select name="status" class="form-control">
                                <option value="active" <?= $editUser['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $editUser['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                        <a href="account.php" class="btn btn-secondary">Cancel</a>
                    </form>
                <?php endif; ?>

                <?php if (count($accounts) > 0): ?>
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($accounts as $index => $account): ?>
                                <tr>
                                    <td><?= $offset + $index + 1; ?></td>
                                    <td><?= htmlspecialchars($account['fullname']); ?></td>
                                    <td><?= htmlspecialchars($account['email']); ?></td>
                                    <td><?= ucfirst($account['status']); ?></td>
                                    <td>
                                        <a href="?edit=<?= $account['id']; ?>&page=<?= $page ?>&search=<?= urlencode($search) ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <form method="POST" onsubmit="return confirm('Delete this user?');" style="display:inline;">
                                            <input type="hidden" name="delete_id" value="<?= $account['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <nav>
                        <ul class="pagination justify-content-center mt-4">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?= http_build_query(['page' => $page - 1, 'search' => $search]) ?>">← Prev</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?<?= http_build_query(['page' => $i, 'search' => $search]) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?= http_build_query(['page' => $page + 1, 'search' => $search]) ?>">Next →</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php else: ?>
                    <div class="alert alert-warning text-center">No user found.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<script>
    function toggleMode() {
        document.body.classList.toggle("dark-mode");
        localStorage.setItem("mode", document.body.classList.contains("dark-mode") ? "dark" : "light");
    }

    window.onload = () => {
        const params = new URLSearchParams(window.location.search);
        if (params.get("mode") === "dark" || localStorage.getItem("mode") === "dark") {
            document.body.classList.add("dark-mode");
        }
    };
</script>

</body>
</html>
