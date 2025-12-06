<?php
session_start();
require __DIR__ . '/db.php';
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = 'guest';
}

$pdo = get_pdo();
$basket = $pdo->query("SELECT * FROM scheduler.CALENDAR_EVENTS_TEMP ORDER BY id")->fetchAll();
// Map room numbers to names
$roomNames = [
        1 => "Ocean View Room",
        2 => "Garden View Room",
        3 => "Mountain View Room",
        4 => "Sunset View Room",
        5 => "City View Room"
];
// Helper function to get the start of the week (Monday)
function get_monday($date) {
    return date('Y-m-d', strtotime('monday this week', strtotime($date)));
}

// Get this week's Monday
$this_monday = get_monday(date('Y-m-d'));

// Generate array of dates for this week
$this_week_dates = [];
for ($i = 0; $i < 7; $i++) {
    $this_week_dates[] = date('Y-m-d', strtotime("$this_monday +$i days"));
}

// Next week's Monday
$next_monday = date('Y-m-d', strtotime("$this_monday +7 days"));

// Array of dates for next week
$next_week_dates = [];
for ($i = 0; $i < 7; $i++) {
    $next_week_dates[] = date('Y-m-d', strtotime("$next_monday +$i days"));
}

// Fetch events for this week
$weekrep = $pdo->query("
    SELECT Name, Room, timeIN, timeOUT, HOUR(timeIN) AS INHOUR, MINUTE(timeIN) AS INMIN,
           HOUR(timeOUT) AS OUTHOUR, MINUTE(timeOUT) AS OUTMIN
    FROM scheduler.CALENDAR_EVENTS_TEMP
    WHERE DATE(timeIN) BETWEEN '$this_week_dates[0]' AND '$this_week_dates[6]'
       OR DATE(timeOUT) BETWEEN '$this_week_dates[0]' AND '$this_week_dates[6]'
    ORDER BY timeIN;
")->fetchAll();

// Fetch events for next week
$nextweekrep = $pdo->query("
    SELECT Name, Room, timeIN, timeOUT, HOUR(timeIN) AS INHOUR, MINUTE(timeIN) AS INMIN,
           HOUR(timeOUT) AS OUTHOUR, MINUTE(timeOUT) AS OUTMIN
    FROM scheduler.CALENDAR_EVENTS_TEMP
    WHERE DATE(timeIN) BETWEEN '$next_week_dates[0]' AND '$next_week_dates[6]'
       OR DATE(timeOUT) BETWEEN '$next_week_dates[0]' AND '$next_week_dates[6]'
    ORDER BY timeIN;
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>The Calendar</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }
        th {
            background: #f9f9f9;
            padding: 4px;
            font-weight: bold;
            height: 30px;
        }
        td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
            height: 80px;
        }
    </style>


</head>
<body>
<h1>Welcome to [INSERT SERVICE NAME HERE]</h1>
<p>We have several different luxurious rooms for you to choose from with different views. (1) The Mountain View Room, (2) The Garden View Room, (3) The Lake View Room, (4) The Sunset View Room, (5) The City View Room</p>
<h2>Schedule for the next two weeks:</h2>

<?php if (!$basket): ?>
    <p>Your Calendar is empty.</p>
<?php else: ?>

    <table>
        <thead>
        <tr>
            <?php foreach ($this_week_dates as $date): ?>
                <th><?php echo date('D m/d', strtotime($date)); ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <tr>
            <?php foreach ($this_week_dates as $date): ?>
                <td>
                    <?php foreach ($weekrep as $row):
                        if (date('Y-m-d', strtotime($row['timeIN'])) == $date) {
                            printf("%s check in %s (%s) at %02d:%02d<br>",
                                    htmlspecialchars($row['Name']),
                                    $roomNames[$row['Room']],
                                    $row['Room'],
                                    $row['INHOUR'],
                                    $row['INMIN']);
                        }
                        if (date('Y-m-d', strtotime($row['timeOUT'])) == $date) {
                            printf("%s check out %s (%s) at %02d:%02d<br>",
                                    htmlspecialchars($row['Name']),
                                    $roomNames[$row['Room']],
                                    $row['Room'],
                                    $row['OUTHOUR'],
                                    $row['OUTMIN']);
                        }
                    endforeach; ?>
                </td>
            <?php endforeach; ?>
        </tr>
    </table>

    <!-- Next week -->
    <table>
        <thead>
        <tr>
            <?php foreach ($next_week_dates as $date): ?>
                <th><?php echo date('D m/d', strtotime($date)); ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <tr>
            <?php foreach ($next_week_dates as $date): ?>
                <td>
                    <?php foreach ($nextweekrep as $row):
                        if (date('Y-m-d', strtotime($row['timeIN'])) == $date) {
                            printf("%s check in %s (%s) at %02d:%02d<br>",
                                    htmlspecialchars($row['Name']),
                                    $roomNames[$row['Room']],
                                    $row['Room'],
                                    $row['INHOUR'],
                                    $row['INMIN']);
                        }
                        if (date('Y-m-d', strtotime($row['timeOUT'])) == $date) {
                            printf("%s check out %s (%s) at %02d:%02d<br>",
                                    htmlspecialchars($row['Name']),
                                    $roomNames[$row['Room']],
                                    $row['Room'],
                                    $row['OUTHOUR'],
                                    $row['OUTMIN']);
                        }
                    endforeach; ?>
                </td>
            <?php endforeach; ?>
        </tr>
    </table>

<?php endif; ?>

<h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
<a href="login.php">| Login |</a>
<a href="logout.php">| Logout |</a>
<a href="createuser.php">| Create User |</a>
<a href="schedule_stay.php">| Schedule Stay |</a>
<a href="admin-canceller.php">| Cancel Bookings |</a>
</body>
</html>
