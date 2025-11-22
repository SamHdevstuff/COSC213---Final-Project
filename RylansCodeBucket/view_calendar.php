<?php
session_start();
require __DIR__ . '/db.php';
if (!isset($_SESSION['username'])) {
    //header("Location: login.php");
    //exit();
    $_SESSION['username'] = 'guest';
}
$pdo = get_pdo();
$basket = $pdo->query("SELECT * FROM scheduler.CALENDAR_EVENTS_TEMP ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>The Calendar</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f9f9f9; }
        .actions { white-space: nowrap; }
        input[type=number] { width: 80px; }
    </style>
</head>
<body>

<h1>This Week's Schedule:</h1>

<?php if (!$basket): ?>
    <p>Your Calendar is empty.</p>
<?php else:
    $weekrep = $pdo->query("SELECT Room, timeIN, timeOUT, HOUR(timeIN) AS INHOUR, MINUTE(timeIN) AS INMIN, HOUR(timeOUT) AS OUTHOUR, MINUTE(timeOUT) AS OUTMIN FROM scheduler.CALENDAR_EVENTS_TEMP WHERE WEEK(CURRENT_DATE(), 1)=WEEK(timeIN, 1) OR WEEK(CURRENT_DATE(), 1)=WEEK(timeOUT, 1) ORDER BY timeIN;")->fetchAll();
    $thisweek = $pdo->query("SELECT WEEK(NOW(), 1) AS cw FROM scheduler.CALENDAR_EVENTS_TEMP;")->fetchAll();
?>
    <table>
        <thead>
        <tr>
            <th>Monday</th>
            <th>Tuesday</th>
            <th>Wednesday</th>
            <th>Thursday</th>
            <th>Friday</th>
            <th>Saturday</th>
            <th>Sunday</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($weekrep as $id => $row): ?>
            <tr>
                <td><?php
                    if(date('w', strtotime($row['timeIN'])) == '1' && date('W', strtotime($row['timeIN'])) == date('W')) {
                        echo ($row['INHOUR']) . ":" . ($row['INMIN']) . " check in (" . $row['Room']. ")" . "<br>";
                    }
                    if (date('w', strtotime($row['timeOUT'])) == '1' && date('W', strtotime($row['timeOUT'])) == date('W')) {
                        echo ($row['OUTHOUR']) . ":" . ($row['OUTMIN']) . " check out (" . $row['Room'] . ")" . "<br>";
                    }
                    ?> </td>
                <td><?php
                    if(date('w', strtotime($row['timeIN'])) == '2' && date('W', strtotime($row['timeIN'])) == date('W')) {
                        echo ($row['INHOUR']) . ":" . ($row['INMIN']) . " check in (" . $row['Room']. ")" . "<br>";
                    }
                    if (date('w', strtotime($row['timeOUT'])) == '2' && date('W', strtotime($row['timeOUT'])) == date('W')) {
                        echo ($row['OUTHOUR']) . ":" . ($row['OUTMIN']) . " check out (" . $row['Room'] . ")" . "<br>";
                    }
                    ?> </td>
                <td><?php
                    if(date('w', strtotime($row['timeIN'])) == '3' && date('W', strtotime($row['timeIN'])) == date('W')) {
                        echo ($row['INHOUR']) . ":" . ($row['INMIN']) . " check in (" . $row['Room']. ")" . "<br>";
                    }
                    if (date('w', strtotime($row['timeOUT'])) == '3' && date('W', strtotime($row['timeOUT'])) == date('W')) {
                        echo ($row['OUTHOUR']) . ":" . ($row['OUTMIN']) . " check out (" . $row['Room'] . ")" . "<br>";
                    }
                    ?> </td>
                <td><?php
                    if(date('w', strtotime($row['timeIN'])) == '4' && date('W', strtotime($row['timeIN'])) == date('W')) {
                        echo ($row['INHOUR']) . ":" . ($row['INMIN']) . " check in (" . $row['Room']. ")" . "<br>";
                    }
                    if (date('w', strtotime($row['timeOUT'])) == '4' && date('W', strtotime($row['timeOUT'])) == date('W')) {
                        echo ($row['OUTHOUR']) . ":" . ($row['OUTMIN']) . " check out (" . $row['Room'] . ")" . "<br>";
                    }
                    ?> </td>
                <td><?php
                    if(date('w', strtotime($row['timeIN'])) == '5' && date('W', strtotime($row['timeIN'])) == date('W')) {
                        echo ($row['INHOUR']) . ":" . ($row['INMIN']) . " check in (" . $row['Room']. ")" . "<br>";
                    }
                    if (date('w', strtotime($row['timeOUT'])) == '5' && date('W', strtotime($row['timeOUT'])) == date('W')) {
                        echo ($row['OUTHOUR']) . ":" . ($row['OUTMIN']) . " check out (" . $row['Room'] . ")" . "<br>";
                    }
                    ?> </td>
                <td><?php
                    if(date('w', strtotime($row['timeIN'])) == '6' && date('W', strtotime($row['timeIN'])) == date('W')) {
                        echo ($row['INHOUR']) . ":" . ($row['INMIN']) . " check in (" . $row['Room']. ")" . "<br>";
                    }
                    if (date('w', strtotime($row['timeOUT'])) == '6' && date('W', strtotime($row['timeOUT'])) == date('W')) {
                        echo ($row['OUTHOUR']) . ":" . ($row['OUTMIN']) . " check out (" . $row['Room'] . ")" . "<br>";
                    }
                    ?> </td>
                <td><?php
                    if(date('w', strtotime($row['timeIN'])) == '0' && date('W', strtotime($row['timeIN'])) == date('W')) {
                        echo ($row['INHOUR']) . ":" . ($row['INMIN']) . " check in (" . $row['Room']. ")" . "<br>";
                    }
                    if (date('w', strtotime($row['timeOUT'])) == '0' && date('W', strtotime($row['timeOUT'])) == date('W')) {
                        echo ($row['OUTHOUR']) . ":" . ($row['OUTMIN']) . " check out (" . $row['Room'] . ")" . "<br>";
                    }
                    ?> </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<!--Anotha one -->
<?php
    $weekrep = $pdo->query("SELECT Room, timeIN, timeOUT, HOUR(timeIN) AS INHOUR, MINUTE(timeIN) AS INMIN, HOUR(timeOUT) AS OUTHOUR, MINUTE(timeOUT) AS OUTMIN FROM scheduler.CALENDAR_EVENTS_TEMP WHERE WEEK(CURRENT_DATE(), 1)=WEEK(timeIN, 1)-1 OR WEEK(CURRENT_DATE(), 1)=WEEK(timeOUT, 1)-1 ORDER BY timeIN;")->fetchAll();
    $thisweek = $pdo->query("SELECT WEEK(NOW(), 1) AS cw FROM scheduler.CALENDAR_EVENTS_TEMP;")->fetchAll();
    ?>
    <table>
        <thead>
        <tr>
            <th>Monday</th>
            <th>Tuesday</th>
            <th>Wednesday</th>
            <th>Thursday</th>
            <th>Friday</th>
            <th>Saturday</th>
            <th>Sunday</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($weekrep as $id => $row): ?>
            <tr>
                <td><?php
                    if(date('w', strtotime($row['timeIN'])) == '1' && date('W', strtotime($row['timeIN'])) == date('W')+1) {
                        echo ($row['INHOUR']) . ":" . ($row['INMIN']) . " check in (" . $row['Room']. ")" . "<br>";
                    }
                    if (date('w', strtotime($row['timeOUT'])) == '1' && date('W', strtotime($row['timeOUT'])) == date('W')+1) {
                        echo ($row['OUTHOUR']) . ":" . ($row['OUTMIN']) . " check out (" . $row['Room'] . ")" . "<br>";
                    }
                    ?> </td>
                <td><?php
                    if(date('w', strtotime($row['timeIN'])) == '2' && date('W', strtotime($row['timeIN'])) == date('W')+1) {
                        echo ($row['INHOUR']) . ":" . ($row['INMIN']) . " check in (" . $row['Room']. ")" . "<br>";
                    }
                    if (date('w', strtotime($row['timeOUT'])) == '2' && date('W', strtotime($row['timeOUT'])) == date('W')+1) {
                        echo ($row['OUTHOUR']) . ":" . ($row['OUTMIN']) . " check out (" . $row['Room'] . ")" . "<br>";
                    }
                    ?> </td>
                <td><?php
                    if(date('w', strtotime($row['timeIN'])) == '3' && date('W', strtotime($row['timeIN'])) == date('W')+1) {
                        echo ($row['INHOUR']) . ":" . ($row['INMIN']) . " check in (" . $row['Room']. ")" . "<br>";
                    }
                    if (date('w', strtotime($row['timeOUT'])) == '3' && date('W', strtotime($row['timeOUT'])) == date('W')+1) {
                        echo ($row['OUTHOUR']) . ":" . ($row['OUTMIN']) . " check out (" . $row['Room'] . ")" . "<br>";
                    }
                    ?> </td>
                <td><?php
                    if(date('w', strtotime($row['timeIN'])) == '4' && date('W', strtotime($row['timeIN'])) == date('W')+1) {
                        echo ($row['INHOUR']) . ":" . ($row['INMIN']) . " check in (" . $row['Room']. ")" . "<br>";
                    }
                    if (date('w', strtotime($row['timeOUT'])) == '4' && date('W', strtotime($row['timeOUT'])) == date('W')+1) {
                        echo ($row['OUTHOUR']) . ":" . ($row['OUTMIN']) . " check out (" . $row['Room'] . ")" . "<br>";
                    }
                    ?> </td>
                <td><?php
                    if(date('w', strtotime($row['timeIN'])) == '5' && date('W', strtotime($row['timeIN'])) == date('W')+1) {
                        echo ($row['INHOUR']) . ":" . ($row['INMIN']) . " check in (" . $row['Room']. ")" . "<br>";
                    }
                    if (date('w', strtotime($row['timeOUT'])) == '5' && date('W', strtotime($row['timeOUT'])) == date('W')+1) {
                        echo ($row['OUTHOUR']) . ":" . ($row['OUTMIN']) . " check out (" . $row['Room'] . ")" . "<br>";
                    }
                    ?> </td>
                <td><?php
                    if(date('w', strtotime($row['timeIN'])) == '6' && date('W', strtotime($row['timeIN'])) == date('W')+1) {
                        echo ($row['INHOUR']) . ":" . ($row['INMIN']) . " check in (" . $row['Room']. ")" . "<br>";
                    }
                    if (date('w', strtotime($row['timeOUT'])) == '6' && date('W', strtotime($row['timeOUT'])) == date('W')+1) {
                        echo ($row['OUTHOUR']) . ":" . ($row['OUTMIN']) . " check out (" . $row['Room'] . ")" . "<br>";
                    }
                    ?> </td>
                <td><?php
                    if(date('w', strtotime($row['timeIN'])) == '0' && date('W', strtotime($row['timeIN'])) == date('W')+1) {
                        echo ($row['INHOUR']) . ":" . ($row['INMIN']) . " check in (" . $row['Room']. ")" . "<br>";
                    }
                    if (date('w', strtotime($row['timeOUT'])) == '0' && date('W', strtotime($row['timeOUT'])) == date('W')+1) {
                        echo ($row['OUTHOUR']) . ":" . ($row['OUTMIN']) . " check out (" . $row['Room'] . ")" . "<br>";
                    }
                    ?> </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>



<?php endif; ?>





<h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
<a href="login.php">| Login |</a>
<a href="logout.php">| Logout |</a>
<a href="createuser.php">| Create User |</a>
<a href="schedule_stay.php">| Schedule Stay |</a>
<a href="admin-canceller.php">| (Admin Only) Cancel Appointments |</a>
</body>
</html>