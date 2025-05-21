<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../includes/db.php';

    $mpdf = new \Mpdf\Mpdf();
    header('Content-Type: application/pdf');

    $stmt = $pdo->query("SELECT * FROM order_completed");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = 1;
    $totalRevenue = 0;

    $html = '
        <html>
        <head>
            <style>
                body {
                    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                    font-size: 12px;
                    padding: 20px;
                    color: #333;
                }
                h4 {
                    text-align: center;
                    margin-bottom: 20px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 11px;
                }
                th, td {
                    border: 1px solid #ccc;
                    padding: 8px;
                    text-align: left;
                }
                th {
                    background-color: #f2f2f2;
                }
                .signature-section {
                    margin-top: 40px;
                    display: flex;
                    justify-content: space-between;
                    font-size: 11px;
                }
                .signature {
                    width: 50%;
                    text-align: center;
                }
                .total-section {
                    margin-top: 20px;
                    font-size: 12px;
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <h4>Completed Orders Report</h4>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Booked Date</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Total Price</th>
                        <th>Delivery Date</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>';

    foreach ($orders as $order) {
        $html .= '
            <tr>
                <td>' . $count++ . '</td>
                <td>' . htmlspecialchars($order['name']) . '</td>
                <td>' . htmlspecialchars($order['email']) . '</td>
                <td>' . htmlspecialchars($order['address']) . '</td>
                <td>' . htmlspecialchars($order['booked_date']) . '</td>
                <td>' . htmlspecialchars($order['product_name']) . '</td>
                <td>' . htmlspecialchars($order['quantity']) . '</td>
                <td>' . htmlspecialchars($order['payment_method']) . '</td>
                <td>' . htmlspecialchars($order['status']) . '</td>
                <td>' . htmlspecialchars(number_format($order['total_price'], 2)) . '</td>
                <td>' . htmlspecialchars(date('F j, Y', strtotime($order['delivery_date']))) . '</td>
                <td>' . htmlspecialchars($order['created_at']) . '</td>
            </tr>';
        
        $totalRevenue += $order['total_price'];
    }

    $html .= '
                </tbody>
            </table>

            <div class="total-section">
                <p><strong>Total Completed Orders: ' . ($count - 1) . '</strong></p>
                <p><strong>Total Revenue: ₱' . number_format($totalRevenue, 2) . '</strong></p>
            </div>

            <div class="signature-section">
                <div class="signature">
                    <p>___________________________</p>
                    <p><strong>General Manager</strong></p>
                </div>
            </div>
        </body>
        </html>';

    $mpdf->SetHTMLFooter('<div style="text-align: left;">Page {PAGENO}/{nbpg}</div>');
    $mpdf->WriteHTML($html);
    $mpdf->Output('Completed_Orders_Report.pdf', 'I');
    exit;
} else {
    echo "Access Denied.";
}
?>
