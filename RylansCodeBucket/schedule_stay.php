<!-- schedule_stay.php -->

<?php
session_start();
require __DIR__ . '/db.php';
$pdo = get_pdo();
if (!isset($_SESSION['username']) || ($_SESSION["username"]) == "guest") {
    header("Location: login.php");
    exit();
}

$error = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $room = $_POST['roomnum'];

    $sum = 0;
    //validate check-in time with room
    $request = "SELECT * from scheduler.calendar_events_temp WHERE ? = Room AND timeIN <= ? AND timeOUT >= ?;";
    $valids = $pdo->prepare($request);
    $valids->execute([$room, $checkin, $checkin]);
    $exists = $valids->rowCount();
    if($exists > 0)
    echo "Your reservation's start time conflicts with " . $exists . " other reservation(s).";
    $sum += $exists;
    //validate check-out time with room
    $exists = 0;
    $request = "SELECT * from scheduler.calendar_events_temp WHERE ? = Room AND timeIN <= ? AND timeOUT >= ?;";
    $valids = $pdo->prepare($request);
    $valids->execute([$room, $checkout, $checkout]);
    $exists = $valids->rowCount();
    $sum += $exists;
    if($exists > 0)
       echo "Your reservation's end time conflicts with " . $exists . " other reservation(s).";


    //yipee!

    //echo htmlspecialchars($_SESSION['username']);
    //check that name exists.
    if($sum < 1){
        $request = "SELECT * from scheduler.user_list WHERE User_Name = ?;";
        $valids = $pdo->prepare($request);
        $valids->execute([$_SESSION['username']]);
        $exists = $valids->rowCount();
        //echo htmlspecialchars($exists);
        //check to make sure timeOUT is after timeIN
        if(strtotime($checkout) - strtotime($checkin) < 0) {
                 echo "Your checkout time of " . $checkout . " is before your check-in time ".  $checkin;
        } else {
            //calculate price
            $bill = ((strtotime($checkout) - strtotime($checkin)) / (60 * 60)) * (10.08/24) ; //Rate: 10.08 Per 24 Hours
            //echo $bill;

            $request = "INSERT INTO scheduler.calendar_events_temp VALUES (DEFAULT, ?, ?, ?, ?, ?);";
            $valids = $pdo->prepare($request);
            $valids->execute([$_SESSION['username'], $bill, $room, $checkin, $checkout]);
        }
        //send le confirmation email
        //ini_set();
        //Set the hostname of the mail server
        ini_set('SMTP','smtp.gmail.com');
        ini_set('smtp_port', '587');
        ini_set('sendmail_from','info@gmail.com');

        $request = "SELECT * from scheduler.user_list WHERE USER_NAME = ?;";
        $allez = $pdo->prepare($request);
        $allez->execute([$_SESSION['username']]);
        $to = $allez->fetchColumn(2); //gets the email
        if($to == null) {
            $to = "rkbrennan369@gmail.com";
        }
        $subject = "Is this your booking?";
        $txt = "A booking was made for " . $checkin . "to ". $checkout . " by " . $_SESSION['username'].
                " for Room " . $room . " at [INSERT SERVICE NAME HERE GUYS]. If this isn't you, contact 
                us to cancel it ASAP.";
        $txt = wordwrap($txt, 70);
       mail($to,$subject,$txt);

    }

}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Schedule Stay</title>
</head>
<body>
<h2>Schedule a Stay</h2>
<?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    Check-In Time: <input type="datetime-local" name="checkin" required><br><br>
    Check-Out Time: <input type="datetime-local" name="checkout" required><br><br>
    Room (Between 1 and 5): <input type="number" name="roomnum" min = "1" max="5"><br><br>
    <button type="submit">Validate</button>



</form>
<a href="view_calendar.php">Go to Calendar</a>
</body>
</html>