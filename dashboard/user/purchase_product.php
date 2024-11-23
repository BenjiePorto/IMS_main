<?php
session_start();

require_once '../authentication/class.php';

$product = new IMS();

if (!$product->isUserLogged()) {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Please log in to continue.'];
    header("Location: ../../"); // Redirect to login page
    exit;
}

// Fetch available products
$stmt = $product->runQuery("SELECT id, product_name, stock, price FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle POST request for purchasing a product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Session expired. Please log in again.'];
        header("Location: ../../"); // Redirect to login page
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $total_price = floatval($_POST['total_price']);

    // Validate inputs
    if ($quantity <= 0 || $total_price <= 0) {
        $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Invalid input. Please try again.'];
        header("Location: purchase_product.php");
        exit;
    }

    // Fetch product details
    $stmt = $product->runQuery("SELECT stock, price FROM products WHERE id = :product_id");
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product_data && $product_data['stock'] >= $quantity) {
        $new_stock = $product_data['stock'] - $quantity;

        // Update product stock
        $stmt = $product->runQuery("UPDATE products SET stock = :new_stock WHERE id = :product_id");
        $stmt->bindParam(':new_stock', $new_stock, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();

        // Insert into orders
        $stmt = $product->runQuery("INSERT INTO orders (product_id, user_id, quantity, total_price, order_date) 
                                    VALUES (:product_id, :user_id, :quantity, :total_price, NOW())");
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':total_price', $total_price, PDO::PARAM_STR);
        $stmt->execute();

        // Success message
        $_SESSION['alert'] = ['type' => 'success', 'message' => 'Purchase successful!'];
    } else {
        // Insufficient stock or invalid product
        $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Insufficient stock or invalid product.'];
    }

    // Reload the page to display the alert
    header("Location: purchase_product.php");
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Product</title>
    <link rel="stylesheet" href="../../src/css/bootstrap.css">
    <link rel="stylesheet" href="../../src/css/user.css">
</head>

<body>
    <div class="wrapper">
        <?php include '../../includes/sidebar-user.php' ?>

        <div class="main-content">
            <h1>Purchase Products</h1>

            <?php
            if (isset($_SESSION['alert']) && isset($_SESSION['alert']['type']) && isset($_SESSION['alert']['message'])) {
                $alert = $_SESSION['alert'];
                echo "<div class='alert alert-{$alert['type']} alert-dismissible fade show' role='alert'>
                        {$alert['message']}
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                     </div>";
                unset($_SESSION['alert']);
            }
            ?>

            <div class="table-responsive mt-4">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                                    <td>$<?= number_format($product['price'], 2) ?></td>
                                    <td><?= $product['stock'] ?></td>
                                    <form action="purchase_product.php" method="POST">
                                        <td>
                                            <input type="number" name="quantity" min="1" max="<?= $product['stock'] ?>" class="form-control" required>
                                        </td>
                                        <td>
                                            $<span id="total-price-<?= $product['id'] ?>">0.00</span>
                                            <input type="hidden" name="total_price" id="hidden-total-<?= $product['id'] ?>" value="0">
                                        </td>
                                        <td>
                                            <button type="submit" name="product_id" value="<?= $product['id'] ?>" class="btn btn-sm btn-primary">Purchase</button>
                                        </td>
                                    </form>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No products available</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer class="text-center py-3">
        <p>&copy; 2024 Inventory Management System</p>
    </footer>

    <script src="../../src/js/bootstrap.bundle.min.js"></script>
    <script>
    
        const updateTotalPrice = (productId, price) => {
            const quantity = document.querySelector(`[name="quantity"]`).value;
            const totalPrice = (quantity * price).toFixed(2);
            document.getElementById(`total-price-${productId}`).textContent = totalPrice;
            document.getElementById(`hidden-total-${productId}`).value = totalPrice;
        }

        document.querySelectorAll('[name="quantity"]').forEach(input => {
            input.addEventListener('input', (e) => {
                const productId = e.target.closest('form').querySelector('[name="product_id"]').value;
                const price = parseFloat(e.target.closest('form').querySelector('td:nth-child(2)').textContent.replace('$', ''));
                updateTotalPrice(productId, price);
            });
        });
    </script>
</body>

</html>
