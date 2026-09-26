<?php
// view_bookings.php

// ডেটাবেস কনফিগারেশন ফাইল ইনক্লুড করুন
include('db_config.php');

// ডেটাবেসের সাথে সংযোগ স্থাপন করুন
$db = connect();

// বুকিং ডেটা ফেচ করার জন্য SQL কোয়েরি
// কলামের নামগুলো স্ক্রিনশট অনুযায়ী: contact_number এবং booked_at
$sql = "SELECT 
            booking_id, 
            user_id, 
            service_type, 
            vehicle_id, 
            hotel_id, 
            guide_id, 
            event_id, 
            destination, 
            contact_number,  /* পরিবর্তিত কলাম নাম */
            booked_at        /* পরিবর্তিত কলাম নাম */
        FROM booking 
        ORDER BY booking_id DESC";

$result = $db->query($sql);

if (!$result) {
    // ত্রুটি দেখালে এই মেসেজটি দেখাবে
    die("SQL Query Failed: " . $db->error); 
}

$bookings = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

// সংযোগ বন্ধ করুন
$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Records</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* স্টাইল আগের মতোই থাকবে, শুধু stats-grid এর CSS গুলো বাদ দেওয়া হয়েছে */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
        }
        .container {
            max-width: 1300px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }
        th {
            background-color: #0f4d2a;
            color: white;
            position: sticky;
            top: 0;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .action-btns a {
            margin-right: 5px;
            text-decoration: none;
            color: #0f4d2a;
            font-size: 1.1em;
            transition: color 0.3s;
        }
        .action-btns a:hover {
            color: #1a6d3f;
        }
        .delete-btn {
            color: #e74c3c !important;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #888;
        }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="fas fa-list-alt"></i> All Booking Records</h2>

    <?php if (!empty($bookings)): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Service Type</th>
                <th>Vehicle ID</th>
                <th>Hotel ID</th>
                <th>Guide ID</th>
                <th>Event ID</th>
                <th>Destination</th>
                <th>Contact Number</th>  <th>Booked At</th>        <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
            <tr>
                <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                <td><?php echo htmlspecialchars($booking['user_id']); ?></td>
                <td><?php echo htmlspecialchars($booking['service_type']); ?></td>
                <td><?php echo htmlspecialchars($booking['vehicle_id'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($booking['hotel_id'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($booking['guide_id'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($booking['event_id'] ?? 'N/A'); ?></td>
                
                <td><?php echo htmlspecialchars($booking['destination']); ?></td>
                <td><?php echo htmlspecialchars($booking['contact_number']); ?></td> <td><?php echo htmlspecialchars($booking['booked_at']); ?></td>      <td class="action-btns">
                    <a href="edit_booking.php?id=<?php echo $booking['booking_id']; ?>" title="Edit"><i class="fas fa-edit"></i></a>
                    <a href="delete_booking.php?id=<?php echo $booking['booking_id']; ?>" class="delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this booking?');"><i class="fas fa-trash-alt"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p class="no-data">No booking records found.</p>
    <?php endif; ?>
</div>

</body>
</html>