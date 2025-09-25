<?php
include 'config.php';

$id = $_GET['id'];

if ($_POST) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $stock = $_POST['stock'];

    $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, category_id = ?, stock = ? WHERE id = ?");
    $stmt->execute([$name, $price, $category_id, $stock, $id]);

    $success = "Product updated successfully!";
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: view.php');
    exit;
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<html>
    <head>
        <title>Edit Product</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="style.css">
    </head>
    <body class="bg-light">

        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white text-center">
                            <h4 class="mb-0">Edit Product</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($success)): ?>
                                <div class="alert alert-success"><?php echo $success; ?></div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Product Name</label>
                                    <input type="text" name="name" value="<?php echo $product['name']; ?>" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Price (RM)</label>
                                    <input type="number" name="price" value="<?php echo $product['price']; ?>" step="0.01" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Category</label>
                                    <select name="category_id" class="form-control" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>"
                                                    <?php echo $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                                <?php echo $category['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Stock</label>
                                    <input type="number" name="stock" value="<?php echo $product['stock']; ?>" class="form-control" required>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-warning">Update Product</button>
                                    <a href="view.php" class="btn btn-secondary">Back to List</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>