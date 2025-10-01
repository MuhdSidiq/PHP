<html>
    <head> 
        <title>Kelas Asas PHP</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="style.css">
    </head>
    <body class="bg-light">

        <?php 
        // Start session to use $_SESSION
        session_start();
        
        // // Set some example data for demonstration
        // $_SESSION['user_id'] = 123;
        // $_SESSION['username'] = 'john_doe';
        // $_SESSION['last_visit'] = date('Y-m-d H:i:s');
        
        // Set a cookie for demonstration
        setcookie('visit_count', '1', time() + 3600, '/');
        setcookie('user_preference', 'dark_mode', time() + (30 * 24 * 60 * 60), '/');
        ?>
        
        <div class="container mt-4">
            <h1 class="text-center mb-4">PHP Superglobals Demo</h1>
            
            <!-- $_GET Example -->
            <div class="row mb-4">
                <div class="col-12">
                    <h3>1. $_GET (URL Parameters)</h3>
                    <p>Try adding parameters to the URL: <code>index.php?name=John&age=25&city=Kuala Lumpur</code></p>
                    <div class="card">
                        <div class="card-body">
                            <h5>Current $_GET data:</h5>
                            <?php if (empty($_GET)): ?>
                                <p class="text-muted">No GET parameters found. Add ?name=John&age=25 to the URL to see data here.</p>
                            <?php else: ?>
                                <pre><?php print_r($_GET); ?></pre>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- $_COOKIE Example -->
            <div class="row mb-4">
                <div class="col-12">
                    <h3>2. $_COOKIE (Browser Cookies)</h3>
                    <div class="card">
                        <div class="card-body">
                            <h5>Current $_COOKIE data:</h5>
                            <?php if (empty($_COOKIE)): ?>
                                <p class="text-muted">No cookies found. Refresh the page to see the cookies we just set.</p>
                            <?php else: ?>
                                <pre><?php print_r($_COOKIE); ?></pre>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- $_SESSION Example -->
            <div class="row mb-4">
                <div class="col-12">
                    <h3>3. $_SESSION (Server-side Session Data)</h3>
                    <div class="card">
                        <div class="card-body">
                            <h5>Current $_SESSION data:</h5>
                            <pre><?php print_r($_SESSION); ?></pre>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- $_SERVER Example -->
            <div class="row mb-4">
                <div class="col-12">
                    <h3>4. $_SERVER (Server Information)</h3>
                    <div class="card">
                        <div class="card-body">
                            <h5>Important $_SERVER variables:</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group">
                                        <li class="list-group-item"><strong>Server Name:</strong> <?php echo $_SERVER['SERVER_NAME'] ?? 'Not set'; ?></li>
                                        <li class="list-group-item"><strong>Request Method:</strong> <?php echo $_SERVER['REQUEST_METHOD'] ?? 'Not set'; ?></li>
                                        <li class="list-group-item"><strong>Request URI:</strong> <?php echo $_SERVER['REQUEST_URI'] ?? 'Not set'; ?></li>
                                        <li class="list-group-item"><strong>HTTP Host:</strong> <?php echo $_SERVER['HTTP_HOST'] ?? 'Not set'; ?></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group">
                                        <li class="list-group-item"><strong>User Agent:</strong> <?php echo substr($_SERVER['HTTP_USER_AGENT'] ?? 'Not set', 0, 50) . '...'; ?></li>
                                        <li class="list-group-item"><strong>Remote IP:</strong> <?php echo $_SERVER['REMOTE_ADDR'] ?? 'Not set'; ?></li>
                                        <li class="list-group-item"><strong>HTTPS:</strong> <?php echo isset($_SERVER['HTTPS']) ? 'Yes' : 'No'; ?></li>
                                        <li class="list-group-item"><strong>Script Name:</strong> <?php echo $_SERVER['SCRIPT_NAME'] ?? 'Not set'; ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Practical Examples -->
            <div class="row mb-4">
                <div class="col-12">
                    <h3>5. Practical Usage Examples</h3>
                    <div class="card">
                        <div class="card-body">
                            <h5>How to use these superglobals:</h5>
                            
                            <h6>$_GET - URL Parameters:</h6>
                            <pre><code>// URL: index.php?name=John&age=25
$name = $_GET['name'] ?? 'Guest';
$age = $_GET['age'] ?? 0;
echo "Hello $name, you are $age years old";</code></pre>
                            
                            <h6>$_COOKIE - Browser Cookies:</h6>
                            <pre><code>// Set a cookie
setcookie('username', 'john', time() + 3600);

// Read a cookie
$username = $_COOKIE['username'] ?? 'Guest';
echo "Welcome back, $username";</code></pre>
                            
                            <h6>$_SESSION - Server-side Data:</h6>
                            <pre><code>// Start session
session_start();

// Store data
$_SESSION['user_id'] = 123;
$_SESSION['username'] = 'john';

// Read data
$userId = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? 'Guest';</code></pre>
                            
                            <h6>$_SERVER - Server Information:</h6>
                            <pre><code>// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission
}

// Get client IP
$ip = $_SERVER['REMOTE_ADDR'];

// Check if HTTPS
$isSecure = isset($_SERVER['HTTPS']);</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>    


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>