<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['order_id'])) {
    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../includes/db.php';

    $orderId = $_GET['order_id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            die("Order not found.");
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }

    // Initialize mPDF
    $mpdf = new \Mpdf\Mpdf();

    // Prepare HTML content
    $html = '
    <html>
    <head>
        <style>
            body {
                font-family: "Arial", sans-serif;
                font-size: 20px;
                color: #333;
                padding: 20px;
            }
            h4 {
                text-align: center;
                margin-bottom: 20px;
            }
            .section-title {
                font-weight: bold;
                margin-top: 20px;
                border-bottom: 1px solid #ccc;
                padding-bottom: 5px;
                color: #6f42c1;
            }
            .order-label {
                font-weight: bold;
                color: #000;
            }
            p {
                margin: 5px 0;
            }
        </style>
    </head>
    <body>
        <h4>Order Details</h4>
    
        <div class="section-title">Customer Information</div>
        <p><span class="order-label">Name:</span> ' . htmlspecialchars($order['name']) . '</p>
        <p><span class="order-label">Email:</span> ' . htmlspecialchars($order['email']) . '</p>
        <p><span class="order-label">Address:</span> ' . htmlspecialchars($order['address']) . '</p>
    
        <div class="section-title">Order Information</div>
        <p><span class="order-label">Product:</span> ' . htmlspecialchars($order['product_name']) . '</p>
        <p><span class="order-label">Quantity:</span> ' . htmlspecialchars($order['quantity']) . '</p>
        <p><span class="order-label">Payment Method:</span> ' . htmlspecialchars($order['payment_method']) . '</p>
        <p><span class="order-label">Status:</span> ' . htmlspecialchars($order['status']) . '</p>
        <p><span class="order-label">Booked Date:</span> ' . date("F j, Y", strtotime($order["booked_date"])) . '</p>';
    
    if (isset($order['total_price'])) {
        $html .= '<p><span class="order-label">Total Price:</span> ₱' . number_format($order['total_price'], 2) . '</p>';
    }
    
    $html .= '
        <br><br>
        <div style="margin-top: 60px; text-align: center;">
            ___________________________<br>
            <strong>Authorized Signature</strong>
        </div>
    </body>
    </html>';
    

    // Footer with page number
    $mpdf->SetHTMLFooter('
        <div style="text-align: right; font-size: 10px;">
            Page {PAGENO} of {nbpg}
        </div>');

    // Write and output the PDF
    $mpdf->WriteHTML($html);
    $mpdf->Output('Order_Details_' . $orderId . '.pdf', 'I'); // Inline display
    exit;

} else {
    echo "Access Denied.";
}
?>
