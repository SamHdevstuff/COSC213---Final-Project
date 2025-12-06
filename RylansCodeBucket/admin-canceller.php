<!-- schedule_stay.php -->

<?php
session_start();
require __DIR__ . '/db.php';
$pdo = get_pdo();
// || ($_SESSION["username"]) != "admin"
if (!isset($_SESSION['username']) || $_SESSION['username'] == 'guest') {
    header("Location: login.php");
    exit();
}
//sets basket to display all values if admin, else only the ones that match username and are more
//than 1 day from now.
if ($_SESSION['username'] != "admin") {
    $name = $_SESSION['username'];
//
    $request = "SELECT * FROM scheduler.CALENDAR_EVENTS_TEMP WHERE Name = ? AND CURRENT_TIMESTAMP()+1 <= timeIN ORDER BY id;";
    $valids = $pdo->prepare($request);
    $valids->execute([$name]);
    $basket = $valids;
    $error = "Note: You can only modify your own bookings up to one day beforehand!";
} else {
    $request = "SELECT * FROM scheduler.CALENDAR_EVENTS_TEMP ORDER BY id";
$basket = $pdo->query("SELECT * FROM scheduler.CALENDAR_EVENTS_TEMP ORDER BY id")->fetchAll();
$error = "";
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_SESSION['username'] == 'admin') {
        $erase = $_POST['eraseID'];

        $request = "DELETE from scheduler.calendar_events_temp WHERE ? = id;";
        $valids = $pdo->prepare($request);
        $valids->execute([$erase]);
        $basket = $pdo->query("SELECT * FROM scheduler.CALENDAR_EVENTS_TEMP ORDER BY id")->fetchAll();

    } else {
        $erase = $_POST['eraseID'];

        $request = "DELETE from scheduler.calendar_events_temp WHERE ? = id AND name = ? AND CURRENT_TIMESTAMP()+1 <= timeIN ORDER BY id;";
        $valids = $pdo->prepare($request);
        $valids->execute([$erase, $name]);
//
        $request = "SELECT * FROM scheduler.CALENDAR_EVENTS_TEMP WHERE Name = ? AND CURRENT_TIMESTAMP()+1 <= timeIN ORDER BY id;";
        $valids = $pdo->prepare($request);
        $valids->execute([$name]);
        $basket = $valids;
        $error = "Note: You can only modify your own bookings up to one day beforehand!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Master Calendar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Calendar</h2>
<?php if (!$basket): ?>
    <p>Your Calendar is empty.</p>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Price</th>
            <th>Check In</th>
            <th>Check Out</th>
            <th>Room</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($basket as $id => $row): ?>
            <tr>
                <td><?php echo (int)($row['id']); ?></td>
                <td><?php echo ($row['Name']); ?></td>
                <td>$<?php echo htmlspecialchars($row['price'], 2); ?></td>
                <td><?php echo ($row['timeIN']); ?></td>
                <td><?php echo ($row['timeOUT']); ?></td>
                <td><?php echo number_format($row['Room']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<h2>Cancel Bookings</h2>
<?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    Remove ID: <input type="number" name="eraseID" required><br><br>
    <button type="submit">Cancel</button>



</form>
<a href="view_calendar.php">Go to Public Calendar</a>
</body>
</html>