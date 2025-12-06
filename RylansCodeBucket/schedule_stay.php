<!-- schedule_stay.php -->

<?php
//Imported PHPMailer Classes
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
// Imported PHPMailer namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/* This is PHPMailer an external library found here https://github.com/PHPMailer/PHPMailer/releases
 PHP's mail() function does not work in our local environment. It requires a mail server and a "from" address which we do not have locally.
 PHPMailer lets us send a confirmation email to users after they book a room using an SMTP server

I also had to use a service called mailtrap to emulate an email. I could not get Gmail or any of my other emails to work and the instructions
I was following had a setup for it. */

session_start();
require __DIR__ . '/db.php';
$pdo = get_pdo();

// Redirect guests to login
if (!isset($_SESSION['username']) || ($_SESSION["username"]) == "guest") {
    header("Location: login.php");
    exit();
}
// Hourly rate for bookings
$hourlyRate = 10.08; // Cost per hour

$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Convert HTML5 datetime-local to MySQL DATETIME
    $checkin = str_replace('T', ' ', $_POST['checkin']) . ":00";
    $checkout = str_replace('T', ' ', $_POST['checkout']) . ":00";
    $room = $_POST['roomnum'];

    // Make sure checkout is after checkin
    if (strtotime($checkout) <= strtotime($checkin)) {
        echo "Your checkout time of $checkout is before your check-in time $checkin";
    } else {

        // Validate any overlapping reservation in this room
        $request = "SELECT * FROM scheduler.calendar_events_temp 
                    WHERE Room = ? 
                    AND timeIN < ? 
                    AND timeOUT > ?";
        $valids = $pdo->prepare($request);
        $valids->execute([$room, $checkout, $checkin]);
        $exists = $valids->rowCount();

        if ($exists > 0) {
            // Conflict found
            echo "Your reservation conflicts with $exists other reservation(s).";
        } else {
            // No conflicts — calculate price
            $bill = ((strtotime($checkout) - strtotime($checkin)) / (60 * 60)) * ($hourlyRate / 24); //Rate: 10.08 Per 24 Hours

            // Insert booking into calendar_events_temp
            $request = "INSERT INTO scheduler.calendar_events_temp (Name, price, Room, timeIN, timeOUT) 
                        VALUES (?, ?, ?, ?, ?)";
            $valids = $pdo->prepare($request);
            $valids->execute([$_SESSION['username'], $bill, $room, $checkin, $checkout]);

            // Fetch the user's email
            $request = "SELECT Email FROM scheduler.user_list WHERE USER_NAME = ?";
            $allez = $pdo->prepare($request);
            $allez->execute([$_SESSION['username']]);
            $to = $allez->fetchColumn();

            if ($to) {
                // Only send email if it exists
                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host       = 'sandbox.smtp.mailtrap.io';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = '3f52a6420715c9'; // Mailtrap username
                    $mail->Password   = 'c778bb7e7853cc';       // Mailtrap password
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;

                    $mail->setFrom('from@example.com', 'Booking Service');
                    $mail->addAddress($to, $_SESSION['username']);

                    $mail->isHTML(false);
                    $mail->Subject = "Is this your booking?";
                    $mail->Body    = "A booking was made for $checkin to $checkout by " . $_SESSION['username'] .
                            " for Room $room at [INSERT SERVICE NAME HERE GUYS]. If this isn't you, contact us to cancel it ASAP.";

                    $mail->send();
                    echo "<p style='color:green;'>Booking successful! Confirmation email sent.</p>";
                } catch (Exception $e) {
                    echo "Booking saved, but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }

            echo "<p style='color:green;'>Booking successful!</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Schedule Stay</title>
    <script>
        // Room names
        const roomNames = {
            1: "Ocean View Room",
            2: "Garden View Room",
            3: "Mountain View Room",
            4: "Sunset View Room",
            5: "City View Room"
        };

        const hourlyRate = 10.08;

        // Function to update room name display
        function updateRoomInfo() {
            const checkin = document.querySelector('input[name="checkin"]').value;
            const checkout = document.querySelector('input[name="checkout"]').value;
            const room = parseInt(document.querySelector('input[name="roomnum"]').value);
            const roomDisplay = document.getElementById('roomDisplay');

            if (checkin && checkout && roomNames[room]) {
                roomDisplay.textContent = "Selected Room: " + roomNames[room] + " | Hourly Rate: $" + hourlyRate;
            } else {
                roomDisplay.textContent = "";
            }
        }

        // Function to calculate and display price
        function updatePrice() {
            const checkinInput = document.querySelector('input[name="checkin"]');
            const checkoutInput = document.querySelector('input[name="checkout"]');
            const priceDisplay = document.getElementById('priceDisplay');

            const checkin = new Date(checkinInput.value);
            const checkout = new Date(checkoutInput.value);

            if (!checkinInput.value || !checkoutInput.value) {
                priceDisplay.textContent = "";
                return;
            }

            if (checkout <= checkin) {
                priceDisplay.textContent = "Checkout must be after check-in!";
                return;
            }

            // Rate: 10.08 per 24 hours
            const hoursDiff = (checkout - checkin) / (1000 * 60 * 60);
            const price = (hoursDiff * hourlyRate) / 24;

            priceDisplay.textContent = "You will be charged: $" + price.toFixed(2);
        }
    </script>
</head>
<body>
<h2>Schedule a Stay</h2>
<?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    Check-In Time: <input type="datetime-local" name="checkin" required onchange="updatePrice(); updateRoomInfo();"><br><br>
    Check-Out Time: <input type="datetime-local" name="checkout" required onchange="updatePrice(); updateRoomInfo();"><br><br>
    Room (Between 1 and 5):
    <input type="number" name="roomnum" min="1" max="5" required onchange="updatePrice(); updateRoomInfo();"><br><br>

    <!-- Display selected room name -->
    <p id="roomDisplay" style="font-weight:bold;"></p>

    <!-- Display estimated price -->
    <p id="priceDisplay" style="font-weight:bold;"></p>

    <button type="submit">Validate</button>
</form>
<a href="view_calendar.php">Go to Calendar</a>
</body>
</html>