<!-- login.php -->

<?php
session_start();
require __DIR__ . '/db.php';
$pdo = get_pdo();
// credentials

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $request = "SELECT User_name, Password from scheduler.user_list WHERE User_name = ? AND Password = ?;";
    $valids = $pdo->prepare($request);
    $valids->execute([$username, $password]);
    //If the line exists (I.E. username and password match) There should be one (or more :( ) lines in rowcount
    $exists = $valids->rowCount();
    if ($exists > 0) {
        $_SESSION['username'] = $username; // Store username in session
        header("Location: view_calendar.php");
       exit();
    } else {
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
<?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    Username: <input type="text" name="username" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit">Login</button>



</form>
<a href="view_calendar.php">Go to Calendar as Guest</a>
</body>
</html>