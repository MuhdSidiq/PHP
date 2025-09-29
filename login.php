<?php
session_start();
include 'config.php';

$error = '';
$success = '';

if ($_POST) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['logged_in'] = true;

                $success = "Login successful! Welcome, " . htmlspecialchars($user['username']);
            } else {
                $error = "Invalid username or password";
            }
        } catch (PDOException $e) {
            $error = "Database error occurred";
        }
    } else {
        $error = "Please enter both username and password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>

    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
        <p><a href="view.php">Go to Products</a></p>
        <p><a href="logout.php">Logout</a></p>
    <?php else: ?>
        <form method="POST">
            <div>
                <label>Username:</label><br>
                <input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
            </div><br>

            <div>
                <label>Password:</label><br>
                <input type="password" name="password" required>
            </div><br>

            <button type="submit">Login</button>
        </form>

        <p><a href="register.php">Don't have an account? Register here</a></p>
    <?php endif; ?>
</body>
</html>