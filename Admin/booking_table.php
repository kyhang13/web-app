<?php
session_start();
require '../includes/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$mail = new PHPMailer(true);

// Only admin access
if (!isset($_SESSION['role_as']) || $_SESSION['role_as'] != 1) {
    $_SESSION['error'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit();
}

// Move completed bookings to order_completed if 7 days after approval
try {
    $stmt = $pdo->query("SELECT * FROM bookings WHERE status = 'Approved' AND approved_at IS NOT NULL");
    while ($booking = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $approvedDate = new DateTime($booking['approved_at']);
        $deliveryDate = clone $approvedDate;
        $deliveryDate->modify('+7 days');
        $today = new DateTime();

        if ($today >= $deliveryDate) {
            $insert = $pdo->prepare("INSERT INTO order_completed (name, email, product_name, quantity, payment_method, status, delivery_date)
                                     VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insert->execute([
                $booking['name'],
                $booking['email'],
                $booking['product_name'],
                $booking['quantity'],
                $booking['payment_method'],
                'Completed',
                $deliveryDate->format('Y-m-d')
            ]);

            $delete = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
            $delete->execute([$booking['id']]);
        }
    }
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error moving completed bookings: " . $e->getMessage() . "</div>";
}

// Approve booking and send email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve'])) {
    $bookingId = $_POST['booking_id'];
    $approvedAt = date('Y-m-d H:i:s');

    try {
        $stmt = $pdo->prepare("SELECT email, name FROM bookings WHERE id = ?");
        $stmt->execute([$bookingId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($booking) {
            $stmt = $pdo->prepare("UPDATE bookings SET status = 'Approved', approved_at = ? WHERE id = ?");
            $stmt->execute([$approvedAt, $bookingId]);

            $email = $booking['email'];
            $name = $booking['name'];
            $deliveryDate = date('F j, Y', strtotime($approvedAt . ' +7 days'));

            $subject = "Booking Approved - MCGCreamline";
            $message = "Dear $name,\n\nYour booking has been approved.\nPlease expect delivery on or before $deliveryDate.\n\nThank you for booking with us!\n\n- Creamline";

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['USER_EMAIL'];
                $mail->Password = $_ENV['USER_PASS'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                $mail->setFrom('acyhang13@gmail.com', 'Ailyn Cabanas');
                $mail->addAddress($email, $name);
                $mail->isHTML(false);
                $mail->Subject = $subject;
                $mail->Body    = $message;

                $mail->send();
                $_SESSION['success'] = "Booking approved and email sent to $email.";
            } catch (Exception $e) {
                $_SESSION['error'] = "Booking approved, but email failed: " . $mail->ErrorInfo;
            }
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();

    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error approving booking: " . $e->getMessage() . "</div>";
    }
}

// Fetch users
try {
    $stmt = $pdo->prepare("SELECT * FROM accounts");
    $stmt->execute();
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching accounts: " . $e->getMessage());
}

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total_orders FROM bookings");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalOrders = $row['total_orders'];
} catch (PDOException $e) {
    $totalOrders = "Error";
}

try {
    $stmt = $pdo->query("SELECT SUM(total_price) AS total_sales FROM bookings WHERE status != 'Cancelled'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalSales = number_format($row['total_sales'] ?? 0, 2);
} catch (PDOException $e) {
    $totalSales = "Error";
}

$userCount = count($accounts);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bookings</title>
    <link rel="stylesheet" href="styles/style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/ice.png">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.body.classList.add(`${savedTheme}-mode`);
        });
    </script>
</head>
<style>
.container.mt-4 {
    max-height: 80vh;
    overflow-y: auto;
    padding: 20px;
}
</style>
<body>
<div class="dashboard">
    <div class="sidebar">
        <div class="heads"> B O O K I N G S 
            <img src="../images/wp.png" alt="logo">
        </div>
        <div class="sidebar2">
            <ul class="menu">
                <li><a href="index.php"><img src="../images/dashboard.png" alt="dashboard"><span style="padding-left: 10px;">Dashboard</span></a></li>
<li><a href="products.php"><img src="../images/product.png" alt="products"><span style="padding-left: 10px;">Products</span></a></li>
<li><a href="booking_table.php" class="active"><img src="../images/book.jpg" alt="bookings"><span style="padding-left: 10px;">Bookings</span></a></li>
<li><a href="reports.php"><img src="../images/report.png" alt="reports"><span style="padding-left: 10px;">Reports</span></a></li>
<li><a href="account.php" ><img src="../images/account.png" alt="accounts"><span style="padding-left: 10px;">Accounts</span></a></li>
<li><a href="settings.php"><img src="../images/settings.png" alt="settings"><span style="padding-left: 10px;">Settings</span></a></li>
<li>
    <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" alt="logout"><span style="padding-left: 10px;">Logout</span></a>
</li>
<script>
    function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = "../home.php";
        }
    }
</script>

            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div class="search"><input type="text" placeholder="Search..."></div>
            <div class="user-profile"><span>MCG Creamline</span><img src="../images/ice.png" alt="User"></div>
        </div>

        <div class="container mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-center m-0">User Bookings</h3>
                <div>
                    <form method="POST" action="print_report.php" target="_blank">
                        <button type="submit" class="btn btn-success">Completed Orders</button>
                    </form>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success text-center"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger text-center"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <table class="table table-bordered text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Booked Product</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Booked Date</th>
                        <th>View Orders</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $stmt = $pdo->query("SELECT id, name, email, address, product_name, quantity, total_price, payment_method, status, booked_date FROM bookings");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                    echo "<td>₱" . number_format($row['total_price'], 2) . "</td>";
                    echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";

                    echo "<td>";
                    if ($row['status'] === 'Pending') {
                        echo "Pending ";
                        echo "<form method='POST' action='' style='display:inline;'>
                                <input type='hidden' name='booking_id' value='" . $row['id'] . "'>
                                <button type='submit' name='approve' class='btn btn-warning btn-sm'>Approve</button>
                              </form>";
                    } else {
                        echo htmlspecialchars($row['status']);
                    }
                    echo "</td>";

                    echo "<td>" . date('F j, Y', strtotime($row['booked_date'])) . "</td>";

                    echo "<td>
                            <form method='GET' action='view_order.php' style='display:inline;'>
                                <input type='hidden' name='order_id' value='" . $row['id'] . "'>
                                <button type='submit' class='btn btn-primary btn-sm'>View</button>
                          </form>
                          </td>";
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
