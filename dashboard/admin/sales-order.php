<?php
require_once '../authentication/class.php';

$sales_order = new IMS();

if (!$sales_order->isUserLogged()) {
    header("Location: ../../");
    exit;
}

$stmt = $sales_order->runQuery("SELECT id, product_name FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if (isset($_POST['btn-create-order'])) {
    $customer_id = $_POST['customer_id'];
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $order_date = $_POST['order_date'];

    // Fetch product details and current stock
    $stmt = $sales_order->runQuery("SELECT id, stock FROM products WHERE id = :product_name");
    $stmt->execute([':product_name' => $product_name]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $current_stock = $product['stock'];

        // Check if there is enough stock
        if ($current_stock < $quantity) {
            $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Insufficient stock for this product'];
        } else {
            // Create the sales order
            $stmt = $sales_order->runQuery("INSERT INTO sales_orders (customer_id, product_name, quantity, order_date) 
                                            VALUES (:customer_id, :product_name, :quantity, :order_date)");
            $stmt->execute([
                ':customer_id' => $customer_id,
                ':product_name' => $product_name,
                ':quantity' => $quantity,
                ':order_date' => $order_date
            ]);

            // Update the stock
            $new_stock = $current_stock - $quantity;
            $stmt = $sales_order->runQuery("UPDATE products SET stock = :new_stock WHERE id = :product_name");
            $stmt->execute([
                ':new_stock' => $new_stock,
                ':product_name' => $product_name
            ]);

            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Sales order created and stock updated'];
        }
    } else {
        $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Product not found'];
    }
    header("Location: sales-order.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Sales Order</title>
    <link href="../../src/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../../src/css/admin.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <?php include '../../includes/sidebar-admin.php' ?>

        <div class="main-content d-flex justify-content-center align-items-center">
            <div class="card" style="width: 600px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Create Sales Order</h3>
                    <?php
                    if (isset($_SESSION['alert']) && isset($_SESSION['alert']['type']) && isset($_SESSION['alert']['message'])) {
                        $alert = $_SESSION['alert'];
                        echo "  <div class='alert alert-{$alert['type']} alert-dismissible fade show' role='alert'>
                                    {$alert['message']}
                                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                </div>";
                        unset($_SESSION['alert']);
                    }
                    ?>
                    <form method="POST" action="sales-order.php">
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Customer ID</label>
                            <input type="text" class="form-control" id="customer_id" name="customer_id" required>
                        </div>
                        <div class="mb-3">
                            <label for="product_name" class="form-label">Select Product</label>
                            <select class="form-select" id="product_name" name="product_name" required>
                                <option value="" disabled selected>Select a product</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['id'] ?>"><?= $product['product_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <div class="mb-3">
                            <label for="order_date" class="form-label">Order Date</label>
                            <input type="date" class="form-control" id="order_date" name="order_date" required>
                        </div>
                        <div class="d-flex justify-content-end pt-3">
                            <button type="submit" class="btn btn-success" name="btn-create-order">Create Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../../src/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
