<!-- login.php -->

<?php
session_start();
require __DIR__ . '/db.php';
$pdo = get_pdo();

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch the hashed password for this user
    $request = "SELECT Password FROM scheduler.user_list WHERE User_name = ?";
    $stmt = $pdo->prepare($request);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['Password'])) {
        // Password is correct
        $_SESSION['username'] = $username;
        header("Location: view_calendar.php");
        exit();
    } else {
        // Invalid credentials
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
<h2>Login</h2>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    Username: <input type="text" name="username" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit">Login</button>
</form>
<a href="view_calendar.php">Go to Calendar as Guest</a>
</body>
</html>
