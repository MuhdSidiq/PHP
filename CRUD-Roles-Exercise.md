# CRUD Operations with Role-Based Access Control - Exercise Guide

## Overview
This exercise will guide you through building a complete CRUD (Create, Read, Update, Delete) system with role-based access control using PHP and MySQL. You'll learn how to implement different user roles and restrict access to various operations based on user permissions.

## Learning Objectives
- Understand CRUD operations in PHP
- Implement role-based access control (RBAC)
- Learn database design for user roles and permissions
- Practice security best practices
- Build a complete user management system


## User Story:
- CUSTOMER NAK SISTEM PENGURUSAN PRODUK
- SIAPA PENGGUNA ? ADMIN, STAFF JUAL, STAFF STOCK
- ADMIN Boleh buat apa ?


## MULA DARI MANA ?
- Database Design - https://drawsql.app/login
- CRUD BASED PAGE - 


## Database Schema

### 1. Users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);
```

### 2. Roles Table
```sql
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 3. Permissions Table (Policies)
```sql
CREATE TABLE permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 4. Role_Permissions Table (Many-to-Many)
```sql
CREATE TABLE role_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_role_permission (role_id, permission_id)
);
```

### 5. Products Table (Enhanced)
```sql
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    category_id INT,
    stock INT DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

### 6. Categories Table
```sql
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Exercise 1: Database Setup

### Task 1.1: Create Database and Tables
Create a new database called `php_crud_roles` and implement all the tables above.

### Task 1.2: Insert Sample Data
```sql
-- Insert roles
INSERT INTO roles (name, description) VALUES 
('admin', 'Full system access'),
('manager', 'Can manage products and view reports'),
('editor', 'Can create and edit products'),
('viewer', 'Can only view products');

-- Insert permissions
INSERT INTO permissions (name, description) VALUES 
('create_product', 'Create new products'),
('read_product', 'View products'),
('update_product', 'Edit existing products'),
('delete_product', 'Delete products'),
('manage_users', 'Manage user accounts'),
('view_reports', 'Access reporting features'),
('manage_categories', 'Manage product categories');

-- Assign permissions to roles
INSERT INTO role_permissions (role_id, permission_id) VALUES 
-- Admin gets all permissions
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7),
-- Manager permissions
(2, 1), (2, 2), (2, 3), (2, 6), (2, 7),
-- Editor permissions
(3, 1), (3, 2), (3, 3),
-- Viewer permissions
(4, 2);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES 
('Electronics', 'Electronic devices and gadgets'),
('Clothing', 'Apparel and fashion items'),
('Books', 'Books and educational materials'),
('Home & Garden', 'Home improvement and garden supplies');

