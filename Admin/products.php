<?php
session_start();
require '../includes/db.php';

// mPDF print handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['print_products'])) {
    require '../vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf();

    $stmt = $pdo->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $html = '<html><head><style>
        body { font-family: Arial, sans-serif; font-size:12px; }
        h4 { text-align:center; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #000; padding:8px; text-align:left; }
        .signature { margin-top:40px; text-align:right; }
    </style></head><body>';
    $html .= '<h4>Product List</h4><table><thead><tr><th>#</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th></tr></thead><tbody>';
    $i=1;
    foreach ($products as $p) {
        $html .= '<tr>' .
                 '<td>' . $i++ . '</td>' .
                 '<td>' . htmlspecialchars($p['product_name']) . '</td>' .
                 '<td>' . htmlspecialchars($p['category']) . '</td>' .
                 '<td>' . number_format($p['price'],2) . '</td>' .
                 '<td>' . $p['stock'] . '</td>' .
                 '</tr>';
    }
    $html .= '</tbody></table><div class="signature"><p>________________________</p><p><strong>General Manager</strong></p></div></body></html>';

    $mpdf->WriteHTML($html);
    $mpdf->Output('products.pdf','I');
    exit;
}

if (isset($_GET['delete'])) {
    $productId = intval($_GET['delete']);
    
    // Check if stock is 0
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product && $product['stock'] > 0) {
        echo "<script>alert('Cannot delete product with remaining stock!'); window.location='products.php';</script>";
        exit;
    }

    // Proceed with deletion if stock is 0
    $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
    $stmt->execute([$productId]);
    header('Location: products.php');
    exit;
}
// Edit init
$editMode=false; $editData=['id'=>0,'product_name'=>'','category'=>'','price'=>0,'stock'=>0,'image'=>''];
if (isset($_GET['edit'])) {
    $stmt=$pdo->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([intval($_GET['edit'])]);
    if($r=$stmt->fetch(PDO::FETCH_ASSOC)){$editMode=true; $editData=$r;}
}
// Handle add/update
if($_SERVER['REQUEST_METHOD']==='POST' && !isset($_POST['print_products'])){
    $name=trim($_POST['productName']); $cat=trim($_POST['category']);
    $price=floatval($_POST['price']); $stk=intval($_POST['stock']);
    $imgPath=$editData['image'];
    if(!empty($_FILES['image']['name'])){
        $u='../uploads/'; $in=basename($_FILES['image']['name']);
        if(move_uploaded_file($_FILES['image']['tmp_name'], $u.$in)) $imgPath=$u.$in;
    }
    if(isset($_POST['add_product'])){
        $st=$pdo->prepare("INSERT INTO products(product_name,category,price,stock,image)VALUES(?,?,?,?,?)");
        $st->execute([$name,$cat,$price,$stk,$imgPath]);
    } elseif(isset($_POST['update_product'])){
        $st=$pdo->prepare("UPDATE products SET product_name=?,category=?,price=?,stock=?,image=? WHERE id=?");
        $st->execute([$name,$cat,$price,$stk,$imgPath,$editData['id']]);
    }
    header('Location: products.php'); exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
            <title>Products</title>
            <link rel="stylesheet" href="styles/style.css">
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="icon" type="image/png" href="../images/ice.png">
            <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel ="stylesheet">

            <script>
                // Load theme on page load
                window.addEventListener('DOMContentLoaded', () => {
                    const savedTheme = localStorage.getItem('theme') || 'light';
                    document.body.classList.add(`${savedTheme}-mode`);
                });
            </script>
    </head>

    <body>
        <div class="dashboard">
            <div class="sidebar">
                <div class="heads"> P R O D U C T
                <img src="../images/wp.png" alt="logo"> 
            </div>
          <div class="sidebar2">
            <ul class="menu">
                <li>
                    <a href="index.php">
                        <img src="../images/dashboard.png" alt="dashboard">
                        <span style="padding-left: 10px;">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="products.php" class="active">
                        <img src="../images/product.png" alt="products">
                        <span style="padding-left: 10px;">Products</span>
                    </a>
                </li>
                <li>
                    <a href="booking_table.php">
                        <img src="../images/book.jpg" alt="bookings">
                        <span style="padding-left: 10px;">Bookings</span>
                    </a>
                </li>
                <li>
                    <a href="reports.php">
                        <img src="../images/report.png" alt="reports">
                        <span style="padding-left: 10px;">Reports</span>
                    </a>
                </li>
                <li>
                    <a href="account.php">
                        <img src="../images/account.png" alt="accounts">
                        <span style="padding-left: 10px;">Accounts</span>
                    </a>
                </li>
                <li>
                    <a href="settings.php">
                        <img src="../images/settings.png" alt="settings">
                        <span style="padding-left: 10px;">Settings</span>
                    </a>
                </li>
                <li>
                    <a href="#" onclick="confirmLogout()">
                        <img src="../images/logout.png" alt="logout">
                        <span style="padding-left: 10px;">Logout</span>
                    </a>
                </li>
                
                <script>
                    function confirmLogout() {
                        // Display a confirmation dialog
                        const userConfirmed = confirm("Are you sure you want to log out?");
                        
                        // If the user clicks "OK", redirect to the home page (logout)
                        if (userConfirmed) {
                            window.location.href = "../home.php";
                        }
                    }
                </script>
            </ul>  </div>
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
                <h2 style="padding: 20px;" >Product Inventory</h2>
<div class="inventory-container">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <div><?php $cnt=$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();?><p>Total Products: <span><?= $cnt ?></span></p></div>
    <div>
      <!-- Print Form -->
      <form method="POST" style="display:inline;">
        <button type="submit" name="print_products" class="btn btn-secondary me-2">Print Products</button>
      </form>
      <?php if($editMode): ?><a href="products.php" class="btn btn-primary">Back to Add</a>
      <?php else: ?><button id="showAddBtn" class="btn btn-primary">Add Product</button><?php endif; ?>
    </div>
  </div>
  <?php if($editMode): ?>
    <h4>Edit Product</h4><form method="POST" enctype="multipart/form-data" style="max-width:400px;">
      <div class="mb-2"><input type="file" name="image" class="form-control"><?php if($editData['image']):?><img src="<?= $editData['image'] ?>" width="50"><?php endif;?></div>
      <input type="text" name="productName" value="<?= htmlspecialchars($editData['product_name']) ?>" required class="form-control mb-2">
      <input type="text" name="category" value="<?= htmlspecialchars($editData['category']) ?>" required class="form-control mb-2">
      <input type="number" name="price" step="0.01" value="<?= $editData['price'] ?>" required class="form-control mb-2">
      <input type="number" name="stock" value="<?= $editData['stock'] ?>" required class="form-control mb-2">
      <button type="submit" name="update_product" class="btn btn-success">Update</button>
    </form>
  <?php else: ?>
    <form id="addForm" method="POST" enctype="multipart/form-data" style="display:none; max-width:400px; margin-bottom:20px;">
      <div class="mb-2"><input type="file" name="image" required class="form-control"></div>
      <input type="text" name="productName" placeholder="Product Name" required class="form-control mb-2">
      <input type="text" name="category" placeholder="Category" required class="form-control mb-2">
      <input type="number" name="price" step="0.01" placeholder="Price" required class="form-control mb-2">
      <input type="number" name="stock" placeholder="Stock" required class="form-control mb-2">
      <button type="submit" name="add_product" class="btn btn-success">Save</button>
    </form>
  <?php endif; ?>
  <div id="printable">
    <table class="table mt-3">
        <thead class="table-success">
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach($pdo->query("SELECT * FROM products ORDER BY id DESC") as $p){ echo "<tr>".
      "<td><img src='{$p['image']}' width='50'></td>".
      "<td>{$p['product_name']}</td>".
      "<td>{$p['category']}</td>".
      "<td>P".number_format($p['price'],2)."</td>".
      "<td>{$p['stock']}</td>".
      "<td><a href='?edit={$p['id']}' class='btn btn-sm btn-primary'>Edit</a> " .
          "<a href='?delete={$p['id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Delete?');\">Delete</a></td>".
      "</tr>";} ?>
  </tbody></table></div>
</div></div></div>
<script>document.getElementById('showAddBtn')?.addEventListener('click',()=>document.getElementById('addForm').style.display='block');function confirmLogout(){if(confirm('Logout?'))location.href='../home.php';}</script>
</body></html>
