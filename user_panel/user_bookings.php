<?php
include('controller.php');
$db = connect();
session_start();
include 'is_admin.php';
// login check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include '../is_admin.php';
$user_id = intval($_SESSION['user_id']);

// fetch booking + names join
$sql = "
    SELECT 
        b.*, 
        v.vehicle_name, 
        h.name AS hotel_name, 
        g.name AS guide_name, 
        e.name AS event_name
    FROM booking b
    LEFT JOIN vehicle v ON b.vehicle_id = v.vehicle_id
    LEFT JOIN hotel h ON b.hotel_id = h.hotel_id
    LEFT JOIN guide g ON b.guide_id = g.guide_id
    LEFT JOIN event e ON b.event_id = e.event_id
    WHERE b.user_id = ?
    ORDER BY b.booking_date ASC
";

$stmt = $db->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Bookings | Vromon Sathi</title>
    <link href="../bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: #f8f9fa;
        font-family: 'Segoe UI', sans-serif;
    }

    .booking-card {
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        transition: 0.3s;
    }

    .booking-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .badge-type {
        background: #d1e7dd;
        color: #0f5132;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
    }

    .no-booking {
        text-align: center;
        margin-top: 60px;
    }

    .past-booking {
        background: #e9ecef;
    }
    </style>
</head>

<body>

    <div class="container py-5">
        <h3 class="text-center text-success mb-4">📅 My Bookings</h3>

        <?php if ($result->num_rows > 0): ?>
        <div class="row">
            <?php while ($b = $result->fetch_assoc()):
                    $bookingDate = strtotime($b['booking_date']);
                    $today = strtotime(date("Y-m-d"));
                    $isFuture = $bookingDate >= $today; // Future booking?
                    $cardClass = $isFuture ? '' : 'past-booking';
                ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card booking-card p-3 <?php echo $cardClass; ?>">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge-type"><?php echo htmlspecialchars(ucfirst($b['service_type'])); ?></span>
                        <small class="text-muted"><?php echo date("d M Y", strtotime($b['booked_at'])); ?></small>
                    </div>

                    <h5 class="mb-1 text-success">
                        Destination: <?php echo htmlspecialchars($b['destination']); ?>
                    </h5>
                    <p class="text-muted mb-2">📅 <?php echo htmlspecialchars($b['booking_date']); ?> | 🕒
                        <?php echo htmlspecialchars($b['booking_time']); ?></p>

                    <ul class="list-unstyled small text-muted mb-2">
                        <?php if ($b['vehicle_name']) echo "<li>🚗 Vehicle: {$b['vehicle_name']}</li>"; ?>
                        <?php if ($b['hotel_name']) echo "<li>🏨 Hotel: {$b['hotel_name']}</li>"; ?>
                        <?php if ($b['guide_name']) echo "<li>🧭 Guide: {$b['guide_name']}</li>"; ?>
                        <?php if ($b['event_name']) echo "<li>🎉 Event: {$b['event_name']}</li>"; ?>
                    </ul>

                    <p class="mb-1"><strong>📞 Contact:</strong> <?php echo htmlspecialchars($b['contact_number']); ?>
                    </p>

                    <!-- ✅ cancel button বা cancelled message -->
                    <?php if (isset($_SESSION['cancelled_booking'][$b['booking_id']])): ?>
                    <p class="text-danger fw-bold">❌ Cancelled</p>
                    <?php elseif ($isFuture): ?>
                    <form method="post" action="cancel_booking.php" class="mt-2">
                        <input type="hidden" name="booking_id" value="<?php echo $b['booking_id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm w-100">Cancel Booking</button>
                    </form>
                    <?php endif; ?>

                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="no-booking">
            <p class="text-success bg-light p-3 rounded">🌿 No bookings yet!</p>
        </div>
        <?php endif; ?>
    </div>

    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>