-- Create sample users (passwords are 'password123')
INSERT INTO users (username, email, password_hash, role_id) VALUES 
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('manager1', 'manager@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2),
('editor1', 'editor@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3),
('viewer1', 'viewer@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4);
```

## Exercise 2: Authentication System Enhancement

### Task 2.1: Create Role-Based Authentication Class
Create `auth.php`:

```php
<?php
class Auth {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function login($username, $password) {
        $stmt = $this->pdo->prepare("
            SELECT u.*, r.name as role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE u.username = ?
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role_name'];
            $_SESSION['logged_in'] = true;
            return true;
        }
        return false;
    }
    
    public function hasPermission($permission) {
        if (!isset($_SESSION['user_id'])) return false;
        
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM role_permissions rp
            JOIN permissions p ON rp.permission_id = p.id
            JOIN users u ON u.role_id = rp.role_id
            WHERE u.id = ? AND p.name = ?
        ");
        $stmt->execute([$_SESSION['user_id'], $permission]);
        return $stmt->fetchColumn() > 0;
    }
    
    public function requirePermission($permission) {
        if (!$this->hasPermission($permission)) {
            header('Location: unauthorized.php');
            exit;
        }
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public function logout() {
        session_destroy();
        header('Location: login.php');
        exit;
    }
}
?>
```

### Task 2.2: Create Unauthorized Access Page
Create `unauthorized.php`:

```php
<?php
session_start();
include 'config.php';
include 'auth.php';

$auth = new Auth($pdo);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Access Denied</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white text-center">
                        <h4>Access Denied</h4>
                    </div>
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-5x text-danger mb-3"></i>
                        <h5>You don't have permission to access this page.</h5>
                        <p class="text-muted">Contact your administrator if you believe this is an error.</p>
                        <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                        <a href="logout.php" class="btn btn-secondary">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
```

## Exercise 3: Role-Based CRUD Operations

### Task 3.1: Create Dashboard with Role-Based Navigation
Create `dashboard.php`:

```php
<?php
session_start();
include 'config.php';
include 'auth.php';

$auth = new Auth($pdo);

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">CRUD System</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 
                    (<?php echo ucfirst($_SESSION['role']); ?>)
                </span>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Dashboard</h2>
        <div class="row">
            <?php if ($auth->hasPermission('read_product')): ?>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Products</h5>
                        <p class="card-text">Manage product inventory</p>
                        <a href="products.php" class="btn btn-primary">View Products</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($auth->hasPermission('manage_categories')): ?>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Categories</h5>
                        <p class="card-text">Manage product categories</p>
                        <a href="categories.php" class="btn btn-success">Manage Categories</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($auth->hasPermission('manage_users')): ?>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Users</h5>
                        <p class="card-text">Manage user accounts</p>
                        <a href="users.php" class="btn btn-warning">Manage Users</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($auth->hasPermission('view_reports')): ?>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Reports</h5>
                        <p class="card-text">View system reports</p>
                        <a href="reports.php" class="btn btn-info">View Reports</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
```

### Task 3.2: Create Role-Based Product Management
Create `products.php`:

```php
<?php
session_start();
include 'config.php';
include 'auth.php';

$auth = new Auth($pdo);

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$auth->requirePermission('read_product');

// Handle product operations
if ($_POST && $auth->hasPermission('create_product')) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $stock = $_POST['stock'];
    
    $stmt = $pdo->prepare("INSERT INTO products (name, price, category_id, stock, created_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $price, $category_id, $stock, $_SESSION['user_id']]);
    $success = "Product added successfully!";
}

// Fetch products
$stmt = $pdo->query("
    SELECT p.*, c.name as category_name, u.username as created_by_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN users u ON p.created_by = u.id
    ORDER BY p.created_at DESC
");
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">CRUD System</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo ucfirst($_SESSION['role']); ?>)
                </span>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Products Management</h2>
            <?php if ($auth->hasPermission('create_product')): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                Add New Product
            </button>
            <?php endif; ?>
        </div>

        <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Category</th>
                                <th>Stock</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td>RM<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $product['stock'] > 10 ? 'success' : ($product['stock'] > 0 ? 'warning' : 'danger'); ?>">
                                        <?php echo $product['stock']; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($product['created_by_name']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($product['created_at'])); ?></td>
                                <td>
                                    <?php if ($auth->hasPermission('update_product')): ?>
                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <?php endif; ?>
                                    <?php if ($auth->hasPermission('delete_product')): ?>
                                    <a href="delete_product.php?id=<?php echo $product['id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Are you sure?')">Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <?php if ($auth->hasPermission('create_product')): ?>
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price (RM)</label>
                            <input type="number" name="price" step="0.01" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

## Exercise 4: Advanced Features

### Task 4.1: User Management System
Create `users.php` (only accessible by admins):

```php
<?php
session_start();
include 'config.php';
include 'auth.php';

$auth = new Auth($pdo);

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$auth->requirePermission('manage_users');

// Handle user operations
if ($_POST) {
    $action = $_POST['action'];
    $user_id = $_POST['user_id'];
    
    if ($action === 'update_role') {
        $role_id = $_POST['role_id'];
        $stmt = $pdo->prepare("UPDATE users SET role_id = ? WHERE id = ?");
        $stmt->execute([$role_id, $user_id]);
        $success = "User role updated successfully!";
    } elseif ($action === 'delete_user') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $success = "User deleted successfully!";
    }
}

// Fetch users with roles
$stmt = $pdo->query("
    SELECT u.*, r.name as role_name 
    FROM users u 
    JOIN roles r ON u.role_id = r.id 
    ORDER BY u.created_at DESC
");
$users = $stmt->fetchAll();

$roles = $pdo->query("SELECT * FROM roles")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">CRUD System</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo ucfirst($_SESSION['role']); ?>)
                </span>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>User Management</h2>

        <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="update_role">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <select name="role_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <?php foreach ($roles as $role): ?>
                                            <option value="<?php echo $role['id']; ?>" 
                                                    <?php echo $user['role_id'] == $role['id'] ? 'selected' : ''; ?>>
                                                <?php echo ucfirst($role['name']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                    <?php else: ?>
                                    <span class="text-muted">Current User</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

### Task 4.2: Category Management
Create `categories.php`:

```php
<?php
session_start();
include 'config.php';
include 'auth.php';

$auth = new Auth($pdo);

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$auth->requirePermission('manage_categories');

// Handle category operations
if ($_POST) {
    $action = $_POST['action'];
    
    if ($action === 'create') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        
        $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->execute([$name, $description]);
        $success = "Category created successfully!";
    } elseif ($action === 'update') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $description, $id]);
        $success = "Category updated successfully!";
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        
        // Check if category is being used by products
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
        $stmt->execute([$id]);
        $productCount = $stmt->fetchColumn();
        
        if ($productCount > 0) {
            $error = "Cannot delete category. It is being used by " . $productCount . " product(s).";
        } else {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Category deleted successfully!";
        }
    }
}

// Fetch categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Category Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">CRUD System</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo ucfirst($_SESSION['role']); ?>)
                </span>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Category Management</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                Add New Category
            </button>
        </div>

        <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo $category['id']; ?></td>
                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                <td><?php echo htmlspecialchars($category['description']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($category['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editCategoryModal"
                                            data-id="<?php echo $category['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                            data-description="<?php echo htmlspecialchars($category['description']); ?>">
                                        Edit
                                    </button>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle edit modal data
        document.getElementById('editCategoryModal').addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var name = button.getAttribute('data-name');
            var description = button.getAttribute('data-description');
            
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
        });
    </script>
</body>
</html>
```

## Exercise 5: Security Enhancements

### Task 5.1: Input Validation and Sanitization
Create `validation.php`:

```php
<?php
class Validation {
    public static function sanitizeInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    public static function validatePassword($password) {
        return strlen($password) >= 8 && preg_match('/[A-Za-z]/', $password) && preg_match('/[0-9]/', $password);
    }
    
    public static function validatePrice($price) {
        return is_numeric($price) && $price > 0;
    }
    
    public static function validateStock($stock) {
        return is_numeric($stock) && $stock >= 0;
    }
    
    public static function csrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function validateCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
?>
```

### Task 5.2: Rate Limiting
Create `rate_limit.php`:

```php
<?php
class RateLimit {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function checkRateLimit($action, $userId, $maxAttempts = 5, $timeWindow = 300) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM rate_limits 
            WHERE action = ? AND user_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)
        ");
        $stmt->execute([$action, $userId, $timeWindow]);
        $attempts = $stmt->fetchColumn();
        
        if ($attempts >= $maxAttempts) {
            return false;
        }
        
        // Log this attempt
        $stmt = $this->pdo->prepare("
            INSERT INTO rate_limits (action, user_id, ip_address, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$action, $userId, $_SERVER['REMOTE_ADDR']]);
        
        return true;
    }
}
?>
```

## Exercise 6: Testing and Challenges

### Challenge 1: Implement Audit Logging
Create a system that logs all CRUD operations with timestamps, user information, and action details.

### Challenge 2: Add Soft Delete
Implement soft delete functionality for products and categories instead of hard deletes.

### Challenge 3: Create API Endpoints
Build REST API endpoints for all CRUD operations with proper authentication and authorization.

### Challenge 4: Implement Search and Filtering
Add search functionality to the products page with filters for category, price range, and stock level.

### Challenge 5: Add File Upload
Allow users to upload product images with proper validation and storage.

## Exercise 7: Best Practices Checklist

### Security
- [ ] All user inputs are validated and sanitized
- [ ] SQL injection prevention using prepared statements
- [ ] XSS prevention with proper output escaping
- [ ] CSRF protection implemented
- [ ] Password hashing using `password_hash()`
- [ ] Session security (secure, httponly flags)
- [ ] Rate limiting implemented
- [ ] File upload validation

### Code Quality
- [ ] Code is well-documented
- [ ] Functions are properly organized
- [ ] Error handling is comprehensive
- [ ] Database connections are properly managed
- [ ] Code follows PSR standards

### User Experience
- [ ] Responsive design
- [ ] Clear error messages
- [ ] Loading states for async operations
- [ ] Confirmation dialogs for destructive actions
- [ ] Proper navigation and breadcrumbs

## Final Project: Complete E-commerce System

Build a complete e-commerce system with:
1. User registration and authentication
2. Role-based access control (Admin, Manager, Customer)
3. Product catalog with categories
4. Shopping cart functionality
5. Order management
6. User profile management
7. Admin dashboard with reports
8. Search and filtering
9. Image upload for products
10. Email notifications

## Resources and Further Learning

- [PHP Official Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Bootstrap Documentation](https://getbootstrap.com/docs/)
- [OWASP Security Guidelines](https://owasp.org/www-project-top-ten/)
- [PSR Standards](https://www.php-fig.org/psr/)

## Conclusion

This exercise provides a comprehensive foundation for building secure, role-based CRUD applications in PHP. The key takeaways include:

1. **Database Design**: Proper normalization and relationship management
2. **Security**: Input validation, output escaping, and access control
3. **Code Organization**: Separation of concerns and reusable components
4. **User Experience**: Intuitive interfaces and proper feedback
5. **Scalability**: Design patterns that support future growth

Continue practicing these concepts and gradually add more complex features to build your expertise in PHP web development.
