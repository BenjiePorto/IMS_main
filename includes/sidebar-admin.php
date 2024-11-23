<nav class="sidebar d-flex flex-column p-3">
    <h4 class="text-white mb-4 text-center">Admin Panel</h4>
    <hr>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="user-management.php" class="nav-link">User Management</a>
        </li>

        <li class="nav-item">
            <a href="#productDropdown" class="nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="productDropdown">
                Product
                <i class="bi bi-caret-down-fill"></i>
            </a>
            <div id="productDropdown" class="collapse">
                <ul class="nav flex-column ps-3">
                    <li class="nav-item">
                        <a href="product.php" class="nav-link">Product List</a>
                    </li>
                    <li class="nav-item">
                        <a href="purchase-order.php" class="nav-link">Purchase Order</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a href="sales-order.php" class="nav-link">Sales Order</a>
        </li>
        <li class="nav-item">
            <a href="supplier.php" class="nav-link">Supplier</a>
        </li>
    </ul>
    <div class="mt-auto">
        <button class="btn btn-success w-100"><a href="../authentication/class.php?signout" class="text-decoration-none text-white">Sign Out</a></button>
    </div>
</nav>