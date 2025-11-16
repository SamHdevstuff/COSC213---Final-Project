<!-- createuser.php -->

<?php
session_start();
require __DIR__ . '/db.php';
$pdo = get_pdo();

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    //Does this username already exist?
    $request = "SELECT User_name, Password from scheduler.user_list WHERE User_name = ?;";
    $valids = $pdo->prepare($request);
    $valids->execute([$username]);
    $exists = $valids->rowCount();
    if($exists > 0){
        //whoopsie!
        $error = "A user with that name already exists! User: " . $valids->fetchColumn(0);
    } else {
        //else, generate a new user!
        $request = "INSERT INTO scheduler.user_list (User_name, Password, Email) VALUES (?, ?, ?);";
        $valids = $pdo->prepare($request);
        $valids->execute([$username, $password, $email]);
        //echo "New record created successfully: " . $valids->fetchColumn(0) . " " . $valids->fetchColumn(1) . " " . $valids->fetchColumn(2);
        $_SESSION['username'] = $username;
        header("Location: view_calendar.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create User</title>
</head>
<body>
<h2>Create User</h2>
<?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    Username: <input type="text" name="username" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    Email (Optional): <input type="email" name="email"><br><br>
    <button type="submit">Go</button>



</form>
<a href="view_calendar.php">Go to Calendar as Guest</a>
</body>
</html>