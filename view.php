<?php
include 'config.php';

session_start();

$currentUser = null;
$canDelete = false;


if (isset($_SESSION['user_id'])) {
    $userStmt = $pdo->prepare(query: "SELECT u.username, u.email, u.role_id, r.name AS role_name FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE u.id = ?");
    $userStmt->execute([$_SESSION['user_id']]);
    $currentUser = $userStmt->fetch();

    if ($currentUser) {
        $roleName = ($currentUser['role_name'] ?? '');
        $isStaff = ($roleName === 'staff');
        $canDelete = !($isStaff || (($currentUser['role_id'] ?? 0) === 2));
    }
}

echo '<pre>';
print_r($currentUser);
echo '<br>Session ID: ' . session_id();
echo '</pre>';
echo '<pre>';
print_r($_SESSION);
echo '<br>Session ID: ' . session_id();
echo '</pre>';
echo '<pre>';
print_r(session_status());
echo '</pre>';





$stmt = $pdo->query("
    SELECT p.*, c.name as category_name 
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC
");
$products = $stmt->fetchAll();

$productCount = count(value: $products);

$AgentPrice = 1.2;

?>

<html>
    <head>
        <title>View Products</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="style.css">
    </head>
    <body class="bg-light">

        <div class="container py-5">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">All Products <span class="badge bg-light text-dark ms-2"><?php echo count($products); ?></span></h4>
                            <a href="add.php" class="btn btn-light btn-sm">Add New Product</a>
                        </div>
                        <div class="card-body">
                            <?php if (count($products) > 0): ?> // Line no 33. Jika ada data product, maka keluarkan data product.
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Product Name</th>
                                                <?php if (empty($isStaff) || !$isStaff): ?>
                                                    <th>SupplierPrice</th>
                                                <?php endif; ?>
                                                <th> Agent or Selling Price</th>
                                                <th>Category</th>
                                                <th>Stock</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($products as $product): ?>
                                                <tr>
                                                    <td><?php echo $product['id']; ?></td>
                                                    <td><?php echo strtoupper(string: $product['name']); ?></td>
                                                    <?php if (empty($isStaff) || !$isStaff): ?>
                                                        <td>RM<?php echo number_format($product['price'], 2); ?></td>
                                                    <?php endif; ?>
                                                    <td>RM<?php echo number_format($product['price'] * $AgentPrice, 2); ?></td>
                                                    <td>
                                                        <span class="badge bg-info"><?php echo strtoupper($product['category_name']); ?></span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $stock = $product['stock'];
                                                        $badgeClass = '';
                                                        switch (true) {
                                                            case $stock == 0:
                                                                $badgeClass = 'danger';
                                                                break;
                                                            case $stock <= 5:
                                                                $badgeClass = 'warning';
                                                                break;
                                                            case $stock <= 20:
                                                                $badgeClass = 'info';
                                                                break;
                                                            default:
                                                                $badgeClass = 'success';
                                                                break;
                                                        }
                                                        ?>
                                                        <span class="badge bg-<?php echo $badgeClass; ?>">
                                                            <?php echo $product['stock']; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('d/m/Y', strtotime($product['created_at'])); ?></td>
                                                    <td>
                                                        <a href="edit.php?id=<?php echo $product['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                                        <?php if ($canDelete): ?>
                                                            <a href="delete.php?id=<?php echo $product['id']; ?>"
                                                               class="btn btn-danger btn-sm"
                                                               onclick="return confirm('Are you sure?')">Delete</a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <p class="text-muted">No products found.</p>
                                    <a href="add.php" class="btn btn-primary">Add First Product</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>