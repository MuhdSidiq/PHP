<?php

function createDatabase() {
    $host = "127.0.0.1";
    $username = "root";
    $password = "";
    $database = "php_demo";

    try {
        $pdo = new PDO("mysql:host=$host;port=3306", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->exec(statement: "CREATE DATABASE IF NOT EXISTS $database");
        $pdo->exec("USE $database");

        return $pdo;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

function createTables($pdo) {
    try {
        $sql = "
        CREATE TABLE IF NOT EXISTS roles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL UNIQUE
        );

        CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            category_id INT,
            stock INT DEFAULT 0,
            image VARCHAR(100), 
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT,
            quantity INT NOT NULL,
            customer_name VARCHAR(100) NOT NULL,
            customer_email VARCHAR(100) NOT NULL,
            order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            role_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
        );
        ";

        $pdo->exec($sql);
        return true;
    } catch(PDOException $e) {
        return "Error creating tables: " . $e->getMessage();
    }
}

function populateData($pdo) {
    try {
        $check = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
        if ($check > 0) {
            return "Data already exists.";
        }

        $categories = [
            ['Electronics', 'Electronic devices and gadgets'],
            ['Clothing', 'Apparel and fashion items'],
            ['Books', 'Educational and entertainment books'],
            ['Sports', 'Sports equipment and accessories'],
            ['Home & Garden', 'Home improvement and gardening items']
        ];

        $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        foreach ($categories as $category) {
            $stmt->execute($category);
        }

        $products = [
            ['Laptop Dell', 2500.00, 1, 10],
            ['iPhone 15', 4500.00, 1, 15],
            ['Gaming Mouse', 150.00, 1, 25],
            ['T-Shirt Nike', 85.00, 2, 50],
            ['Jeans Levis', 250.00, 2, 30],
            ['Sneakers Adidas', 350.00, 2, 20],
            ['PHP Programming', 65.00, 3, 15],
            ['Web Development', 85.00, 3, 12],
            ['Football', 45.00, 4, 40],
            ['Tennis Racket', 180.00, 4, 8],
            ['Garden Tools Set', 120.00, 5, 18],
            ['Plant Pot', 25.00, 5, 60]
        ];

        $stmt = $pdo->prepare("INSERT INTO products (name, price, category_id, stock) VALUES (?, ?, ?, ?)");
        foreach ($products as $product) {
            $stmt->execute($product);
        }

        $orders = [
            [1, 2, 'Ahmad Ali', 'ahmad@email.com'],
            [3, 1, 'Siti Aminah', 'siti@email.com'],
            [5, 1, 'Rahman Hassan', 'rahman@email.com'],
            [2, 1, 'Fatimah Omar', 'fatimah@email.com'],
            [7, 3, 'Zakaria Ismail', 'zakaria@email.com']
        ];

        $stmt = $pdo->prepare("INSERT INTO orders (product_id, quantity, customer_name, customer_email) VALUES (?, ?, ?, ?)");
        foreach ($orders as $order) {
            $stmt->execute($order);
        }

        // Add sample users
        $users = [
            ['admin', 'admin@example.com', password_hash('admin123', PASSWORD_DEFAULT)],
            ['testuser', 'test@example.com', password_hash('test123', PASSWORD_DEFAULT)],
            ['demo', 'demo@example.com', password_hash('demo123', PASSWORD_DEFAULT)]
        ];

        // Seed roles and map sample users to roles
        $roles = [
            ['Admin'],
            ['User']
        ];

        $stmt = $pdo->prepare("INSERT INTO roles (name) VALUES (?)");
        foreach ($roles as $role) {
            $stmt->execute($role);
        }

        // Fetch role ids
        $roleStmt = $pdo->prepare("SELECT id FROM roles WHERE name = ?");
        $roleStmt->execute(['Admin']);
        $adminRoleId = (int)$roleStmt->fetchColumn();
        $roleStmt->execute(['User']);
        $userRoleId = (int)$roleStmt->fetchColumn();

        // Insert users with role_id
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role_id) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin', 'admin@example.com', password_hash('admin123', PASSWORD_DEFAULT), $adminRoleId]);
        $stmt->execute(['testuser', 'test@example.com', password_hash('test123', PASSWORD_DEFAULT), $userRoleId]);
        $stmt->execute(['demo', 'demo@example.com', password_hash('demo123', PASSWORD_DEFAULT), $userRoleId]);

        return "Sample data inserted successfully!";
    } catch(PDOException $e) {
        return "Error inserting data: " . $e->getMessage();
    }
}

function setupDatabase() {
    $messages = [];

    $pdo = createDatabase();
    $messages[] = "Database 'php_demo' created/connected successfully!";

    $result = createTables($pdo);
    if ($result === true) {
        $messages[] = "Tables created successfully!";
    } else {
        $messages[] = $result;
    }

    $result = populateData($pdo);
    $messages[] = $result;

    return $messages;
}

if (isset($_GET['run'])) {
    $results = setupDatabase();
}
?>

<html>
    <head>
        <title>Database Setup</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="style.css">
    </head>
    <body class="bg-light">

        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white text-center">
                            <h4 class="mb-0">Database Setup</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($results)): ?>
                                <div class="alert alert-info">
                                    <h5>Setup Results:</h5>
                                    <ul class="mb-0">
                                        <?php foreach ($results as $message): ?>
                                            <li><?php echo $message; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="d-grid gap-2 mt-3">
                                    <a href="view.php" class="btn btn-success">View Products</a>
                                    <a href="add.php" class="btn btn-primary">Add Product</a>
                                </div>
                            <?php else: ?>
                                <div class="text-center">
                                    <h5>Initialize Database</h5>
                                    <p class="text-muted">Click the button below to create the database, tables, and sample data.</p>
                                    <div class="alert alert-warning">
                                        <strong>Note:</strong> Make sure MySQL server is running and the connection settings in config.php are correct.
                                    </div>
                                    <a href="?run=1" class="btn btn-primary btn-lg">Setup Database</a>
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